<h1>OneTask Login</h1>
<div class="spacer loginPage">
	<?php echo $this->getError('<div class="note error"><span>!</span><p>%</p></div>')?>
	<?php echo $this->getNote('<div class="note ok"><span>&#10003;</span><p>%</p></div>')?>
	<form method="post" action="">
		<label>
			Username:<br />
			<input type="text" name="username" value="<?php echo $this->username?>" id="defaultCursor" />
			<?php echo $this->addJS('document.getElementById("defaultCursor").focus();')?>
		</label>
		<br />
		
		<label>
			Password:<br />
			<input type="password" name="password" />
		</label>
		<br />
		
		<input type="submit">
	</form>
</div>