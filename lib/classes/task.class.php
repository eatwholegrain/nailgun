<?php
/**
 * Tasks
 * @package tasks
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class Task extends Database {

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
    public function countOpenTasks($pid) {
        $query = "SELECT id FROM tasks WHERE project=".$pid." AND status=1";
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
    public function countUserActiveTasks($uid) {
        $query = "SELECT id FROM tasks WHERE assigned=".$uid." AND status=1";
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
    public function countUserOpenTasks($pid, $uid) {
        $query = "SELECT id FROM tasks WHERE project=".$pid." AND assigned=".$uid." AND status=1";
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
    public function countUserResolvedTasks($pid, $uid) {
        $query = "SELECT id FROM tasks WHERE project=".$pid." AND assigned=".$uid." AND status=2";
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
    public function countUserClosedTasks($pid, $uid) {
        $query = "SELECT id FROM tasks WHERE project=".$pid." AND assigned=".$uid." AND status=3";
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
    public function listAllTasks($account, $status) {
        $query = "SELECT * FROM tasks WHERE account=".$account." AND status=".$status." ORDER BY expire ASC, created DESC";
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
    public function listAllProjectTasks($pid, $stack = "ASC") {
        $query = "SELECT * FROM tasks WHERE project=".$pid." ORDER BY created $stack";
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
	public function listActiveTasks($pid) {
        $query = "SELECT * FROM tasks WHERE project=".$pid." AND status=1 ORDER BY expire ASC, created DESC";
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
    public function listResolvedTasks($pid) {
        $query = "SELECT * FROM tasks WHERE project=".$pid." AND status=2 ORDER BY completed DESC, created DESC";
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
    public function listClosedTasks($pid) {
        $query = "SELECT * FROM tasks WHERE project=".$pid." AND status=3 ORDER BY completed DESC, created DESC";
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
    public function listUserTasks($uid, $status, $order="expire", $sort="ASC") {
        $query = "SELECT * FROM tasks WHERE assigned=".$uid." AND status=".$status." ORDER BY ".$order." ".$sort.", created DESC";
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
    public function listUserProjectTasks($pid, $uid, $status) {
        $query = "SELECT * FROM tasks WHERE assigned=".$uid." AND project=".$pid." AND status=".$status." ORDER BY expire ASC, created DESC";
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
    public function listUserTasksByTerm($uid, $status, $term) {
        $query = "SELECT * FROM tasks WHERE assigned=".$uid." AND title LIKE '%$term%' AND status=".$status." ORDER BY title ASC, created DESC";
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
    public function getTask($pid, $tid) {
        $query = "SELECT * FROM tasks WHERE id=".$tid." AND project=".$pid." LIMIT 1";
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
    public function getTaskTitle($tid) {
        $query = "SELECT title FROM tasks WHERE id='".$tid."' LIMIT 1";
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
    public function getTaskStatus($tid) {
        $query = "SELECT status FROM tasks WHERE id=".$tid." LIMIT 1";
        $data = $this->select($query);

        if($data[0]["status"] == 0){
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
    public function getTaskPriority($tid) {
        $query = "SELECT priority FROM tasks WHERE id=".$tid." LIMIT 1";
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
    public function getAssignedTaskUser($pid, $tid) {
        $query = "SELECT assigned FROM tasks WHERE project=".$pid." AND id=".$tid." LIMIT 1";
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
    public function getCompletedTaskUser($pid, $tid) {
        $query = "SELECT finished FROM tasks WHERE project=".$pid." AND id=".$tid." LIMIT 1";
        $data = $this->select($query);
        return $data[0]["finished"];
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getTaskAuthor($pid, $tid) {
        $query = "SELECT author FROM tasks WHERE project=".$pid." AND id=".$tid." LIMIT 1";
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
    public function getTaskDateCreated($pid, $tid) {
        $query = "SELECT created FROM tasks WHERE project=".$pid." AND id=".$tid." LIMIT 1";
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
    public function getTaskDateExpired($pid, $tid) {
        $query = "SELECT expire FROM tasks WHERE project=".$pid." AND id=".$tid." LIMIT 1";
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
    public function createTask($account, $title, $description, $pid, $author, $assigned, $created, $expire, $priority, $private, $status) {
        $query = "INSERT INTO tasks (account, title, description, project, author, assigned, created, expire, priority, private, status) VALUES ($account, '$title', '$description', $pid, $author, $assigned, $created, $expire, $priority, $private, $status)";
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
    public function editTask($tid, $title, $description, $reassigned, $expire, $priority, $private, $status) {
        $query = "UPDATE tasks SET title='".$title."', description='".$description."', assigned=".$reassigned.", expire=".$expire.", priority=".$priority.", private=".$private.", status=".$status." WHERE id=".$tid;
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
    public function updateTask($tid, $reassigned, $expire, $priority, $status) {
        $query = "UPDATE tasks SET assigned=".$reassigned.", expire=".$expire.", priority=".$priority.", status=".$status." WHERE id=".$tid;
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
    public function completeTask($tid, $finished, $completed) {
        $query = "UPDATE tasks SET completed=".$completed.", finished=".$finished." WHERE id=".$tid;
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
    public function deleteTask() {
        
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function closeTask($pid, $tid) {
        $query = "UPDATE tasks SET status=0 WHERE id=".$tid." AND project=".$pid;
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
    public function closeAllProjectTasks($pid, $finished, $completed) {
        $query = "UPDATE tasks SET status=3, completed=".$completed.", finished=".$finished." WHERE project=".$pid;
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
    public function openTask($pid, $tid) {
        $query = "UPDATE tasks SET status=1 WHERE id=".$tid." AND project=".$pid;
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
    public function isTaskExpired($pid, $tid) {
        $query = "SELECT expire FROM tasks WHERE project=".$pid." AND id=".$tid." LIMIT 1";
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
    public function isTaskMine($pid, $tid, $uid) {
        $data = $this->getAssignedTaskUser($pid, $tid);
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
    public function isTaskAuthor($pid, $tid, $uid) {
        $data = $this->getTaskAuthor($pid, $tid);
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
    public function isTaskHasPriority($pid, $tid) {
        $query = "SELECT priority FROM tasks WHERE project=".$pid." AND id=".$tid." LIMIT 1";
        $data = $this->select($query);
        if($data[0]["priority"] == 1){
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
    public function isTaskPrivate($pid, $tid) {
        $query = "SELECT private FROM tasks WHERE project=".$pid." AND id=".$tid." LIMIT 1";
        $data = $this->select($query);
        if($data[0]["private"] == 1){
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
    public function isTaskEmpty($pid, $tid) {
        $query = "SELECT * FROM updates WHERE project=".$pid." AND task=".$tid." AND status=1";
        $data = $this->select($query);

        if ($this->getNumRows($data) == 0) {
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
    public function isTaskFilesEmpty($pid, $tid) {
        $query = "SELECT * FROM uploads WHERE project=".$pid." AND task=".$tid." AND status=1";
        $data = $this->select($query);

        if ($this->getNumRows($data) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if task belong to the account
     * @param number $tid Task ID.
     * @param number $account Account ID.
     * @return boolean true if task belong to the account or false if not.
     */
    public function isAccountTask($tid, $account) {
        $query = "SELECT account FROM tasks WHERE id=".$tid;
        $data = $this->select($query);

        if($data[0]["account"] == $account) {
            return true;
        } else {
            return false;
        }
    }
	
}