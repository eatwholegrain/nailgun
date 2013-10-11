<?php
/**
 * To do
 * @package todos
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2013, Milan Trajkovic
 * @access public
 */

class Todo extends Database {

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
    public function countTodos() {
        $query = "SELECT id FROM todos WHERE status=1";
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
    public function countUserTodos($uid) {
        $query = "SELECT id FROM todos WHERE assigned=".$uid." AND status=1";
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
    public function listAllTodos($account, $status) {
        $query = "SELECT * FROM todos WHERE account=".$account." AND status=".$status." ORDER BY expire ASC, created DESC";
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
    public function listUserTodos($account, $uid, $status) {
        $query = "SELECT * FROM todos WHERE account=".$account." AND assigned=".$uid." AND status=".$status." ORDER BY expire ASC, created DESC";
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
    public function getTodo($aid) {
        $query = "SELECT * FROM todos WHERE id=".$aid." LIMIT 1";
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
    public function getTodoTitle($aid) {
        $query = "SELECT title FROM todos WHERE id='".$aid."' LIMIT 1";
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
    public function getTodoStatus($aid) {
        $query = "SELECT status FROM todos WHERE id=".$aid." LIMIT 1";
        $data = $this->select($query);

        if($data[0]["status"] == 0) {
            return "DISABLED";
        } else if ($data[0]["status"] == 1){
            return "OPEN";
        } else if ($data[0]["status"] == 2){
            return "RESOLVED";
        } else if ($data[0]["status"] == 3){
            return "COMPLETE";
        } else {
            return "UNKNOWN";
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getTodoPriority($aid) {
        $query = "SELECT priority FROM todos WHERE id=".$aid." LIMIT 1";
        $data = $this->select($query);
        return $data[0]["priority"];
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getAssignedTodoUser($aid) {
        $query = "SELECT assigned FROM todos WHERE id=".$aid." LIMIT 1";
        $data = $this->select($query);
        return $data[0]["assigned"];
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getTodoAuthor($aid) {
        $query = "SELECT author FROM todos WHERE id=".$aid." LIMIT 1";
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
    public function getTodoDateCreated($aid) {
        $query = "SELECT created FROM todos WHERE id=".$aid." LIMIT 1";
        $data = $this->select($query);
        return $data[0]["created"];
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getTodoDateExpired($aid) {
        $query = "SELECT expire FROM todos WHERE id=".$aid." LIMIT 1";
        $data = $this->select($query);
        return $data[0]["expire"];
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function createTodo($account, $title, $description, $author, $assigned, $created, $expire, $priority, $status) {
        $query = "INSERT INTO todos (account, title, description, author, assigned, created, expire, priority, status) VALUES ($account, '$title', '$description', $author, $assigned, $created, $expire, $priority, $status)";
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
    public function editTodo($aid, $title, $description, $reassigned, $expire, $priority, $status) {
        $query = "UPDATE todos SET title='".$title."', description='".$description."', assigned=".$reassigned.", expire=".$expire.", priority=".$priority.", status=".$status." WHERE id=".$aid;
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
    public function updateTodo($aid, $reassigned, $expire, $priority, $status) {
        $query = "UPDATE todos SET assigned=".$reassigned.", expire=".$expire.", priority=".$priority.", status=".$status." WHERE id=".$aid;
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
    public function completeTodo($aid, $finished, $completed) {
        $query = "UPDATE todos SET completed=".$completed.", finished=".$finished." WHERE id=".$aid;
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
    public function deleteTodo() {
        
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function closeTodo($aid) {
        $query = "UPDATE todos SET status=0 WHERE id=".$tid;
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
    public function closeAllUserTodos($uid, $finished, $completed) {
        $query = "UPDATE todos SET status=3, completed=".$completed.", finished=".$finished." WHERE assigned=".$uid;
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
    public function openTodo($aid) {
        $query = "UPDATE todos SET status=1 WHERE id=".$aid;
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
    public function isTodoExpired($aid) {
        $query = "SELECT expire FROM todos WHERE id=".$aid." LIMIT 1";
        $data = $this->select($query);
        $today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        if($data[0]["expire"] < $today){
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
    public function isTodoMine($aid, $uid) {
        $data = $this->getAssignedTodoUser($aid);
        if($data == $uid){
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
    public function isTodoHasPriority($aid) {
        $query = "SELECT priority FROM todos WHERE id=".$aid." LIMIT 1";
        $data = $this->select($query);
        if($data[0]["priority"] == 1){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if project has no task
     * @param number $pid Project ID.
     * @return boolean true if project empty or false if has tasks.
     */
    public function isTodoEmpty($aid) {
        $query = "SELECT * FROM updates WHERE todo=".$aid." AND status=1";
        $data = $this->select($query);

        if ($this->getNumRows($data) == 0) {
            return true;
        } else {
            return false;
        }
    }
	
}