<?php echo  $this->controller->core->app->topBar() ?>
<h1>404. Page not found</h1>
<p class="note error">
	Sorry, but the page:
	<br /><b>http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?></b>
	<br />doesn't appear to exist.
</p>