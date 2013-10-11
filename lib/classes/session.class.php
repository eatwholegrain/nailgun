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
	public function init() {
		if(!session_start()) {
               /*
               ini_set("session.gc_maxlifetime", 60*60);
               ini_set("session.gc_probability",1);
               ini_set("session.gc_divisor",1);
               session_set_cookie_params(60*60);
               */
               session_start();
          }
	}
	
	/**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function set($key, $value) {
          $_SESSION[$key] = $value;
	}
	
	/**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function get($key) {
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
	}

     /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
     public function delete($key) {
          $_SESSION[$key] = "";
     }
	
	/**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function destroy() {
          $_SESSION["userid"] = NULL;
          $_SESSION["active"] = NULL;
		session_destroy();
	}

     /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
     public function regenerateSessionId() {
          @session_regenerate_id();
     }
	
}