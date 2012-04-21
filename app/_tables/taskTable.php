<?php
	class taskTable extends table
	{
		var $table = 'ot_task';
		var $key = 'task_id';
		
		function handleChangeStatus($data)
		{
			if(isset_true($data['delete']))
			{
				if($_SESSION['user_type']==3) $this->deleteTask($data['taskID']);
				$this->controller->setNote('Task Deleted.');
				$this->controller->redirect('task/list');
			}
			elseif(isset_true($data['assignTo']))
			{
				$taskID = $data['taskID'];
				$assignTo = $data['assignTo'];
				$this->setTaskStatus($taskID, 2);
				$this->update("UPDATE `ot_task` SET `task_developer` = '$assignTo' WHERE `task_id` = '$taskID'");
			}
			elseif(isset_true($data['acknowledge']))
			{
				$taskID = $data['taskID'];
				$this->setTaskStatus($taskID, 3);
				$userTable = $this->controller->newTable('user');
				$userTable->setMyTask($taskID);
			}
			elseif(isset_true($data['completed']))
			{
				$this->setTaskStatus($data['taskID'], 4);
			}
			elseif(isset_true($data['close']))
			{
				$this->setTaskStatus($data['taskID'], 5);
			}
			$this->controller->redirect();
		}
		
		function setTaskStatus($taskID, $taskStatusID)
		{
			$time = time();
			$this->update("UPDATE `ot_task` SET `task_update_time` = '$time' WHERE `task_id` = '$taskID';");
			return $this->update("UPDATE `ot_task` SET `task_status` = '$taskStatusID' WHERE `task_id` = '$taskID';");
		}
		
		function getTask($id)
		{
			$query=<<<SQL
SELECT `task_id`, `task_name`, `task_description`, `task_type_name`, `task_reporter`, `user_name` AS 'reporter_name', `task_developer`, `task_estimated_dev_time`, `task_create_time`, `task_update_time`, `task_status`, `task_project`
FROM `ot_task`, `ot_task_type`, `ot_user`
WHERE `task_type` = `task_type_id`
AND `task_reporter` = `user_id`
AND `task_id` = '$id'
LIMIT 1;
SQL;
			$task = $this->query($query);
			if($task) $task = $task[0];
			return $task;
		}
		
		function getStatusName($taskStatusID)
		{
			$result=$this->query("SELECT `task_status_name` FROM `ot_task_status` WHERE `task_status_id` = '$taskStatusID';");
			return $result[0]['task_status_name'];
		}
		
	
		/**
		 * returns tasks that this task depends on
		 */
		function getDependancies($id, $count=false)
		{
			$select = $count? 'COUNT(`task_id`)' : '`task_id`, `task_name`, `task_estimated_dev_time`';
			$query=<<<SQL
SELECT $select
FROM `ot_task`
WHERE `task_id` IN 
(
	SELECT `task_dependancy_dependancy`
	FROM `ot_task_dependancy`
	WHERE `task_dependancy_task` = '$id'
);
SQL;
			$tasks=$this->query($query);
			return $tasks;
		}
	
		/**
		 * returns tasks that depend on this task
		 */
		function getOppositeOfDependencies($id, $count=false)
		{
			$select = $count? 'COUNT(`task_id`)' : '`task_id`, `task_name`, `task_estimated_dev_time`';
			$query=<<<SQL
SELECT $select
FROM `ot_task`
WHERE `task_id` IN 
(
	SELECT `task_dependancy_task`
	FROM `ot_task_dependancy`
	WHERE `task_dependancy_dependancy` = '$id'
);
SQL;
			$tasks=$this->query($query);
			return $tasks;
		}
		
		function getCommentsHTML($taskID)
		{
			$DATEFORMAT = 'd/m/y H:i:s';
			$HTML='<h2>Comments</h2>';
			$data=$this->query("SELECT * FROM `ot_task_note` WHERE `task_note_task_id` = '$taskID' AND `task_note_status` = '1';");
			if(is_array($data))
			{
				$userTable = $this->controller->newTable('user');
				foreach($data as $note)
				{
					$comment	= cleanHTML($note['task_note_comment']);
					$time		= date($DATEFORMAT,$note['task_note_time']);
					$creator	= $userTable->getUserName($note['task_note_creator']);
					
					$HTML.="<p>$time $creator:<br /><b>$comment</b></p>";
				}
			}
			return $HTML;
		}
		
		function listTasks($project)
		{
			$query=<<<SQL
SELECT
	`task_id`,
	`task_name`,
	`task_status`, 
	`user_name` AS 'reporter',
	`task_status_name`,
	`task_type_name`,
	`task_developer`,
	`task_estimated_dev_time`
FROM `ot_task`, `ot_task_type`, `ot_task_status`, `ot_user`
WHERE `task_status` = `task_status_id`
AND `task_type` = `task_type_id`
AND `task_reporter` = `user_id`
AND `task_project` = '$project'
ORDER BY
	`task_status` ASC, `task_type` ASC
SQL;
			$temp = $this->query($query);
			$tasks = array();
			foreach($temp as $task)
			{
				$taskObj = new stdClass();
				$taskObj->id				= $task['task_id'];
				$taskObj->name				= cleanHTML($task['task_name']);
				$taskObj->type				= $task['task_type_name'];
				$taskObj->reporter			= $task['reporter'];
				$taskObj->developer			= $task['task_developer'];
				$taskObj->statusID			= $task['task_status'];
				$taskObj->statusName		= $task['task_status_name'];
				$taskObj->time				= $task['task_estimated_dev_time'];
				$taskObj->afterMe			= $this->getOppositeOfDependencies($taskObj->id,true);
				$taskObj->afterMe			= $taskObj->afterMe[0]['COUNT(`task_id`)'];
				$taskObj->detailedBeforeMe	= $this->getDependancies($taskObj->id);
				$taskObj->beforeMe			= is_array($taskObj->detailedBeforeMe)? count($taskObj->detailedBeforeMe):0;
				$temp = $taskObj->detailedBeforeMe;
				$taskObj->detailedBeforeMe='';
				if(is_array($temp))
				{
					$i=0;
					foreach($temp as $taskb)
					{
						$nameb	= cleanHTML($taskb['task_name']);
						$idb	= $taskb['task_id'];
						$taskObj->detailedBeforeMe.="<a href=\"../task/$idb\" title=\"$nameb\">$idb</a>";
						if($i < $taskObj->beforeMe-1) $taskObj->detailedBeforeMe.=', ';
						$i++;
					}
				}
				if($taskObj->developer)
				{
					$taskObj->developer=$this->query("SELECT `user_name` FROM `ot_user` WHERE `user_id` = '$taskObj->developer';");
					$taskObj->developer=$taskObj->developer[0]['user_name'];
				}else{
					$taskObj->developer='--';
				}
				$tasks[] = $taskObj;
			}
			
			return $tasks;
		}
		
		function deleteTask($id=null)
		{
			if(is_numeric($id))
			{
				$this->update("DELETE FROM `ot_task_note` WHERE `task_note_id` = $id;");
				$this->update("DELETE FROM `ot_task_dependancy` WHERE `task_dependancy_task` = $id;");
				$this->update("DELETE FROM `ot_task_dependancy` WHERE `task_dependancy_dependancy` = $id;");
				$this->update("DELETE FROM `ot_task` WHERE `task_id` = $id;");
			}
		}
	}
?>