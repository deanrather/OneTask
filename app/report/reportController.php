<?php
class reportController extends controller
{
	function indexView()
	{		
		$this->setTitle('OneTask - Report New Task');
		$this->addJS('scripts');
		$this->view->topBar = $this->core->app->topBar();
		$userTable = $this->newTable('user');
		$taskTable = $this->newTable('task');
		
		$userID		= $_SESSION['user_id'];
		$userName	= $_SESSION['user_name'];
		$userType	= $_SESSION['user_type'];
		$userProject= $_SESSION['user_project'];
		
		$DATEFORMAT='d/m/y H:i:s';
		
		$prefs = $userTable->query("SELECT `user_default_developer`, `user_default_type` FROM `ot_user` WHERE `user_id` = $userID;");
		$dev			= $prefs[0]['user_default_developer'];
		$type			= $prefs[0]['user_default_type'];
		$estDevHours	= '00';
		$estDevMinutes	= 30;
		$title			= '';
		$desc			= '';
		$successors		= array();
		$predecessors	= array();
		
		$taskTypes = $taskTable->query('SELECT `task_type_id`, `task_type_name` FROM `ot_task_type` WHERE `task_type_status` = 1;');
		$users = $userTable->query('SELECT `user_id`, `user_name` FROM `ot_user` WHERE `user_type` = 2 AND `user_status` = 1;');
		$tasks = $taskTable->query("SELECT `task_name`, `task_id`, `task_description` FROM `ot_task` WHERE `task_status` < 4 AND `task_project` = '$userProject'");

		if(isset_true($_POST['add_task']))
		{
			$dev			= $_POST['developer'];
			$type			= $_POST['type'];
			$estDevHours	= $_POST['estimated_dev_hours'];
			$estDevMinutes	= $_POST['estimated_dev_minutes'];
			$estDevTime		= $estDevMinutes + (int)($estDevHours*60);
			$title			= addslashes($_POST['title']);
			$desc			= addslashes($_POST['description']);
			$successors		= isset_val($_POST['successors']);
			$predecessors	= isset_val($_POST['predecessors']);
			$status			= $dev? 2:1;
			if(!$successors) $successors = array();
			if(!$predecessors) $predecessors = array();
			
			if(!$title)
			{
				$this->setError('Task must have a title.');
			}else{
				if(!is_numeric($estDevTime))
				{
					$this->setError('Estimated Development Time must be a number');
				}else{
					// Check that they didn't say something must be done before <AND> after some other task
					$good=true;
					foreach($successors as $successor)
					{
						if(in_array($successor, $predecessors))
						{
							$good=false;
							$name=query("SELECT `task_name` FROM `ot_task` WHERE `task_id` = '$successor';");
							$name=$name[0]['task_name'];
							$this->setError("Cannot need to be completed before <b>and</b> after <i>$name</i>.");
						}
					}
					reset($successors);
					
					if($good)
					{
						$userTable->update("UPDATE `ot_user` SET `user_default_developer` = '$dev' WHERE `user_id` = $userID;");
						$userTable->update("UPDATE `ot_user` SET `user_default_type` = '$type' WHERE `user_id` = $userID;");
						$time=time();
						$query=<<<SQL
INSERT INTO `ot_task`
(
	`task_name`,
	`task_description`,
	`task_type`,
	`task_project`,
	`task_reporter`,
	`task_developer`,
	`task_estimated_dev_time`,
	`task_create_time`,
	`task_update_time`,
	`task_status`
)VALUES(
'$title', '$desc', '$type', '$userProject', '$userID', '$dev', '$estDevTime', '$time', '$time', '$status');
SQL;
						if($taskTable->update($query))
						{
							$taskID=$taskTable->query("SELECT `task_id` FROM `ot_task` WHERE `task_name` = '$title' AND `task_create_time` = '$time' ORDER BY `task_id` DESC;");
							$taskID=$taskID[0]['task_id'];
							foreach($successors as $successor)
							{
								if(!$taskTable->update("INSERT INTO `ot_task_dependancy` (`task_dependancy_task`, `task_dependancy_dependancy`)VALUES('$successor', '$taskID');"))
								{
									$good=false;
									$this->setError('Error Inserting successor');
								}
							}
							
							foreach($predecessors as $predecessor)
							{
								if(!$taskTable->update("INSERT INTO `ot_task_dependancy` (`task_dependancy_task`, `task_dependancy_dependancy`)VALUES('$taskID', '$predecessor');"))
								{
									$good=false;
									$this->setError('Error Inserting predecessor');
								}
							}
							
							if($good)
							{
								$title = stripslashes($title);
								$this->setNote("<a href=\"task/$taskID\">[$taskID]: <i>$title</i> Submitted</a>");
								$this->redirect();
							}
						}else{
							$this->setError('Error Inserting Task.');
						}
					}
				}
			}
		}
		
		$this->view->title			= $title;
		$this->view->desc			= $desc;
		$this->view->type			= $type;
		$this->view->taskTypes		= $taskTypes;
		$this->view->users			= $users;
		$this->view->dev			= $dev;
		$this->view->estDevHours	= $estDevHours;
		$this->view->estDevMinutes	= $estDevMinutes;
		$this->view->predecessors	= $predecessors;
		$this->view->tasks			= $tasks;
		$this->view->successors 	= $successors;
	}
}
?>