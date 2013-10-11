<?php
/**
 * Session
 * @package session
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class Session {

	/**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function init(){
		//@session_start();
	}
	
	/**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function set($key, $value){
          //$_SESSION[$key] = $value;
          setcookie($key, $value, time()+3600, "/", "nailgunapp.com", 0);
	}
	
	/**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function get($key){
          /*
		if (isset($_SESSION[$key])){
			return $_SESSION[$key];
		}
          */
          if (isset($_COOKIE[$key])){
               return $_COOKIE[$key];
          }
	}

     /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
     public function delete($key){
          //$_SESSION[$key] = "";
          setcookie($key, "", time()+3600, "/", "nailgunapp.com", 0);
     }
	
	/**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function destroy(){
		//session_destroy();
          setcookie("userid", "", time()+3600, "/", "nailgunapp.com", 0);
          setcookie("firstname", "", time()+3600, "/", "nailgunapp.com", 0);
          setcookie("lastname", "", time()+3600, "/", "nailgunapp.com", 0);
          setcookie("username", "", ttime()+3600, "/", "nailgunapp.com", 0);
          setcookie("email", "", time()+3600, "/", "nailgunapp.com", 0);
          setcookie("role", "", time()+3600, "/", "nailgunapp.com", 0);
          setcookie("active", 0, time()+3600, "/", "nailgunapp.com", 0);
          setcookie("redirection", "", time()+3600, "/", "nailgunapp.com", 0);
	}

     /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
     public function regenerateSessionId(){
          session_regenerate_id();
     }

     /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
     public function isLogedIn(){
          /*
          if(isset($_SESSION["active"]) && !empty($_SESSION["active"])) {
               return true;
          } else {
               return false;
          }
          */
          if(isset($_COOKIE["active"]) && !empty($_COOKIE["active"]) && $_COOKIE["active"]=="1") {
               return true;
          } else {
               return false;
          }
          
     }
	
}