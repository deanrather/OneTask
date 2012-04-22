<?php echo $this->topBar?>
<h1>Edit <?php echo (!$this->mine ? $this->otherUserName.'\'s ' : '')?>Profile</h1>
<div class="spacer editProfilePage">
	<?php echo $this->getError('<div class="note error"><span>!</span><p>%</p></div>')?>
	<?php echo $this->getNote('<div class="note ok"><span>&#10003;</span><p>%</p></div>')?>
	
	<? if(!$this->mine) { ?>
		<h2>Change User Type</h2>
		<form method="post" action="">
			<input type="hidden" name="action" value="change_user_type" />
			
			<label>
				User Type:<br />
				<select name="newUserType">
				<?
					foreach($this->userTypes as $userType)
					{
						$id		= $userType['user_type_id'];
						$name	= $userType['user_type_name'];
						
						if($id==$this->otherUserType)
							print "<option value='$id' selected='selected'>$name</option>";
						else
							print "<option value='$id'>$name</option>";
					}
				?>
				</select>
			</label>
			<br />
			
			<input type="submit" value="Change Type" />
		</form>
		
		<? if($this->otherUserStatus){ ?>
			<h2>Disable Account</h2>
			<form method="post" action="">
				<input type="hidden" name="action" value="disable_account" />
				<input type="submit" value="DISABLE" />
			</form>
		<? }else{ // Account is disabled ?>
			<h2>Enable Account</h2>
			<form method="post" action="">
				<input type="hidden" name="action" value="enable_account" />
				<input type="submit" value="ENABLE" />
			</form>
		<? } // endif account enabled / disabled. ?>
	<? } // endif looking at someone else's account. ?>
	
	<h2>Change Password</h2>
	<form method="post" action="">
		<input type="hidden" name="action" value="change_password" />
		
		<?if($this->mine){?>
			<label>
				Old Password:<br />
				<input type="password" name="old_password" />
			</label><br />
		<?} // Endif my own profile ?>
		
		<label>
			New Password:<br />
			<input type="password" name="new_password" />
		</label><br />
		
		<label>
			Confirm New Password:<br />
			<input type="password" name="new_password_confirm" />
		</label><br />
		
		<input type="submit" value="Change Password" />
	</form>
	<?php echo ($this->mine ? '' : '<br /><a href="admin?action=viewUsers">Back</a>')?>
</div>