<?php
	class edit_profileController extends controller
	{
		function indexView()
		{
			$this->setTitle('OneTask - Edit Profile');
			$this->view->topBar = $this->core->app->topBar();
			
			$mine = true;
			$otherUserName = false;
			$userID = $_SESSION['user_id'];
			$userType = $_SESSION['user_type'];
			$this->userTable = $this->newTable('user');
			
			// We're looking at someone else's
			if(isset($_GET['id']) && $_GET['id']!=$userID) 
			{
				if($userType!=3)
				{
					$this->setError('Only an admin can do that.');
					$this->redirect('login');
				}else
				{
					$mine=false;
					$userID=$_GET['id'];
					$userTypes=$this->userTable->query('SELECT `user_type_id`, `user_type_name` FROM `ot_user_type` WHERE `user_type_status` = 1;');
					$otherUser=$this->userTable->query("SELECT `user_name`, `user_type`, `user_status` FROM `ot_user` WHERE `user_id` = '$userID'");
					if($otherUser)
					{
						$otherUserName		= $otherUser[0]['user_name'];
						$otherUserType		= $otherUser[0]['user_type'];
						$otherUserStatus	= $otherUser[0]['user_status'];
					}else{
						$this->setError('Invalid User.');
					}
				}
				
				// Change User Type
				if(isset($_POST['action']) && $_POST['action']=='change_user_type')
				{
					$newUserType=$_POST['newUserType'];
					if($this->userTable->update("UPDATE `ot_user` SET `user_type` = '$newUserType' WHERE `user_id` = '$userID';"))
					{
						$otherUserType=$newUserType;
						$this->setNote('User Type Successfully changed.');
					}else{
						$this->setError('User Type update failed.');
					}
					$this->redirect();
				}
				
				// Enable Account
				if(isset($_POST['action']) && $_POST['action']=='enable_account')
				{
					if($this->userTable->update("UPDATE `ot_user` SET `user_status` = '1' WHERE `user_id` = '$userID';"))
					{
						$otherUserStatus=1;
						$this->setNote('Account enabled.');
					}else{
						$this->setError('Failed to enable account.');
					}
					$this->redirect();
				}
				
				// Disable Account
				if(isset($_POST['action']) && $_POST['action']=='disable_account')
				{
					if($this->userTable->update("UPDATE `ot_user` SET `user_status` = '0' WHERE `user_id` = '$userID';"))
					{
						$otherUserStatus=0;
						$this->setNote('Account disabled.');
					}else{
						$this->setError('Failed to disable account.');
					}
					$this->redirect();
				}
			}
			
			// Change Password
			if(isset($_POST['action']) && $_POST['action']=='change_password')
			{
				if($_POST['new_password'] && $_POST['new_password_confirm'])
				{
					if(isset($_POST['old_password'])) $oldPass = $_POST['old_password'];
					$newPass	= $_POST['new_password'];
					$newPass2	= $_POST['new_password_confirm'];
					if($newPass!==$newPass2)
					{
						$this->setError('New Passwords don\'t match');
					}else{
						if(isset($oldPass)) $oldPass=$this->core->app->salt_md5($oldPass);
						$newPass=$this->core->app->salt_md5($newPass);
						
						if($mine)
						{
							$query="UPDATE `ot_user` SET `user_pass` = '$newPass' WHERE `user_id` = '$userID' AND `user_pass` = '$oldPass';";
							if($this->userTable->update($query))
							{
								$this->setNote('Password Successfully changed.');
							}else{
								$this->setError('Password update failed. Please enter correct old password.');
							}
						}else{
							$query="UPDATE `ot_user` SET `user_pass` = '$newPass' WHERE `user_id` = '$userID';";
							if($this->userTable->update($query))
							{
								$this->setNote('Password Successfully changed.');
							}else{
								$this->setError('Password update failed. That was probably already their password.');
							}
						}
					}
				}else{
					$this->setError('Please enter your password details.');
				}
				$this->redirect();
			}
			
			$this->view->mine = $mine;
			$this->view->otherUserName = $otherUserName;
			if($otherUserName)
			{
				$this->view->otherUserStatus = ($otherUserStatus ? $otherUserStatus : false);
				$this->view->otherUserType = ($otherUserType ? $otherUserType : false);
				$this->view->userTypes = $userTypes;
			}
		}
	}
?>