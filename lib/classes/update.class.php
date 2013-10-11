<?php
/**
 * Update
 * @package updates
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class Update extends Database {

    public $query;
    public $data;
    public $today;

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function countTasksUpdates($pid, $tid) {
        $query = "SELECT id FROM updates WHERE project=".$pid." AND task=".$tid." AND status=1";
        $data = $this->select($query);
        return $this->getNumRows($data);
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function countTodoUpdates($aid) {
        $query = "SELECT id FROM updates WHERE todo=".$aid." AND status=1";
        $data = $this->select($query);
        return $this->getNumRows($data);
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function listAllTaskUpdates($pid, $tid) {
        $query = "SELECT * FROM updates WHERE project=".$pid." AND task=".$tid." AND status=1";
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
    public function listAllTodoUpdates($aid) {
        $query = "SELECT * FROM updates WHERE todo=".$aid." AND status=1";
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
    public function getUpdate($uid) {
        
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getUpdateAuthor($uid) {
        $query = "SELECT author FROM updates WHERE id=".$uid." LIMIT 1";
        $data = $this->select($query);
        return $data[0]["author"];
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function isUpdateAuthor($uid, $userid) {
        $query = "SELECT author FROM updates WHERE id=".$uid." LIMIT 1";
        $data = $this->select($query);
        
        if ($data[0]["author"] == $userid) {
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
    public function createUpdate($account, $description, $projectId, $taskId, $todoId, $author, $assigned, $created, $status) {
        $query = "INSERT INTO updates (account, description, project, task, todo, author, assigned, created, status) VALUES ($account, '$description', $projectId, $taskId, $todoId, $author, $assigned, $created, $status)";
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
    public function updateUpdate() {
        
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function deleteUpdate($uid) {
        $query = "DELETE FROM updates WHERE id=".$uid;
        $data = $this->select($query);
        return $data;
    }
	
}