<?php
/**
 * Calendar
 * @package ics
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class ICS {

	protected $calendarName;
	protected $events = array();
	

	/**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */	
	public function __construct($calendarName=""){
		$this->calendarName = $calendarName;
	}


	/**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function addEvent($start, $end, $summary="", $description="", $url=""){
		$this->events[] = array(
			"start" => $start,
			"end" => $end,
			"summary" => $summary,
			"description" => $description,
			"url" => $url
		);
	}
	
	/**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function render(){	
		$ics = "";
		$ics .= "BEGIN:VCALENDAR
METHOD:PUBLISH
VERSION:2.0
X-WR-CALNAME:".$this->calendarName."
PRODID:-//hacksw/handcal//NONSGML v1.0//EN";
		foreach($this->events as $event){
			$ics .= "
BEGIN:VEVENT
UID:". md5(uniqid(mt_rand(), true)) ."@nailgunapp.com
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
DTSTART:".gmdate('Ymd', $event["start"])."T".gmdate('His', $event["start"])."Z
DTEND:".gmdate('Ymd', $event["end"])."T".gmdate('His', $event["end"])."Z
SUMMARY:".str_replace("\n", "\\n", $event['summary'])."
DESCRIPTION:".str_replace("\n", "\\n", $event['description'])."
URL;VALUE=URI:".$event['url']."
END:VEVENT";
		}
		$ics .= "
END:VCALENDAR";
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: inline; filename='.$this->calendarName.'.ics');
		echo $ics;

	}
}