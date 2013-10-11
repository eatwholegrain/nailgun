<?php
/**
 * Authentification
 * @package authentifications
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class Auth extends Database {

    public $algo;
    public $data;
    public $context;
    public $token;
    public $alphabet;
    public $passphrase;
    public $alphaLength;


    /**
     * Create hash
     * @param string $algo Algoritm used.
     * @param string $data String to be hashed.
     * @param string $salt Additional salt for crypting.
     * @return string Hashed string.
     */
	public function createHash($algo="md5", $data, $salt) {
		$context = hash_init($algo, HASH_HMAC, $salt);
		hash_update($context, $data);
		return hash_final($context);
	}

    /**
     * Check if user exist in database and return user data
     * @param string $username User username.
     * @param string $password User password.
     * @return array Array with all user data.
     */
    public function login($username, $password) {
        $query  = "SELECT * FROM users WHERE username='".$username."' AND password='".$password."' AND status > 0 LIMIT 1";
        $data = $this->select($query);  
        return $data;
    }

    /**
     * Reset user password
     * @param number $uid User specific ID number.
     * @param string $password Password to set for user.
     * @return boolean true if update successfull or false if failed.
     */
    public function resetPassword($uid, $password) {
        $query = "UPDATE users SET password='".$password."' WHERE id=".$uid;
        $data = $this->select($query);  
        return $data;
    }

    /**
     * Create random password string
     * @return string Random password string
     */
    public function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $passphrase = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $passphrase[] = $alphabet[$n];
        }
        return implode($passphrase);
    }

    /**
     * Create token string
     * @param string $password Password to use for token.
     * @return string Random token string
     */
    public function tokenize($password) {
        $token = md5($password.$_SERVER['REMOTE_ADDR'].$this->hashString);
        return $token;  
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
     public function isLogedIn() {
          if(isset($_SESSION["active"]) && !empty($_SESSION["active"]) && intval($_SESSION["active"]) > 0 && isset($_SESSION["account"]) && !empty($_SESSION["account"]) && intval($_SESSION["account"]) > 0) {
               return true;
          } else {
               return false;
          }
          
     }
	
}