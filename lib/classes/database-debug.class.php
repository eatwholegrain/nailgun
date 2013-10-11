<?php
/**
 * Database
 * @package database
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class Database {

    protected $host = DB_HOST;
    protected $user = DB_USER;
    protected $pass = DB_PASS;
    protected $dbName = DB_NAME;  
    protected $error; 
    protected $inforResult;
    protected $numRows;
    protected $numCols;
    protected $id;
    protected $dataJson;
    protected $transmission;
    protected $sql;
    protected $converterUtf8 = false;
    protected $uppercase = false;
    protected $connection;
    
    
    function __construct(){

    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setDbName($dbName){
        if (strlen(trim($dbName)) > 0 ){
            $this->dbName = $dbName;
            return true;
        }
        else{
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
    public function getDbName(){
        return $this->dbName;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setHost($host){
        if (strlen(trim($host)) > 0 ){
            $this->host = $host;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getHost(){
        return $this->host;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setUser($user){
        if (strlen(trim($user)) > 0 ){
            $this->user = $user;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getDatabaseUser(){
        return $this->user;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setDatabasePassword($password){
        if (strlen(trim($password)) > 0 ){
            $this->pass = $password;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getError(){
        $error = print_r($this->error, true);
        return $error;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setUppercase($bool){
        if(is_bool($bool)){
            $this->uppercase = $bool;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getId(){
        return $this->id;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getSql(){
        return $this->sql;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getNumRows(){
        return $this->numRows;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getNumCols(){
        return $this->numCols;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    private function connect(){
        try{
            $this->connection = new PDO("mysql:host=".$this->host.";dbname=".$this->dbName, $this->user, $this->pass);
            return true;
        }
        catch (PDOException $e) {
            print "Error: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    private function logout(){
        $this->connection = null;
        $this->sql = null;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setUtf8($bool){
        if(is_bool($bool)){
            $this->converterUtf8 = $bool;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setSqlScript($sql, $complementation=false){

        $arrayExp[] = "/''/";
        $arrayExp[] = "/' '/";
        $arrayExp[] = "/\" \"/";
        $arrayExp[] = "/\"\"/";
        $arrayExp[] = "/\"null\"/";
        $arrayExp[] = "/\"NULL\"/";
        $arrayExp[] = "/'null'/";
        $arrayExp[] = "/'NULL'/";
        $sql = preg_replace($arrayExp, "null", $sql);
        $sql = preg_replace($arrayExp, "null", $sql);

        $arrayExp = null;
        $arrayExp[] = "/,[ \t\n\r\f\v]*,/";
        $arrayExp[] = "/,,/";
        $arrayExp[] = "/, ,/";
        $sql = preg_replace($arrayExp, ",\n null,", $sql);
        $sql = preg_replace($arrayExp, ",\n null,", $sql);

        $arrayExp = null;
        $arrayExp = "/=[ \t\n\r\f\v]*,/";
        $sql = preg_replace($arrayExp, "= null,", $sql);
        $sql = preg_replace($arrayExp, "= null,", $sql);
       
        
        if($complementation == false){
            $this->sql = null;
            $this->sql = $sql;
        }
        else{
            $this->sql .= $sql."; \n";
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function select($sql){

        $this->connect();

        if($this->connection === false){
            die("Connection error");
        } else {

            $this->setSqlScript($sql);
            $pdo = $this->connection;

            $db = $pdo->prepare($this->sql);

            $result = $db->execute();

            if($result === true){
                $data = $db->fetchAll(PDO::FETCH_ASSOC);

                $this->id = $pdo->lastInsertId();
                $this->numRows = $db->rowCount();
                $this->numCols = $db->columnCount();
                $pdo = null;
                
                // temporary fix
                $return = null;

                if($this->uppercase == false){
                    if(is_array($data)){
                        foreach($data as $key=> $reg){
                            foreach($reg as $campo=>$val){
                                $val = ($this->converterUtf8==true)?utf8_encode($val):$val;
                                $return[$key][$campo] = $val;
                            }
                        }
                    }
                    return $return;
                } else {
                    
                    if(is_array($data)){
                        foreach($data as $key=> $reg){
                            foreach($reg as $campo=>$val){
                                $val = ($this->converterUtf8==true)?utf8_encode(strtoupper($val)):strtoupper($val);
                                $returnUppercase[$key][$campo] = $val;
                            }
                        }
                    }

                    return $returnUppercase;
                }
            }else{

                $this->error = $db->errorInfo();
                $this->error['sql'] = $this->sql;
                return die($this->getError());
            }
            $this->logout();
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function insert($sql){
    
        $this->connect();
        if($this->connection === false){
            die("Connection error");
        }else{
            $sql = ($this->converterUtf8==true)?utf8_decode($sql):$sql;
            $this->setSqlScript($sql);
            $pdo = $this->connection;

            try {
                $transmission = $pdo->beginTransaction();
                if($transmission === true){
                    $db = $pdo->prepare($this->sql);

                    $result = $db->execute();
                    if($result===true){
                        $this->id = $pdo->lastInsertId(); 
                        $this->numRows = $db->rowCount();
                        $this->numCols = $db->columnCount();
                        
                        $commit = $pdo->commit();

                        if($commit === true){
                            $this->sql = null;
                            return $result;
                        }else{
                            $this->error = $db->errorInfo();
                            $this->error['sql'] = $this->sql;
                            return die($this->getError());
                        }
                    }else{
                        $this->error = $db->errorInfo();
                        $this->error['sql'] = $this->sql;
                        return die($this->getError());
                        $this->sql = null;
                    }

                    $pdo = null;
                    $this->sql = null;
                }
                else{
                    $pdo = null;
                    $this->error['sql'] = $this->sql;
                    return die($this->getError());
                    $this->sql = null;
                }
                $this->sql = null;

            }
            catch (PDOException $e) {
                $pdo->rollBack();
                $this->error = $db->errorInfo();
                $this->error['sql'] = $this->sql;
                $this->sql = null;
                return die("Failed: ".$e->getMessage().$this->getError());
            }

            $this->logout();
        }

    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function multInsert($sqlArray){

        $this->connect();
        if($this->connection === false){
            die("Connection error");
        }

        if(is_array($sqlArray)==false){
            die("sql array error");
        } else {

            $this->sql = null;
            $pdo = $this->connection;
            
            $Transaction = $pdo->beginTransaction();
            $Transaction = true;
            
            try {
               
                if($transmission === true){
                    foreach ($sqlArray as $sql) {
                        $this->setSqlScript($sql);
                        
                        $db = $pdo->prepare($this->sql);
                        $result  = $db->execute();

                        if($result===true){
                            $this->id = $pdo->lastInsertId();
                            $this->numRows = $db->rowCount();
                            $this->numCols = $db->columnCount();
                        }else{
                            $this->error = $db->errorInfo();
                            $this->error['sql'] = $this->sql;
                            return die($this->getError());
                        }
                    }

                    $commit = $pdo->commit();
                    if($commit === true){
                        $pdo = null;
                        return true;
                    }else{
                        $pdo->rollBack();
                        $this->error = $db->errorInfo();
                        $this->error['sql'] = $this->sql;
                        return die($this->getError());
                    }
                    
                }
                else{
                    $pdo = null;
                    $this->error['sql'] = $this->sql;
                    return die("Error: " .$this->getError());
                }

            }
            catch (PDOException $e) {
                $pdo->rollBack();
                $this->error = $db->errorInfo();
                $this->error['sql'] = $this->sql;
                return die("Failed: " . $e->getMessage().$this->getError());
                $pdo = null;
            }

            $this->logout();
        }

    }
}
?>