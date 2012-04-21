<?php
	class indexController extends controller
	{
		function indexView()
		{
			// Set default session variables
			if(!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'])
			{
				$_SESSION['logged_in'] = false;
			}
			
			// Get session variables for this page
			$loggedIn	= $_SESSION['logged_in'];
			$userType	= $_SESSION['user_type'];
			
			// Forward them to the appropriate page
			if(!$loggedIn)		$this->redirect('login');
			if($userType==1)	$this->redirect('report');
			if($userType==2)	$this->redirect('task');
			if($userType==3)	$this->redirect('admin');
			$this->core->error('Should have redirected...');
		}
	}
?>