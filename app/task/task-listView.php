<?=$this->topBar?>
<h1><?=$this->title?></h1>
<div class="taskBody spacer">
	<?=$this->getError('<div class="note error"><span>!</span><p>%</p></div>')?>
	<?=$this->getNote('<div class="note ok"><span>&#10003;</span><p>%</p></div>')?>
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
		<?foreach($this->tasks as $task){?>
			<tr>
				<td><?=$task->id?></td>
				<td>
					<?if($task->statusID==3)echo'<strong>';?>
					<?if($task->statusID>3)echo'<del>';?>
					<a href="../task/<?=$task->id?>"><?=stripPad($task->name,50)?></a>
					<?if($task->statusID>3)echo'</del>';?>
					<?if($task->statusID==3)echo'</strong>';?>
				</td>
				<td><?=$task->type?></td>
				<td><?=$task->reporter?></td>
				<td><?=$task->developer?></td>
				<td><?=$task->detailedBeforeMe?></td>
				<td><?=$task->beforeMe?></td>
				<td><?=$task->afterMe?></td>
				<td><?=displayMinutes($task->time)?></td>
				<td><?=$task->statusName?></td>
			</tr>
		<?} // End foreach task ?>
		</tbody>
	</table>
</div>