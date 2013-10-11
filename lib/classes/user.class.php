<?php
/**
 * User
 * @package users
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class User extends Database {

    public $query;
    public $data;

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function listAllUsers($account, $sort = "role") {
        $query = "SELECT * FROM users WHERE account=".$account." AND status > 0 ORDER BY $sort ASC, created ASC";
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
    public function listSpecificUsers($uid, $sort = "role") {
        $query = "SELECT * FROM users WHERE id=".$uid." AND status > 0 ORDER BY $sort ASC, created ASC";
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
    public function listAllProjectUsers($pid) {
        $query = "SELECT user FROM roles WHERE project=".$pid;
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
    public function getUser($uid) {
        $query = "SELECT * FROM users WHERE id=".$uid;
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
    public function getUserStatus($uid) {
        $query = "SELECT role FROM users WHERE id='".$uid."' LIMIT 1";
        $data = $this->select($query);

        if($data[0]["role"] == 0){
            return "Disabled";
        } else if ($data[0]["role"] == 1){
            return "Owner";
        } else if ($data[0]["role"] == 2){
            return "Admin";
        } else if ($data[0]["role"] == 3){
            return "User";
        } else {
            return "Unknown";
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getFullUserName($uid) {
        $query = "SELECT * FROM users WHERE id=".$uid." LIMIT 1";
        $data = $this->select($query);
        return $data[0]["firstname"]." ".$data[0]["lastname"]; 
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getShortUserName($uid) {
        $query = "SELECT * FROM users WHERE id=".$uid." LIMIT 1";
        $data = $this->select($query);
        return $data[0]["firstname"]." ".$this->formatUserLastName($data[0]["lastname"]); 
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getUserFirstName($uid) {
        $query = "SELECT firstname FROM users WHERE id=".$uid." LIMIT 1";
        $data = $this->select($query);
        return $data[0]["firstname"]; 
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getUserEmail($uid) {
        $query = "SELECT email FROM users WHERE id=".$uid." LIMIT 1";
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
    public function getUserByEmail($email) {
        $query = "SELECT * FROM users WHERE email='".$email."' LIMIT 1";
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
    public function createUser($firstname, $lastname, $email, $username, $password, $account, $created, $role, $status) {
        $query = "INSERT INTO users (firstname, lastname, email, username, password, account, created, role, status) VALUES ('$firstname', '$lastname', '$email', '$username', '$password', $account, $created, $role, $status)";
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
    public function updateUser($uid, $firstname, $lastname, $email, $username, $role) {
        $query = "UPDATE users SET firstname='".$firstname."', lastname='".$lastname."', email='".$email."', username='".$username."', role=".$role." WHERE id=".$uid;
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
    public function editUser($uid, $firstname, $lastname, $email, $username) {
        $query = "UPDATE users SET firstname='".$firstname."', lastname='".$lastname."', email='".$email."', username='".$username."' WHERE id=".$uid;
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
    public function deleteUser($uid) {
        $query = "DELETE FROM users WHERE id=".$uid;
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
    public function isOwner($uid) {
        $query = "SELECT role FROM users WHERE id='".$uid."' LIMIT 1";
        $data = $this->select($query);

        if($data[0]["role"] == 1){
            return true;
        } else {
            return false;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function isAdmin($uid) {
        $query = "SELECT role FROM users WHERE id='".$uid."' LIMIT 1";
        $data = $this->select($query);

        if($data[0]["role"] == 1 || $data[0]["role"] == 2){
            return true;
        } else {
            return false;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function isUser($uid) {
        $query = "SELECT role FROM users WHERE id=".$uid." LIMIT 1";
        $data = $this->select($query);

        if($data[0]["role"] == 3 || $data[0]["role"] == 2 || $data[0]["role"] == 1){
            return true;
        } else {
            return false;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function formatUserLastName($lastname){
        return substr($lastname, 0, 1).".";
    }
	
}