<?php 
	class noteTable extends table
	{
		var $table = 'ot_note';
		var $key = 'note_id';
		
		function handleNewComment($userID, $data)
		{
			$comment = addslashes($data['newComment']);
			$taskID = $data['taskID'];
			$time = time();
			$this->update("INSERT INTO `ot_task_note` (`task_note_comment`, `task_note_task_id`, `task_note_time`, `task_note_creator`)VALUES('$comment', '$taskID', '$time','$userID');");
			$this->controller->redirect();
		}
	}
?>