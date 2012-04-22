<?php echo $this->topBar?>
<h1>Admin</h1>
<div class="spacer">
	<?php echo $this->getError('<div class="note error"><span>!</span><p>%</p></div>')?>
	<?php echo $this->getNote('<div class="note ok"><span>&#10003;</span><p>%</p></div>')?>
	<ul class="menu">
		<li><a href="../task/list">Manage Tasks</a></li>
		<li><a href="../admin/projects">Manage Projects</a></li>
		<li><a href="../admin?action=viewUsers">Manage Users</a></li>
		<li><a href="../admin?action=createUser">Create User</a></li>
	</ul>
	
	<h2>Manage Projects</h2>
	<table>
		<?php foreach($this->projects as $project) { ?>
			<tr>
				<?php if($project->project_status) { ?>
					<td><?php echo $project->project_name?></td>
					<td>
						<form method="post" action="">
							<input type="hidden" name="id" value="<?php echo $project->project_id?>" />
							<input type="submit" name="action" value="Delete" />
						</form>
					</td>
				<?php } else { // Project is disabled ?>
					<td><strike><?php echo $project->project_name?></strike></td>
					<td>
						<form method="post" action="">
							<input type="hidden" name="id" value="<?php echo $project->project_id?>" />
							<input type="submit" name="action" value="Enable" />
						</form>
					</td>
				<?php } // end whether project is enabled or not ?>
			</tr>
		<?php } // end foreach projects ?>
	</table>
	
	<h2>New Project</h2>
	<form method="post" action="">
		<input type="hidden" name="action" value="new" />
		<input type="text" name="name" />
		<input type="submit" value="Create" />
	</form>
</div>