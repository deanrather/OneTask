<?=$this->topBar?>
<h1><?=$this->title?></h1>
<div class="taskBody spacer">
	<?=$this->getError('<div class="note error"><span>!</span><p>%</p></div>')?>
	<?=$this->getNote('<div class="note ok"><span>&#10003;</span><p>%</p></div>')?>
	<?php $back = ($this->myTask? '' : '../'); // how many levels deep are we? ?>
	<div class="description">
		<?=($this->taskDescription ? $this->taskDescription : $this->taskName)?>
	</div>
	<div class="details">
		<h2>Details<small>[<a href="<?=$back?>report?edit=<?=$this->taskID?>">edit</a>]</small></h2>
		<ul>
			<li>Status: <b><?=$this->taskStatus?></b></li>
			<li>ID: <b><?=$this->taskID?></b></li>
			<li>Type: <b><?=$this->taskType?></b></li>
			<li>Reporter: <b><?=$this->taskReporterName?></b></li>
			<li>Assigned To: <b><?=$this->taskDeveloper?></b></li>
			<li>Estimated Development Time: <b><?=$this->taskEstDevTime?></b></li>
			<li>Created: <b><?=$this->taskCreateTime?></b></li>
			<li>Last Modified: <b><?=$this->taskUpdateTime?></b></li>
		</ul>

		<?php
		if(count($this->beforeMe))
		{
			print '<h4>Can\'t be done till after:</h4>';
			foreach($this->beforeMe as $task)
			{
				$id		= $task['task_id'];
				$title	= cleanHTML($task['task_name']);
				print '<a href="'.$back.'task/'.$id.'" target="_blank">'.$title.'</a><br />';
			}
		}
	
		if(count($this->afterMe))
		{
			print '<h4>Must be done before:</h4>';
			foreach($this->afterMe as $task)
			{
				$id		= $task['task_id'];
				$title	= cleanHTML($task['task_name']);
				print '<a href="'.$back.'task/'.$id.'" target="_blank">'.$title.'</a><br />';
			}
			print '<br />';
		}
		?>
	</div>
	
	<div class="comments">
		<?=$this->comments?>
		<form method="post" action="">
			<input type="hidden" name="taskID" value="<?=$this->taskID?>" />
			<label>
				Add Comment:<br />
				<textarea name="newComment" rows="4" cols="45"></textarea>
			</label>
			<br />
			<input type="submit" value="Add Comment" />
		</form>
	</div>
	
	<div class="changeStatus">
		<?=$this->changeStatusForm?>
	</div>
</div>