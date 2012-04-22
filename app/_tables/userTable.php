<?php 
	class userTable extends table
	{
		var $table = 'ot_user';
		var $key = 'user_id';

		function setProject($projectID)
		{
			$userID = $_SESSION['user_id'];
			$_SESSION['user_project'] = $projectID;
			$this->update("UPDATE `ot_user` SET `user_project` = '$projectID' WHERE `user_id` = '$userID'");
		}
	
		/*
		If I have a task & it's not done - return it.
		Otherwise, get me a new task!
		*/
		function getMyTask()
		{
			$userID = $_SESSION['user_id'];
			$userProject = $_SESSION['user_project'];
			
			// This query checks to see if the user has a task set in the user table.
			$query=<<<SQL
SELECT
	`task_id`,
	`task_name`,
	`task_description`,
	`task_type_name`,
	`task_reporter`,
	`user_name` AS 'reporter_name',
	`task_developer`,
	`task_estimated_dev_time`,
	`task_create_time`,
	`task_update_time`,
	`task_status`
FROM `ot_task`, `ot_task_type`, `ot_user`
WHERE `task_type` = `task_type_id`
AND `task_reporter` = `user_id`
AND `task_id` IN(SELECT `user_task` FROM `ot_user` WHERE `user_id` = '$userID')
AND `task_status` NOT IN (4,5)
AND `task_project` = '$userProject'
LIMIT 1;
SQL;
			$task = $this->query($query);
			if(!$task){
				// Get me a task:
				/*
					This query grabs a task who:
					* isn't dependant on any active tasks
					* is not marked 'complete' or 'closed'
					It orders by:
					* If it's assigned to me, then
					* # of tasks depending on it
					* status (started, then unstarted), then
					* Importance of task (bugs before features), then
					* estimated duration of task (shortest first), then
					* time task was entered (first in, first out)
				*/
				$query = <<<SQL
SELECT
	`task_id`,
	`task_name`,
	`task_description`,
	`task_type_name`,
	`task_reporter`,
	`user_name` AS 'reporter_name',
	`task_developer`,
	`task_estimated_dev_time`,
	`task_create_time`,
	`task_update_time`,
	`task_status`,
	(
		SELECT COUNT(`task_dependancy_id`)
		FROM `ot_task_dependancy`
		WHERE `task_dependancy_dependancy` = `task_id`
	) as 'predecessors'
	
FROM
	`ot_task`,
	`ot_task_type`,
	`ot_user`
	
WHERE `task_type` = `task_type_id`
AND `task_reporter` = `user_id`
AND `task_id` NOT IN 
(
	SELECT `task_dependancy_task`
	FROM `ot_task_dependancy`, `ot_task`
	WHERE `task_status` NOT IN (4,5)
	AND `task_id` = `task_dependancy_dependancy`
)
AND `task_status` IN (1,2,3)
AND `task_project` = '$userProject'

ORDER BY
	(`task_developer` = '$userID') DESC,
	`predecessors` DESC,
	`task_status` ASC,
	`task_type` ASC,
	`task_estimated_dev_time` ASC,
	`task_create_time` DESC
LIMIT 1;
SQL;
				$task = $this->query($query);
				if(!$task)
				{
					$task = false;
				}
				else
				{
					$task = $task[0];
					// Make this my new task
					$taskID = $task['task_id'];
					$this->setMyTask($taskID);
				}
			}else{
				$task = $task[0];
			}
			//setTaskStatus($task['task_id'],2);
			return $task;
		}
		
		function setMyTask($taskID)
		{
			$userID = $_SESSION['user_id'];
			$this->update("UPDATE `ot_user` SET `user_task` = '$taskID' WHERE `user_id` = '$userID';");
		}
		
		function getUserName($userID)
		{
			$name = $this->get('user_name', $userID);
			return ($name ? $name : 'Unknown');
		}
	}
?>