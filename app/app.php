<?php 
	class app extends controller
	{
		var $taskTable = null;
		
		function init()
		{
			$this->addCSS('reset');
			$this->addCSS('style');
			
			if(!isset_true($_SESSION['logged_in']))
			{
				$_SESSION['logged_in']		= false;
				$_SESSION['user_id']		= 0;
				$_SESSION['user_name']		= '';
				$_SESSION['user_type']		= 0;
				$_SESSION['user_project']	= 0;
			}
			if(!$_SESSION['logged_in'] && isset_val($this->core->uri[0])!=='login') $this->redirect('login');
			
			$this->taskTable = $this->newTable('task');
			if(isset_true($_POST['project']))
			{
				$userTable = $this->newTable('user');
				$userTable->setProject($_POST['project']);
			}
			
			if(isset_true($_POST['quicktask']))
			{
				$taskID = $_POST['quicktask'];
				$projectID = $this->taskTable->get('task_project', $taskID);
				if($projectID)
				{
					$_SESSION['user_project'] = $projectID;
					$this->redirect('task/'.$taskID);
				}
				else
				{
					$this->setError('Invalid Task ID');
					$this->redirect();
				}
			}
		}
		
		function topBar()
		{
			$loggedIn	= $_SESSION['logged_in'];
			$userName	= $_SESSION['user_name'];
			$userType	= $_SESSION['user_type'];
			$userProject= $_SESSION['user_project'];
			$indexDir	= $this->core->config['index_dir'];
			
			$html = '<div class="topBar"><h1><a href="'.$indexDir.'">OneTask <span>&#10003;</span></a></h1>';
			
			if($loggedIn)
			{
				$html.="Hello $userName, \n";
				if($userType==2) $html.='<a href="'.$indexDir.'task">My Task</a>, '."\n";
				$html.='<a href="'.$indexDir.'report">New</a>, '."\n";
				$html.='<a href="'.$indexDir.'task/list">List</a>, '."\n";
				if($userType==2 || $userType==3) $html.='<a href="'.$indexDir.'summary">Summary</a>, '."\n"; 
				if($userType==3) $html.='<a href="'.$indexDir.'admin">Admin</a>, '."\n"; 
				$html.='<a href="'.$indexDir.'edit_profile">Edit Profile</a>, '."\n";
				
				$projects=$this->taskTable->query("SELECT * FROM `ot_project` WHERE `project_status` = 1 ORDER BY `project_id` = $userProject DESC");
				$html.='<form method="post" action=""><select name="project" onchange="javascript:form.submit()">'."\n";
				foreach($projects as $project) $html.='<option value="'.$project['project_id'].'">'.$project['project_name']."</option>\n";
				$html.='</select></form>, '."\n";
				
				$html.='<form method="post" action=""><input type="text" name="quicktask" value="Task ID" onclick="javascript:this.value=\'\'" /></form>, '."\n";
				
				$html.='<a href="'.$indexDir.'login?action=logout">Log Out</a>';
			}
			
			return "$html</div>";
		}
		
		function salt_md5($text)
		{
			$salt='t_4vY}@(c\DG=qvm/Ie3,wn0drp$TpLJ$';
			return md5($salt.$text);
		}
	}
?>