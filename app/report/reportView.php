<?=$this->topBar?>
<h1>Report a Task</h1>
<div class="spacer">
	<?=$this->getError('<div class="note error"><span>!</span><p>%</p></div>')?>
	<?=$this->getNote('<div class="note ok"><span>&#10003;</span><p>%</p></div>')?>
	<form method="post" action="">
		<input type="hidden" name="add_task" value="true" />
		<ul class="reportForm">
			<li class="title">
				<label>
					Title: <br />
					<input type="text" name="title" value="<?=$this->title?>" id="defaultCursor" />
					<?=$this->addJS('document.getElementById("defaultCursor").focus();')?>
				</label>
			</li>
			
			<li class="description">
				<label>
					Description: <br />
					<textarea name="description" rows="5" cols="45"><?=$this->desc?></textarea>
				</label>
			</li>
			
			<li class="type">
				<label>
					Type:<br />
					<select name="type">
						<?
							foreach($this->taskTypes as $taskType)
							{
								$id		= $taskType['task_type_id'];
								$name	= $taskType['task_type_name'];
								if($this->type==$id)
									print "<option value='$id' selected='selected'>$name</option>";
								else
									print "<option value='$id'>$name</option>";
							}
						?>
					</select>
				</label>
			</li>
			
			<li class="who">
				<label>
					Assign To:<br />
					<select name="developer">
						<option value="0">No One</option>
						<?
							foreach($this->users as $user)
							{
								$id		= $user['user_id'];
								$name	= $user['user_name'];
								if($this->dev==$id)
									print "<option value='$id' selected='selected'>$name</option>";
								else
									print "<option value='$id'>$name</option>";
							}
						?>
					</select>
				</label>
			</li>
			
			<li class="time">
				<label>
					Estimated Development Time:
					<input type="text" name="estimated_dev_hours" value="<?=$this->estDevHours?>" />
				</label>
				<label>
					:
					<input type="text" name="estimated_dev_minutes" value="<?=$this->estDevMinutes?>" />
				</label>
			</li>
			
			<li class="after">
				<a href="#" onclick="show('predecessors'); return false;"><b>Can't be done until after...</b></a><br />
				<div id="predecessors"<?=(count($this->predecessors) ? '' : ' class="hidden"')?>>
					<?
						foreach($this->tasks as $task)
						{
							$id		= $task['task_id'];
							$title	= $task['task_name'];
							$desc	= $task['task_description'];
							$checked = (in_array($id,$this->predecessors));
					?>
						<p>
							<input type="checkbox" value="<?=$id?>" name="predecessors[]" <?=($checked?'checked="checked"':'')?> />
							<a href="task/<?=$id?>" target="_blank" title="<?=htmlspecialchars($desc)?>"><?=$title?></a>
						</p>
					<? } // End foreach predecessor ?>
				</div>
			</li>
			
			<li class="before">
				<a href="#" onclick="show('successors'); return false;"><b>Must be done before...</b></a><br />
				<div id="successors"<?if(!count($this->successors))print' class="hidden"'?>>
					<?
						reset($this->tasks);
						foreach($this->tasks as $task)
						{
							$id		= $task['task_id'];
							$title	= $task['task_name'];
							$desc	= $task['task_description'];
							$checked = (in_array($id,$this->successors));
					?>
						<p>
							<input type="checkbox" value="<?=$id?>" name="successors[]" <?=($checked?'checked="checked"':'')?> />
							<a href="task/<?=$id?>" target="_blank" title="<?=htmlspecialchars($desc)?>"><?=$title?></a>
						</p>
					<? } // End foreach predecessor ?>
				</div>
			</li>
			
			<li class="submit">
				<input type="submit" value="Add Task" />
			</li>
		</ul>
	</form>
</div>