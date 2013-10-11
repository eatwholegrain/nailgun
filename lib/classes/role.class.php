<?php
/**
 * Role
 * @package roles
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class Role extends Database {

    public $query;
    public $data;
    public $isProjectManager;
    public $isProjectUser;
    public $isAssigned;
    public $isTaskAssigned;
    public $isTodoAssigned;

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function listRoles(){
       
	}

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getRole($pid, $uid){
        $query = "SELECT role FROM roles WHERE project=".$pid." AND user=".$uid." ORDER BY id DESC LIMIT 1";
        $data = $this->select($query);

        if($data[0]["role"] == 1 || $data[0]["role"] == 2){
            return $data[0]["role"];
        } else {
            return 0;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getRoleID($pid, $uid){
        $query = "SELECT id FROM roles WHERE project=".$pid." AND user=".$uid." ORDER BY id DESC LIMIT 1";
        $data = $this->select($query);
        return $data[0]["id"];
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function createRole($pid, $uid, $role){
        $query = "INSERT INTO roles (project, user, role) VALUES ($pid, $uid, $role)";
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
    public function updateRole($pid, $uid, $role){
        $query = "UPDATE roles SET role=".$role." WHERE project=".$pid." AND user=".$uid;
        $data = $this->insert($query);
        return $role;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function deleteRole($rid){
        $query = "DELETE FROM roles WHERE id=".$rid;
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
    public function deleteRoles($pid, $uid){
        $query = "DELETE FROM roles WHERE project=".$pid." AND user=".$uid;
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
    public function deleteAllUserRole($uid){
        $query = "DELETE FROM roles WHERE user=".$uid;
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
    public function deleteAllProjectRole($pid){
        $query = "DELETE FROM roles WHERE project=".$pid;
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
    public function isRoleSet($pid, $uid){
        $query = "SELECT role FROM roles WHERE project=".$pid." AND user=".$uid;
        $data = $this->select($query);
        if($this->getNumRows($data) > 0){
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
    public function isProjectManager($pid, $uid){
        $query = "SELECT role FROM roles WHERE project=".$pid." AND user=".$uid." ORDER BY id DESC LIMIT 1";
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
    public function getProjectManagers($pid){
        $query = "SELECT user FROM roles WHERE project=".$pid." AND role=1";
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
    public function isProjectUser($pid, $uid){
        $query = "SELECT role FROM roles WHERE project=".$pid." AND user=".$uid." ORDER BY id DESC LIMIT 1";
        $data = $this->select($query);

        if($data[0]["role"] == 2){
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
    public function getProjectUsers($pid){
        $query = "SELECT user FROM roles WHERE project=".$pid." AND role=2";
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
    public function canViewProject($pid, $uid){
        $isProjectManager = $this->isProjectManager($pid, $uid);
        $isProjectUser = $this->isProjectUser($pid, $uid);
        $isGlobalAdmin = $this->isGlobalAdmin($uid);

        if($isProjectManager || $isProjectUser || $isGlobalAdmin){
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
    public function isTaskAssigned($tid, $uid){
        $query = "SELECT id FROM tasks WHERE id=".$tid." AND assigned=".$uid." LIMIT 1";
        $data = $this->select($query);
        $isAssigned = $this->getNumRows($data);

        if($isAssigned > 0){
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
    public function canUpdateTask($pid, $tid, $uid){
        $isProjectManager = $this->isProjectManager($pid, $uid);
        $isProjectUser = $this->isProjectUser($pid, $uid);
        $isTaskAssigned = $this->isTaskAssigned($tid, $uid);
        $isGlobalAdmin = $this->isGlobalAdmin($uid);

        if($isProjectManager || $isProjectUser || $isTaskAssigned || $isGlobalAdmin){
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
    public function canChangeTask($pid, $tid, $uid){
        $isProjectManager = $this->isProjectManager($pid, $uid);
        $isTaskAssigned = $this->isTaskAssigned($tid, $uid);
        $isGlobalAdmin = $this->isGlobalAdmin($uid);

        if($isProjectManager || $isTaskAssigned || $isGlobalAdmin ){
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
    public function canCloseTask($pid, $tid, $uid){
        $isProjectManager = $this->isProjectManager($pid, $uid);
        $isGlobalAdmin = $this->isGlobalAdmin($uid);

        if($isProjectManager || $isGlobalAdmin ){
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
    public function isTodoAssigned($aid, $uid) {
        $query = "SELECT id FROM todos WHERE id=".$aid." AND assigned=".$uid." LIMIT 1";
        $data = $this->select($query);
        $isAssigned = $this->getNumRows($data);

        if($isAssigned > 0){
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
    public function isGlobalOwner($uid){
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
    public function isGlobalAdmin($uid){
        $query = "SELECT role FROM users WHERE id='".$uid."' LIMIT 1";
        $data = $this->select($query);

        if($data[0]["role"] == 1 || $data[0]["role"] == 2){
            return true;
        } else {
            return false;
        }
    }
	
}