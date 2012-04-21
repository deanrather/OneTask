<?=$this->topBar?>
<h1>Admin</h1>
<div class="spacer adminPage">
	<?=$this->getError('<div class="note error"><span>!</span><p>%</p></div>')?>
	<?=$this->getNote('<div class="note ok"><span>&#10003;</span><p>%</p></div>')?>
	<ul class="menu">
		<li><a href="task/list">Manage Tasks</a></li>
		<li><a href="admin/projects">Manage Projects</a></li>
		<li><a href="admin?action=viewUsers">Manage Users</a></li>
		<li><a href="admin?action=createUser">Create User</a></li>
	</ul>
	
	<?
		if(isset($_GET['action']) && $_GET['action']=='viewUsers')
		{
	?>
		<h2>View Users</h2>
		<table class="highlight">
		<?
			foreach($this->users as $user)
			{
				$id		= $user['user_id'];
				$name	= $user['user_name'];
				$status	= $user['user_status'];
				$type	= $user['user_type_name'];
				
				print '<tr>';
				print "<td><a href=\"edit_profile?id=$id\">";
				print $status ? $name : "<font style=\"text-decoration:line-through;\">$name</font>";
				print '</a></td>';
				print "<td>$type</td>";
				print '</tr>';
			}
		?>
		</table>
	<?
		} // Endif view users
		elseif(isset($_GET['action']) && $_GET['action']=='createUser')
		{
	?>
		<h2>Create User</h2>
		<form method="post" action="">
			<input type="hidden" name="action" value="create_user" id="defaultCursor" />
			<?=$this->addJS('document.getElementById("defaultCursor").focus();')?>
			
			<label>
				Username:<br />
				<input type="text" name="username" value="<?=$this->username?>"/>
			</label>
			<br />
			
			<label>
				Password:<br />
				<input type="password" name="password" />
			</label>
			<br />
			
			<label>
				Confirm Password:<br />
				<input type="password" name="password_confirm" />
			</label>
			<br />
			
			<label>
				User Type:<br />
				<select name="user_type">
				<?
					foreach($this->userTypes as $userType)
					{
						$id		= $userType['user_type_id'];
						$name	= $userType['user_type_name'];
						print "<option value='$id'>$name</option>";
					}
				?>
				</select>
			</label>
			<br />
			
			<input type="submit" value="Create" />
		</form>
	<?
		}
	?>
</div>