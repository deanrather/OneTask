<?php 
	class editController extends controller
	{
		function indexView()
		{
			$this->setTitle('OneTask - Edit Profile');
			$this->view->topBar = $this->core->app->topBar();
			$this->setView('report');
		}
	}
?>