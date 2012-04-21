<?php
	/**
	 * Good for HTML that came from the database and needs to be displayed.
	 */
	function cleanHTML($HTML)
	{
		// Tabs become 4 Spaces
		$HTML=str_replace("\t",'     ',$HTML);
		
		// <tags> become &lt;tags&gt;
		$HTML=htmlspecialchars($HTML);
		
		// 2 spaces become space then &nbsp;
		$HTML=str_replace('  ',' &nbsp;',$HTML);
		
		// New-Line's become <br /> Tags
		$HTML=nl2br($HTML);
		
		// strip slashes
		$HTML=stripslashes($HTML);
		
		return $HTML;
	}
	
	function stripPad($string, $length=40)
	{
		$string = strip_tags($string);
		if(strlen($string)>$length) $string = substr($string,0,$length-3).'...';
		else $string = str_pad($string,$length);
		return $string;
	}
	
	function displayMinutes($minutes=0, $return=false)
	{
		$hours = 0;
		while($minutes >= 60)
		{
			$hours++;
			$minutes -= 60;
		}
		if($return) return array('hours'=>$hours, 'minutes'=>$minutes);
		return sprintf("%02d:%02d", $hours, $minutes);
	}
?>