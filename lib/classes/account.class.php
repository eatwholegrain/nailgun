<?php
/**
 * User
 * @package accounts
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2013, Milan Trajkovic
 * @access public
 */

class Account extends Database {

    public $query;
    public $data;

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getAccount($account) {
        $query = "SELECT * FROM accounts WHERE id=".$account;
        $data = $this->select($query);
        return $data;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getAccountName($account) {
        $query = "SELECT * FROM accounts WHERE id=".$account." LIMIT 1";
        $data = $this->select($query);
        return $data[0]["title"]; 
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getAccountEmail($account) {
        $query = "SELECT * FROM accounts WHERE id=".$account." LIMIT 1";
        $data = $this->select($query);
        return $data[0]["email"]; 
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getAccountByEmail($email) {
        $query = "SELECT * FROM accounts WHERE email='".$email."' LIMIT 1";
        $data = $this->select($query);
        return $data;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function createAccount($title, $created, $status) {
        $query = "INSERT INTO accounts (title, created, status) VALUES ('$title', $created, $status)";
        $data = $this->insert($query);
        return $this->getId();
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function updateAccount($account, $title) {
        $query = "UPDATE accounts SET title='".$title."' WHERE id=".$account;
        $data = $this->insert($query);
        return $data;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function deleteAccount($account) {
        $query = "DELETE FROM accounts WHERE id=".$account;
        $data = $this->select($query);
        return $data;
    }
	
}