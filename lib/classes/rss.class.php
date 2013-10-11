<?php
/**
 * Rss
 * @package rss
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class RSS {

    public $title;
    public $link;
    public $description;
    public $language = "en-us";
    public $pubDate;
    public $items;
    public $tags;

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function RSS(){
        $this->items = array();
        $this->tags = array();
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function addItem($item){
        $this->items[] = $item;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setPubDate($when){
        if(strtotime($when) == false){
            $this->pubDate = date("D, d M Y H:i:s ", $when)."GMT";
        } else {
            $this->pubDate = date("D, d M Y H:i:s ", strtotime($when))."GMT";
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getPubDate(){
        if(empty($this->pubDate)){
            return date("D, d M Y H:i:s ")."GMT";
        } else {
            return $this->pubDate;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function addTag($tag, $value){
        $this->tags[$tag] = $value;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function out(){
        $out  = $this->header();
        $out .= "<channel>\n";
        $out .= "<title>" . $this->title . "</title>\n";
        $out .= "<link>" . $this->link . "</link>\n";
        $out .= "<description>" . $this->description . "</description>\n";
        $out .= "<language>" . $this->language . "</language>\n";
        $out .= "<pubDate>" . $this->getPubDate() . "</pubDate>\n";

        foreach($this->tags as $key => $val) $out .= "<$key>$val</$key>\n";
        foreach($this->items as $item) $out .= $item->out();

        $out .= "</channel>\n";
        
        $out .= $this->footer();

        $out = str_replace("&", "&amp;", $out);

        return $out;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function serve($contentType = "application/xml"){
        $xml = $this->out();
        header("Content-type: $contentType");
        echo $xml;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function header(){
        $out  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $out .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n";
        return $out;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function footer(){
        return '</rss>';
    }
}

class RSSItem {

    public $title;
    public $link;
    public $description;
    public $pubDate;
    public $guid;
    public $tags;
    public $attachment;
    public $length;
    public $mimetype;

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function RSSItem(){
        $this->tags = array();
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setPubDate($when){
        if(strtotime($when) == false){
            $this->pubDate = date("D, d M Y H:i:s ", $when)."GMT";
        } else {
            $this->pubDate = date("D, d M Y H:i:s ", strtotime($when))."GMT";
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getPubDate(){
        if(empty($this->pubDate)){
            return date("D, d M Y H:i:s ")."GMT";
        } else {
            return $this->pubDate;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function addTag($tag, $value){
        $this->tags[$tag] = $value;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function out(){
        $out = "";
        $out .= "<item>\n";
        $out .= "<title>" . $this->title . "</title>\n";
        $out .= "<link>" . $this->link . "</link>\n";
        $out .= "<description>" . $this->description . "</description>\n";
        $out .= "<pubDate>" . $this->getPubDate() . "</pubDate>\n";

        if($this->attachment != ""){
            $out .= "<enclosure url='{$this->attachment}' length='{$this->length}' type='{$this->mimetype}' />";
        }

        if(empty($this->guid)) { 
            $this->guid = $this->link;
            $out .= "<guid>" . $this->guid . "</guid>\n";
        }

        foreach($this->tags as $key => $val) $out .= "<$key>$val</$key\n>";
        $out .= "</item>\n";
        return $out;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function enclosure($url, $mimetype, $length){
        $this->attachment = $url;
        $this->mimetype = $mimetype;
        $this->length = $length;
    }
}
?>