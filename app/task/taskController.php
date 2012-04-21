<?php
	class taskController extends controller
	{
		/**
		 * @var taskTable
		 */
		private $taskTable = null;
		
		/**
		 * @var userTable
		 */
		private $userTable = null;
		
		/**
		 * @var noteTable
		 */
		private $noteTable = null;
		
		function init()
		{
			$this->noteTable = $this->newTable('note');
			$this->taskTable = $this->newTable('task');
			$this->userTable = $this->newTable('user');
		}
		
		function indexView()
		{
			$loggedIn	= $_SESSION['logged_in'];
			$userID		= $_SESSION['user_id'];
			$userName	= $_SESSION['user_name'];
			$userType	= $_SESSION['user_type'];
			$userProject= $_SESSION['user_project'];
			
			$DATEFORMAT = 'd/m/y H:i:s';
			
			if(isset_true($_POST['changeTaskStatus']))	$this->taskTable->handleChangeStatus($_POST);
			if(isset_true($_POST['newComment']))		$this->noteTable->handleNewComment($userID, $_POST);
			
			// Reporters can view all bugs, and edit their own ones
			// Administrators can view/edit/delete/ all bugs.
			$task = array();
			$list = false; // view a list, or a task?
			$myTask = false; // Is this my "one task"?
			
			if(isset($this->core->uri[1]) && is_numeric($this->core->uri[1]))
			{ // We know which task we're looking at
				$id = $this->core->uri[1];
				$task = $this->taskTable->getTask($id);
				
				if(!$task)
				{
					$this->setError('Invalid Task ID');
					$this->redirect('task/list');
				}
				
				if($userProject != $task['task_project'])
				{
					// If they're viewing task in project A, and swap to project B...
					$this->redirect('task/list');
				}
			}
			elseif($userType==2)
			{ // I'm a Developer! What is my one task...
				$myTask = true;
				$task = $this->userTable->getMyTask();
				if(!$task)
				{
					$this->setError('Please choose your own task.');
					$this->redirect('task/list');
				}
			}
			else
			{
				$list = true;
			}
			
			// Details about the one task
			$this->view->taskID				= $task['task_id'];
			$this->view->taskName			= cleanHTML($task['task_name']);
			$this->view->taskDescription	= cleanHTML($task['task_description']);
			$this->view->taskType			= $task['task_type_name'];
			$this->view->taskReporterID		= $task['task_reporter'];
			$this->view->taskReporterName	= $task['reporter_name'];
			$this->view->taskDeveloper		= $task['task_developer']==$userID?'Me':'Someone Else';
			$this->view->taskEstDevTime		= displayMinutes($task['task_estimated_dev_time']);
			$this->view->taskCreateTime		= date($DATEFORMAT,$task['task_create_time']);
			$this->view->taskUpdateTime		= date($DATEFORMAT,$task['task_update_time']);
			$this->view->taskStatus			= $this->taskTable->getStatusName($task['task_status']);
			$this->view->beforeMe			= $this->taskTable->getDependancies($this->view->taskID);
			$this->view->afterMe			= $this->taskTable->getOppositeOfDependencies($this->view->taskID);
			$this->view->comments			= $this->taskTable->getCommentsHTML($this->view->taskID);
			$this->view->changeStatusForm	= $this->getChangeTaskStatusHTML($this->view->taskID);
			$title = cleanHTML($task['task_name']).' ['.$this->view->taskStatus.']';
			
			$this->setTitle("OneTask - $title");
			$this->view->title				= $title;
			$this->view->list				= $list;
			$this->view->topBar				= $this->core->app->topBar();
			$this->view->userType			= $userType;
			$this->view->myTask				= $myTask;
		}
		
		function getChangeTaskStatusHTML($taskID)
		{
			$userID	= $_SESSION['user_id'];
			/*
				If "New", offers: "Assign to [Myself] [Go]" Where [Myself] is a drop-down of developers.
				If "Assigned" (to me) offers: "In Progress"
				If "In Progress", offers: "Completed"
				If "Completed" offers: "Close"
				if "Closed" offers: "Re-open and assign to: [Myself] [Go]"
			*/
			$taskStatusID=$this->taskTable->query("SELECT `task_status` FROM `ot_task` WHERE `task_id` = '$taskID'");
			$taskStatusID=$taskStatusID[0]['task_status'];
			$taskStatusName = $this->taskTable->getStatusName($taskStatusID);
			$html = '<form method="post" action=""><input type="hidden" name="changeTaskStatus" value="TRUE" /><input type="hidden" name="taskID" value="'.$taskID.'" />';
			$html.='<h2>Change Task Status</h2>';
			if($taskStatusName == 'New')
			{
				$developers=$this->userTable->query("SELECT `user_id`, `user_name` FROM `ot_user` WHERE `user_type` = '2' AND `user_id` != '$userID' AND `user_status` = 1;");
				$html.='Assign to <select name="assignTo"><option value="'.$userID.'">Myself</option>';
				if(is_array($developers)) foreach($developers as $developer) $html.='<option value="'.$developer['user_id'].'">'.$developer['user_name'].'</option>';
				$html.='</select><input type="submit" value="Go" />';
				
			}
			elseif($taskStatusName == 'Assigned')
			{ // Change to "In Progress"
				$html.='<input type="hidden" name="acknowledge" value="TRUE" />';
				$html.='Change status to <input type="submit" value="In Progress" />';
			}
			elseif($taskStatusName == 'In Progress')
			{ // Change to "Completed"
				$html.='<input type="hidden" name="completed" value="TRUE" />';
				$html.='Change status to <input type="submit" value="Completed" />';
			}
			elseif($taskStatusName == 'Complete')
			{ // Close Task
				$html.='<input type="hidden" name="close" value="TRUE" />';
				$html.='<input type="submit" value="Close" /> this task.';
			}
			elseif($taskStatusName == 'Closed')
			{ // Delete, or Re-open, and assign to <someone>
				$developers=$this->userTable->query("SELECT `user_id`, `user_name` FROM `ot_user` WHERE `user_type` = '2' AND `user_id` != '$userID' AND `user_status` = 1;");
				$html.='Re-Open, and assign to <select name="assignTo"><option value="'.$userID.'">Myself</option>';
				if(count($developers)) foreach($developers as $developer) $html.='<option value="'.$developer['user_id'].'">'.$developer['user_name'].'</option>';
				$html.='</select><input type="submit" value="Go" />';
				$userType = $_SESSION['user_type'];
				if($userType==3) $html.=' or <input type="submit" name="delete" value="DELETE" />';
			}
			$html.='</form>';
			return $html;
		}
		
		function listView()
		{
			$this->setTitle('OneTask - Task List');
			$this->view->title = 'All Tasks';
			$this->addJS('common');
			$this->addJS('css');
			$this->addJS('standardista-table-sorting');
			
			$this->view->topBar = $this->core->app->topBar();
			$userProject = $_SESSION['user_project'];
			$this->view->tasks = $this->taskTable->listTasks($userProject);
		}
	}
?>