<?php 
	class adminController extends controller
	{
		function indexView()
		{
			$this->setTitle('OneTask - Admin');
			$this->view->topBar = $this->core->app->topBar();
			$note = $_SESSION['note'];
			$error = $_SESSION['error'];
			
			if(isset($_GET['action']) && $_GET['action']=='viewUsers')
			{
				$userTable = $this->newTable('user');
				$this->view->users = $userTable->query('SELECT `user_id`, `user_name`, `user_status`, `user_type_name` FROM `ot_user`, `ot_user_type` WHERE `user_type_id` = `user_type`;');
			}
			elseif(isset($_GET['action']) && $_GET['action']=='createUser')
			{
				$userTable = $this->newTable('user');
				$this->view->userTypes=$userTable->query('SELECT `user_type_id`, `user_type_name` FROM `ot_user_type` WHERE `user_type_status` = 1;');
			}
			
			if(isset($_POST['action']) && $_POST['action']=='create_user')
			{
				if($_POST['username']
				&& $_POST['password']
				&& $_POST['password_confirm']
				&& $_POST['user_type'])
				{
					$newUsername	= $_POST['username'];
					$newUserType	= $_POST['user_type'];
					$pass			= $_POST['password'];
					$pass2			= $_POST['password_confirm'];
					if($pass!==$pass2)
					{
						$error='New Passwords don\'t match';
					}else{
						$query="SELECT `user_id` FROM `ot_user` WHERE `user_name` LIKE '$newUsername'";
						$success=$userTable->query($query);
						if($success)
						{
							$error="$newUsername already exists!";
						}else{
							$pass=$this->core->app->salt_md5($pass);
							$query="INSERT INTO `ot_user` (`user_name`, `user_pass`, `user_type`) VALUES ('$newUsername','$pass','$newUserType');";
							$success=$userTable->update($query);
							if($success)
							{
								$note='User Created Successfully.';
								$username='';
							}else{
								$error='User Creation Failed.';
							}
						}
					}
				}else{
					$error='Please enter new user details.';
				}
				$this->setNote($note);
				$this->setError($error);
				$this->redirect();
			}
			
			$username = '';
			if(isset($_POST['username']) && $_POST['username']) $username = $_POST['username'];
			$this->view->username = $username;
			$this->view->note = $note;
		}
		
		function projectsView()
		{
			$projectTable = $this->newTable('project');
			if(isset_true($_POST['action']))
			{
				$error = '';
				$note = '';
				
				// Creating New
				if($_POST['action']=='new')
				{
					$name = $_POST['name'];
					if(!$name)
					{
						$error='You must name your new project.';
					}
					else
					{
						$success = $projectTable->update("INSERT INTO `ot_project` (`project_name`) VALUES ('$name');");
						if($success)
						{
							$note = 'Project Created';
						}
						else
						{
							$error = 'Failed to create project';
						}
					}
				}
				
				// Updating Status
				if($_POST['action']=='Delete' || $_POST['action']=='Enable')
				{
					$id = $_POST['id'];
					$status = ($_POST['action']=='Enable' ? 1 : 0);
					$success = $projectTable->update("UPDATE `ot_project` SET `project_status` = $status WHERE `project_id` = $id;");
					if($success)
					{
						$note = 'Project Updated';
					}
					else
					{
						$error = 'Failed to update project';
					}
				}
				
				$this->setNote($note);
				$this->setError($error);
				$this->redirect();
			}
			
			
			$this->setTitle('OneTask - Admin - Manage Projects');
			$this->view->topBar = $this->core->app->topBar();
			$this->view->projects = $projectTable->getRows();
		}
	}
?>