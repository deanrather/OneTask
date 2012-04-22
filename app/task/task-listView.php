<?php echo $this->topBar?>
<h1><?php echo $this->title?></h1>
<div class="taskBody spacer">
	<?php echo $this->getError('<div class="note error"><span>!</span><p>%</p></div>')?>
	<?php echo $this->getNote('<div class="note ok"><span>&#10003;</span><p>%</p></div>')?>
	<table class="sortable highlight">
		<thead>
			<tr>
				<td>ID</td>
				<td>Title</td>
				<td>Type</td>
				<td>Reporter</td>
				<td>Developer</td>
				<td>Before</td>
				<td># Before</td>
				<td># After</td>
				<td>Time</td>
				<td>Status</td>
			</tr>
		</thead>
		<tbody>
		<?php foreach($this->tasks as $task){?>
			<tr>
				<td><?php echo $task->id?></td>
				<td>
					<?php if($task->statusID==3) echo '<strong>';?>
					<?php if($task->statusID>3) echo '<del>';?>
					<a href="../task/<?php echo $task->id?>"><?php echo stripPad($task->name,50)?></a>
					<?php if($task->statusID>3) echo '</del>';?>
					<?php if($task->statusID==3) echo '</strong>';?>
				</td>
				<td><?php echo $task->type?></td>
				<td><?php echo $task->reporter?></td>
				<td><?php echo $task->developer?></td>
				<td><?php echo $task->detailedBeforeMe?></td>
				<td><?php echo $task->beforeMe?></td>
				<td><?php echo $task->afterMe?></td>
				<td><?php echo displayMinutes($task->time)?></td>
				<td><?php echo $task->statusName?></td>
			</tr>
		<?php } // End foreach task ?>
		</tbody>
	</table>
</div>