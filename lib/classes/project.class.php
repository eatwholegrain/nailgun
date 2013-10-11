<?php
/**
 * Project
 * @package projects
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class Project extends Database {

    public $query;
    public $data;
    public $today;

    /**
     * List all projects
     * @return array Array with project data.
     */
	public function listAllProjects($account = 0) {
        $query = "SELECT * FROM projects WHERE account=".$account." AND status > 0 ORDER BY title ASC";
        $data = $this->select($query);
        return $data;
	}

    /**
     * List all projects by specified status
     * @param number $status Number defining current project status.
     * @return array Array with project data.
     */
    public function listProjectsByStatus($account = 0, $status) {
        $query = "SELECT * FROM projects WHERE account=".$account." AND status=".$status." ORDER BY expire ASC, created DESC";
        $data = $this->select($query);
        return $data;
    }

    /**
     * List project assigned to specific user
     * @param number $uid User ID.
     * @return array Array with project data.
     */
    public function listUserProjects($uid) {
        $query = "SELECT * FROM projects WHERE id='".$uid."' AND status > 0 ORDER BY expire ASC, created DESC";
        $data = $this->select($query);
        return $data;
    }

    /**
     * Get specific project data
     * @param number $pid Project ID.
     * @return array Array with project data.
     */
    public function getProject($pid) {
        $query = "SELECT * FROM projects WHERE id='".$pid."' LIMIT 1";
        $data = $this->select($query);
        return $data;
    }

    /**
     * Get specific project ID
     * @param number $pid Project ID.
     * @return number Project ID.
     */
    public function getProjectId($pid) {
        $query = "SELECT id FROM projects WHERE id='".$pid."' LIMIT 1";
        $data = $this->select($query);
        return $data[0]["id"];
    }

    /**
     * Get project title
     * @param number $pid Number desc.
     * @return string Project title.
     */
    public function getProjectTitle($pid) {
        $query = "SELECT title FROM projects WHERE id='".$pid."' LIMIT 1";
        $data = $this->select($query);
        return $data[0]["title"];
    }

    /**
     * Get project status
     * @param number $pid Number desc.
     * @return string Project status.
     */
    public function getProjectStatus($pid) {
        $query = "SELECT status FROM projects WHERE id='".$pid."' LIMIT 1";
        $data = $this->select($query);

        if($data[0]["status"] == 0){
            return "CLOSED";
        } else if ($data[0]["status"] == 1){
            return "OPEN";
        } else {
            return "UNKNOWN";
        }
    }

    /**
     * Create new project
     * @param string $title Project title.
     * @param string $description Project description.
     * @param number $author User ID.
     * @param number $created Unix timestamp for current date.
     * @param number $expire Unix timestamp for expiration date.
     * @param number $status Status number.
     * @return number Project ID.
     */
    public function createProject($account, $title, $description, $author, $created, $expire, $status) {
        $query = "INSERT INTO projects (account, title, description, author, created, expire, status) VALUES ($account, '$title', '$description', $author, $created, $expire, $status)";
        $data = $this->insert($query);
        return $this->getId();
    }

    /**
     * Update project
     * @param number $pid Project ID.
     * @param string $title Project title.
     * @param string $description Project description.
     * @param number $author User ID.
     * @param number $created Unix timestamp for current date.
     * @param number $expire Unix timestamp for expiration date.
     * @param number $status Status number.
     * @return boolean true if success or false if failed.
     */
    public function editProject($pid, $title, $description, $expire) {
        $query = "UPDATE projects SET title='".$title."', description='".$description."', expire=".$expire." WHERE id=".$pid;
        $data = $this->insert($query);
        return $data;
    }

    /**
     * Delete project
     * @param number $pid Project ID.
     * @return boolean true if successfull or false.
     */
    public function deleteProject($pid){
        
    }

    /**
     * Close project
     * @param number $pid Project ID.
     * @param number $finished User ID.
     * @param number $completed Unix timestamp for current date.
     * @return boolean true if success or false if failed.
     */
    public function closeProject($pid, $finished, $completed) {
        $query = "UPDATE projects SET status=0, completed=".$completed.", finished=".$finished." WHERE id=".$pid;
        $data = $this->insert($query);
        return $data;
    }

    /**
     * Open project
     * @param number $pid Project ID.
     * @return boolean true if success or false if failed.
     */
    public function openProject($pid) {
        $query = "UPDATE projects SET status=1 WHERE id=".$pid;
        $data = $this->insert($query);
        return $data; 
    }

    /**
     * Check if project has open status
     * @param number $pid Project ID.
     * @return boolean true if open or false if closed or disabled.
     */
    public function isProjectOpen($pid) {
        $query = "SELECT status FROM projects WHERE id=".$pid." LIMIT 1";
        $data = $this->select($query);

        if($data[0]["status"] == 1){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if project has closed status
     * @param number $pid Project ID.
     * @return boolean true if closed or false if open.
     */
    public function isProjectClosed($pid) {
        $query = "SELECT status FROM projects WHERE id=".$pid." LIMIT 1";
        $data = $this->select($query);

        if($data[0]["status"] == 0){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if project expired
     * @param number $pid Project ID.
     * @return boolean true if expired or false if active.
     */
    public function isProjectExpired($pid) {
        $query = "SELECT expire FROM projects WHERE id=".$pid." LIMIT 1";
        $data = $this->select($query);
        $today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

        if($data[0]["expire"] < $today) {
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
    public function isProjectEmpty($pid) {
        $query = "SELECT id FROM tasks WHERE project=".$pid;
        $data = $this->select($query);

        if ($this->getNumRows($data) == 0) {
            return true;
        } else {
            return false;
        }
    }
	
}