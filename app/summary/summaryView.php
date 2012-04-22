<?php echo $this->topBar?>
<h1>Summary</h1>
<div class="spacer">
	<?php echo $this->getError('<div class="note error"><span>!</span><p>%</p></div>')?>
	<?php echo $this->getNote('<div class="note ok"><span>&#10003;</span><p>%</p></div>')?>
	<pre><?php echo $this->chart?></pre>
	<br /><b>Legend:</b><br />
	<?php foreach($this->legend as $key => $val) print "$val $key<br />"; ?>
</div>