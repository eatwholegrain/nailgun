<?php
/**
 * Uploads
 * @package uploads
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class Upload extends Database {

    public $query;
    public $data;
    public $uploadfile;
    public $deleting;

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getUpload($fid){
        $query = "SELECT * FROM uploads WHERE id=".$fid." AND status=1 LIMIT 1";
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
    public function getTaskUploads($pid, $tid) {
        $query = "SELECT * FROM uploads WHERE project=".$pid." AND task=".$tid." AND comment=0 AND status=1";
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
    public function hasTaskHaveUploads($pid, $tid) {
        $query = "SELECT * FROM uploads WHERE project=".$pid." AND task=".$tid." AND status=1";
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
    public function getTodoUploads($aid) {
        $query = "SELECT * FROM uploads WHERE todo=".$aid." AND comment=0 AND status=1";
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
    public function hasTodoHaveUploads($aid) {
        $query = "SELECT * FROM uploads WHERE todo=".$aid." AND status=1";
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
    public function getUpdateUploads($pid, $tid, $uid) {
        $query = "SELECT * FROM uploads WHERE project=".$pid." AND task=".$tid." AND comment=".$uid." AND status=1";
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
    public function getUpdateUpload($pid, $tid, $uid, $fid) {
        $query = "SELECT * FROM uploads WHERE project=".$pid." AND task=".$tid." AND comment=".$uid." AND id=".$fid." AND status=1 LIMIT 1";
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
    public function uploadTaskFile($pid, $tid, $fileName, $tempName) {
        $this->createFolder(UPLOAD."project/".$pid."/".$tid, 0777);
        $uploadfile = UPLOAD."project/".$pid."/".$tid."/".$fileName;

        if (move_uploaded_file($tempName, $uploadfile)) {
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
    public function uploadTodoFile($aid, $fileName, $tempName) {
        $this->createFolder(UPLOAD."todo/".$aid, 0777);
        $uploadfile = UPLOAD."todo/".$aid."/".$fileName;

        if (move_uploaded_file($tempName, $uploadfile)) {
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

    public function createUpload($account, $fileName, $fileIdentifier, $fileSize, $fileType, $filePath, $pid, $tid, $aid, $uid, $author, $created, $status) {
        $query = "INSERT INTO uploads (account, name, identifier, size, type, path, project, task, todo, comment, author, created, status) VALUES ($account, '$fileName', '$fileIdentifier', $fileSize, '$fileType', '$filePath', $pid, $tid, $aid, $uid, $author, $created, $status)";
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
    public function deleteUploads($pid, $tid, $uid) {
        $query = "DELETE FROM uploads WHERE project=".$pid." AND task=".$tid." AND comment=".$uid;
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
    public function deleteUpload($fid) {
        $query = "DELETE FROM uploads WHERE id=".$fid;
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
    public function createFolder($path, $permission=0777) {
        if(!is_dir($path)) {
            $create = mkdir($path, $permission, true);
            return $create;
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
    public function deleteFolder($path) {
        if(is_dir($path)) {
            $deleting = rmdir($path);
            return $deleting;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setPermission($path, $permission) {
        chmod($path, $permission);
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function isFolderExist($path) {
        if(is_dir($path)) {
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
    public function getFileType($fid) {
        $query = "SELECT * FROM uploads WHERE id=".$fid." AND status=1 LIMIT 1";
        $data = $this->select($query);
        $subtipe = explode("/", $data[0]["type"]);
        return $subtipe[0];
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getFileSize($fid) {
        $query = "SELECT * FROM uploads WHERE id=".$fid." AND status=1 LIMIT 1";
        $data = $this->select($query);

        $converted = log($data[0]["size"]) / log(1024);
        $metrics = array(' Bytes', ' KB', ' MB', ' GB', ' TB');   
        return round(pow(1024, $converted - floor($converted)), 2) . $metrics[floor($converted)];
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getFileExtension($fid) {
        $query = "SELECT * FROM uploads WHERE id=".$fid." AND status=1 LIMIT 1";
        $data = $this->select($query);
        $fileParts = pathinfo($data[0]["name"]);
        return $fileParts["extension"];
    }
	
}