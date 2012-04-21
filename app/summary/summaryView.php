<?=$this->topBar?>
<h1>Summary</h1>
<div class="spacer">
	<?=$this->getError('<div class="note error"><span>!</span><p>%</p></div>')?>
	<?=$this->getNote('<div class="note ok"><span>&#10003;</span><p>%</p></div>')?>
	<pre><?=$this->chart?></pre>
	<br /><b>Legend:</b><br />
	<?php foreach($this->legend as $key => $val) print "$val $key<br />"; ?>
</div>