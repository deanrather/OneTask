<?php
	/*
	1) find beginnings.
	Beginnings depend on nothing.
	tasks with no dependancies
	put in an array beginnings[];
	probably don't need to find the beginnings
	
	2) find endings.
	Nothing depends on an ending.
	opposite of above.
	put in an array endings[];
	
	3) find paths between.
	If an ending is a beginning - it is a 1-step path.
	a path can be stored as an array of tasks
	a path can be stored in reverse order... because.
	paths can be stored as an array of paths
	for each ending,
		create a new path (ending with that ending)
		addChildren()
	function addChildren(){
		find any tasks before it
		if there are none
			that path is done
		if there is 1
			add it to the path
			addChildren()
		if there's more than 1
			add the first one to the path,
			for each of the other paths
				copy the current path as a new path
				add this task to the path
				addChildren()
	}


	PLAN B
	
	I think the complication is in storing the data.
	I should store it like this:
	1) Get all the task (as row objects) (with uid as key)
	2) Set an array of dependancies onto each task (as ids)
	3) Iterate over each task, then for each dependancy each of its tasks 
		This would be recursive, and IF a task where at some point dependant
		on itself (Which is shouldn't be) then would infinate loop.
		
	
	*/
	class summaryController extends controller
	{
		function indexView()
		{
			$this->setTitle('OneTask - Summary');
			if($_SESSION['user_type']!=2 && $_SESSION['user_type']!=3) $this->redirect('login');	
			$this->taskTable = $this->newTable('task');
			$userProject = $_SESSION['user_project'];
			$chart = '';
			$chartArray = array();
			
			$tasks=$this->taskTable->query("SELECT `task_id`, `task_name`, `task_estimated_dev_time`, `task_status` FROM `ot_task` WHERE `task_project` = '$userProject';");
			foreach($tasks as $task)
			{
				$dependancies=$this->taskTable->getDependancies($task['task_id']);
				if(!$dependancies)
				{
					$timeBefore=0;
				}else{
					$timeBefore=$this->getTimeBefore($task['task_id']);
				}
				
				$time = ceil((int)$task['task_estimated_dev_time']/60);
				
				$name = stripPad($task['task_name']);
				
				// A task is critical if the final task depends on it to be done in time.
				$critical=false;
				$chartArray[]=array
				(
					'timeBefore'	=> $timeBefore,
					'time'			=> $time,
					'name'			=> '<a href="task/'.$task['task_id'].'">'.$name.'</a>',
					'critial'		=> $critical,
					'status'		=> $task['task_status']
				);
			}
			
			foreach ($chartArray as $key => $val)
			{
				$timesBefore[$key]=$val['timeBefore'];
			}
			array_multisort($timesBefore, SORT_ASC, $chartArray);
			
			$legend=array(
				'Milestone'		=> '*',
				'Critical'		=> '~',
				'New'			=> '+',
				'Assigned'		=> '+',
				'In Progress'	=> '=',
				'Complete'		=> '-',
				'Closed'		=> '-'
			);
			
			$chart='';
			foreach($chartArray as $task)
			{
				$chart.="\n".str_pad($task['timeBefore']+$task['time'],2).' hrs | ';
				$chart.=$task['name'].'| ';
				for($i=0; $i<$task['timeBefore']; $i++) $chart.=' ';
				if(!$task['time'])
				{
					$chart.=$legend['Milestone'];
				}else{
					if($task['critial']) $bar = $legend['Critial'];
					elseif($task['status']==1) $bar = $legend['New'];
					elseif($task['status']==2) $bar = $legend['Assigned'];
					elseif($task['status']==3) $bar = $legend['In Progress'];
					elseif($task['status']==4) $bar = $legend['Complete'];
					elseif($task['status']==5) $bar = $legend['Closed'];
					else $bar = '-';
					for($i=0; $i<$task['time']; $i++) $chart.=$bar;
				}
			}
			$this->view->topBar = $this->core->app->topBar();
			$this->view->chart = $chart;
			$this->view->legend = $legend;
			
			// TODO DEBUG:
			// $this->view->chart .= '<pre>'.print_r($this->createPaths(),true).'</pre>';
		}
		
	
		function createPaths()
		{
			$paths=array();
			$endings=$this->getEndings();
			foreach ($endings as $ending)
			{
				$paths=$this->newPath($paths, $ending);
			}
			return $paths;
		}
		
		function newPath($paths, $task)
		{
			$path=array();
			$path[]=$task;
			$children=$this->getChildren($task);
			$childrenCount=count($children);
			if($childrenCount==1)
			{
				$path[]=$children[0];
				
			}else if($childrenCount>1){
				$path[]=$children[0];
				for($i=1; $i<$childrenCount; $i++)
				{
					$paths=$this->newPath($paths,$children[$i]);
				}
			}
			$paths[]=$path;
			return $paths;
		}
		
		function getEndings()
		{
			$userProject = $_SESSION['user_project'];
			$sql=<<<SQL
SELECT *
FROM `ot_task`
WHERE `task_id` NOT IN 
(
	SELECT `task_dependancy_dependancy`
	FROM `ot_task_dependancy`
)
AND `task_project` = '$userProject';
SQL;
			$endings=$this->taskTable->query($sql);
			if(!is_array($endings)) $endings=array();
			return $endings;
		}
		
		function getChildren($task)
		{
			$children=$this->taskTable->getDependancies($task['task_id']);
			if(!is_array($children)) $children=array();
			return $children;
		}

		function getTimeBefore($id,$depth=0)
		{
			$timeBefore=0;
			$dependancies=$this->taskTable->getDependancies($id);
			if($dependancies){
				// add time of longest dependancy
				$longest=array('task_estimated_dev_time' => 0);
				foreach($dependancies as $dependancy)
				{
					// print '<div style="margin-left:'.(50*$depth).'"><pre>'.print_r($dependancy,true).'</pre></div>';
					if($dependancy['task_estimated_dev_time']>$longest['task_estimated_dev_time'])
						$longest = $dependancy;
				}
				$timeBefore+=$longest['task_estimated_dev_time']/60;
				$timeBefore+=$this->getTimeBefore($longest['task_id'],$depth+1);
			}
			// print "<br />TimeBefore:$timeBefore";
			return $timeBefore;
		}
	}
?>