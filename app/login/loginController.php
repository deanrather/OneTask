<?php 
	class loginController extends controller
	{
		function indexView()
		{
			$this->setTitle('OneTask - Login');
			$loggedIn = $_SESSION['logged_in'];
			$username = '';
			
			if($loggedIn && isset_val($_GET['action'])=='logout')
			{
				$loggedIn=false;
				$_SESSION['logged_in']=false;
				$_SESSION['username'] = '';
				$this->setNote('You have successfully logged out');
			}
			
			if($loggedIn)
			{
				$_SESSION['note'] = 'You\'re already logged in.';
				$this->redirect('index');
			}
			
			if(isset($_POST['username']) || isset($_POST['password']))
			{
				if(isset_true($_POST['username'])) $username = $_POST['username'];
				if($username && isset_true($_POST['password']))
				{
					$password = $this->core->app->salt_md5($_POST['password']);
					$table = $this->newTable('user');
					$user = $table->query("SELECT `user_id`, `user_name`, `user_type`, `user_project` FROM `ot_user` WHERE `user_name` LIKE '$username' AND `user_pass` = '$password' AND `user_status` = '1';");
					if(!$user)
					{
						$this->setError('Login Failed');
					}else{
						$_SESSION['logged_in']		= true;
						$_SESSION['user_id']		= $user[0]['user_id'];
						$_SESSION['user_name']		= $user[0]['user_name'];
						$_SESSION['user_type']		= $user[0]['user_type'];
						$_SESSION['user_project']	= $user[0]['user_project'];
						$this->setNote('Login Success.');
						$this->redirect('index');
					}
				}else{
					$this->setError('Please enter login details');
				}
				$_SESSION['username'] = $username;
				$this->redirect();
			}
			if(isset_true($_SESSION['username'])) $username = $_SESSION['username'];
			$this->view->username = $username;
		}
	}
?>