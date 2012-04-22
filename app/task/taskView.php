<?php echo $this->topBar?>
<h1><?php echo $this->title?></h1>
<div class="taskBody spacer">
	<?php echo $this->getError('<div class="note error"><span>!</span><p>%</p></div>')?>
	<?php echo $this->getNote('<div class="note ok"><span>&#10003;</span><p>%</p></div>')?>
	<?php $back = ($this->myTask? '' : '../'); // how many levels deep are we? ?>
	<div class="description">
		<?php echo ($this->taskDescription ? $this->taskDescription : $this->taskName)?>
	</div>
	<div class="details">
		<h2>Details<small>[<a href="<?php echo $back?>report?edit=<?php echo $this->taskID?>">edit</a>]</small></h2>
		<ul>
			<li>Status: <b><?php echo $this->taskStatus?></b></li>
			<li>ID: <b><?php echo $this->taskID?></b></li>
			<li>Type: <b><?php echo $this->taskType?></b></li>
			<li>Reporter: <b><?php echo $this->taskReporterName?></b></li>
			<li>Assigned To: <b><?php echo $this->taskDeveloper?></b></li>
			<li>Estimated Development Time: <b><?php echo $this->taskEstDevTime?></b></li>
			<li>Created: <b><?php echo $this->taskCreateTime?></b></li>
			<li>Last Modified: <b><?php echo $this->taskUpdateTime?></b></li>
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
		<?php echo $this->comments?>
		<form method="post" action="">
			<input type="hidden" name="taskID" value="<?php echo $this->taskID?>" />
			<label>
				Add Comment:<br />
				<textarea name="newComment" rows="4" cols="45"></textarea>
			</label>
			<br />
			<input type="submit" value="Add Comment" />
		</form>
	</div>
	
	<div class="changeStatus">
		<?php echo $this->changeStatusForm?>
	</div>
</div>