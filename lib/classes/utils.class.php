<?php
/**
 * Utilities
 * @package utilities
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class Utils {

    public $string;
    public $content;
    public $now;
    public $today;
    public $yesterday;
    public $tommorow;
    public $past;
    public $seconds;
    public $minutes;
    public $hours;
    public $days;
    public $weeks;
    public $months;
    public $years;
    public $notice;
    public $channel;
    public $data;
    public $ip;
    public $response;
    public $patterns;
    public $ipInfo;
    public $ipData;
    public $normalChars;
    public $base;
    public $key;
    public $suffixes;


    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
	public function filter($string){
		if ((stristr("<[^>]*script*\"?[^>]*>", $string)) || (stristr("<[^>]*object*\"?[^>]*>", $string)) ||
        (stristr("<[^>]*iframe*\"?[^>]*>", $string)) || (stristr("<[^>]*applet*\"?[^>]*>", $string)) ||
        (stristr("<[^>]*meta*\"?[^>]*>", $string)) || (stristr("<[^>]*style*\"?[^>]*>", $string)) ||
        (stristr("<[^>]*form*\"?[^>]*>", $string)) || (stristr("\([^>]*\"?[^)]*\)", $string)) ||
        (stristr("\"", $string))) {
            $string = "";
        }
        //$string = str_replace("'", "`", $string);
        //$string = addslashes($string);
        //$string = htmlspecialchars($string, ENT_QUOTES);
        $string = str_replace (array('&', '"', "'", '<', '>', '�'), array('&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;', '&apos;' ), $string); 
        //$string = mysql_real_escape_string($string);
        return $string;
	}

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function replace($string, $search, $replace){
        $string = str_replace($search, $replace, $string);
        return $string;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function truncate($string, $limit){
        $content = explode(" ", $string, $limit);
        if (count($content) >= $limit) {
            array_pop($content);
            $content = implode(" ",$content).'...';
        } else {
            $content = implode(" ",$content);
        }   
        return $content;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function createLinks($string) {

        $pattern  = "#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#";

        $callback = create_function('$matches', '$url = array_shift($matches);
            $url_parts = parse_url($url);

            $text = parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH);
            $text = preg_replace("/^www./", "", $text);

            $last = -(strlen(strrchr($text, "/"))) + 1;
            /*
            if ($last < 0) {
                $text = substr($text, 0, $last) . "&hellip;";
            }
            */

           return sprintf(\'<a rel="nofollow" target="_blank" href="%s">%s</a>\', $url, $text);
       ');

        return preg_replace_callback($pattern, $callback, $string);
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function parseSmileys($string){ 
        
        $smileyList = array( 
            ':)' => '<img src="images/smileys/smile.png" alt=":)" />',
            ':-)' => '<img src="images/smileys/smile.png" alt=":)" />', 
            ':(' => '<img src="images/smileys/unhappy.png" alt=":(" />', 
            ':D' => '<img src="images/smileys/grin.png" alt=":D" />', 
            ':p' => '<img src="images/smileys/tongue.png" alt=":p" />',
            ';-)' => '<img src="images/smileys/wink.png" alt=";)" />',
            ':o' => '<img src="images/smileys/surprised.png" alt=":o" />'  
        ); 
        return str_ireplace(array_keys($smileyList), $smileyList, $string); 
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function singular($count) {
        return $sufix = ($count == 1) ? "" : "s";
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getDate(){
        $now = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
        return $now;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getToday(){
        $today = mktime(23, 59, 59, date("m"), date("d"), date("Y"));  
        return $today;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getTommorow(){
        $tommorow = mktime(23, 59, 59, date("m"), date("d")+1, date("Y")); 
        return $tommorow;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getYesterday(){
        $yesterday = mktime(23, 59, 59, date("m"), date("d")-1, date("Y")); 
        return $yesterday;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setExpirationTime($date){
        if ($date == -1){
            $date = 1924902000;
        } else {
            $date = explode("/", $date);
            $date = mktime(23, 59, 59, $date[0], $date[1], $date[2]); // included due day
        }
        return $date;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function formatDate($date, $format, $expiredPrefix="Expired"){
        if($date == 1924902000) {
            return "Whenever";
        } else if (date("d", $date) == date("d") && date("m", $date) == date("m") && date("Y", $date) == date("Y")) {
            return "TODAY";
        } else if (date("d", $date) == date("d")+1 && date("m", $date) == date("m") && date("Y", $date) == date("Y")) {
            return "Tommorow";
        } else if ($this->getDate() > $date){
            return $expiredPrefix." ".$this->elapsedTime($date);
            //return "Expired on ".date("d. F", $date);
        } else {
            return date($format, $date);
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function formatDateTime($date, $dateFormat, $timeeFormat){
        return date($dateFormat, $date). " at ".date($timeeFormat, $date);
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function formatRemainingDate($date, $format, $expiredPrefix="Expired"){
        if($date == 1924902000) {
            return "Whenever";
        } else if (date("d", $date) == date("d") && date("m", $date) == date("m") && date("Y", $date) == date("Y")) {
            return "TODAY";
        } else if (date("d", $date) == date("d")+1 && date("m", $date) == date("m") && date("Y", $date) == date("Y")) {
            return "Tommorow";
        } else if ($this->getDate() > $date){
            return $expiredPrefix." ".$this->elapsedTime($date);
            //return "Expired on ".date("d. F", $date);
        } else {
            $remaining = $this->remainingDate($this->getDate(), $date);
            return $remaining; 
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function elapsedTime($timestamp){ 
        //
        $past = time() - $timestamp; 
        $seconds = $past; 
        $minutes = round($past / 60);
        $hours = round($past / 3600); 
        $days = round($past / 86400); 
        $weeks = round($past / 604800); 
        $months = round($past / 2419200); 
        $years = round($past / 29030400); 

        if($seconds <= 60){
            return "before $seconds seconds"; 
        } else if($minutes <=60){
            if($minutes==1){
                return"minute before"; 
            } else {
                return"before $minutes minutes"; 
           }
        } else if($hours <=24){
           if($hours==1) {
                return"hour before";
           } else {
                return"before $hours hours";
          }
        } else if($days <=7){
            if($days==1){
                return"day before";
            } else {
                return"$days days ago";
            }
        } else if($weeks <=4){
            if($weeks==1){
                return"week before";
            } else {
                return"$weeks weeks ago";
            }
        } else if($months <=12){
            if($months==1){
                return"last month";
            } else {
                return"$months months ago";
            }   
        } else {
            if($years==1){
                return"last year";
            } else {
                return"$years years ago";
            }
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function remainingDate($fromTime, $toTime = null, $includeSeconds = false){ 
        //
        $toTime = $toTime? $toTime: time();

        $distanceInMinutes = floor(abs($toTime - $fromTime) / 60);
        $distanceInSeconds = floor(abs($toTime - $fromTime));

        $string = "";
        $parameters = array();

        if ($distanceInMinutes <= 1){
          
            if(!$includeSeconds){
                $string = $distanceInMinutes == 0 ? 'less than a minute' : '1 minute';
            } else {
                if($distanceInSeconds <= 5){
                    $string = 'less than 5 seconds';
                } else if ($distanceInSeconds >= 6 && $distanceInSeconds <= 10){            
                    $string = 'less than 10 seconds';
                } else if ($distanceInSeconds >= 11 && $distanceInSeconds <= 20){
                    $string = 'less than 20 seconds';
                } else if ($distanceInSeconds >= 21 && $distanceInSeconds <= 40){
                    $string = 'half a minute';
                } else if ($distanceInSeconds >= 41 && $distanceInSeconds <= 59){            
                    $string = 'less than a minute';
                } else {
                    $string = '1 minute';
                }
            }
        } else if ($distanceInMinutes >= 2 && $distanceInMinutes <= 44){
                $string = '%minutes% minutes';
                $parameters['%minutes%'] = $distanceInMinutes;
            } else if ($distanceInMinutes >= 45 && $distanceInMinutes <= 89){
                $string = 'about 1 hour';
            } else if ($distanceInMinutes >= 90 && $distanceInMinutes <= 1439){
                $string = 'about %hours% hours';
                $parameters['%hours%'] = round($distanceInMinutes / 60);
            } else if ($distanceInMinutes >= 1440 && $distanceInMinutes <= 2879){
                $string = '1 day';
            } else if ($distanceInMinutes >= 2880 && $distanceInMinutes <= 43199){
                $string = '%days% days';
                $parameters['%days%'] = round($distanceInMinutes / 1440);
            } else if ($distanceInMinutes >= 43200 && $distanceInMinutes <= 86399){  
                $string = 'about 1 month';
            } else if ($distanceInMinutes >= 86400 && $distanceInMinutes <= 525959){
                $string = '%months% months';
                $parameters['%months%'] = round($distanceInMinutes / 43200);
            } else if ($distanceInMinutes >= 525960 && $distanceInMinutes <= 1051919){
                $string = 'about 1 year';
            } else {
                $string = 'over %years% years';
                $parameters['%years%'] = floor($distanceInMinutes / 525960);
          }
          return strtr($string, $parameters);
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function isToday($date){
        $today = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
        if ($today == $date){
            return true;
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setColorClass($date){
        if($date == 1924902000) {
            return "whenever";
        } else if (date("d", $date) == date("d") && date("m", $date) == date("m") && date("Y", $date) == date("Y")) {
            return "today";
        } else if (date("d", $date) == date("d")+1 && date("m", $date) == date("m") && date("Y", $date) == date("Y")) {
            return "tommorow";
        } else if ($this->getDate() > $date) {
            return "expired";
        } else if ($this->getTommorow() < $date){
            return "future";
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function setUserColorClass($role){
        if($role == 1) {
            return "owner";
        } else if ($role == 2) {
            return "admin";
        } else if ($role == 3) {
            return "user";
        } else if ($role == 4) {
            return "client";
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function notify($notice="", $timeout=7){
        if (!empty($notice)){
         echo "$.achtung({message: '".$notice."', timeout: ".$timeout."});";
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function redirect($location="", $timeout=0){

        if ($timeout > 0) {
            if (!empty($location)){
                header("Refresh: $timeout; url=$location");
            }
        
        } else {

            if (!empty($location)){
                $self = $_SERVER['PHP_SELF'];
                $str2use = strrchr($self, '/');
                $length  = strlen($str2use) -1;
                @$fname  = substr($str2use, 1, $length);

                if ($fname != $location){
                    echo "<script>window.location.href='".$location."';</script>";
                }
            }

        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function lastActivity() {
        if(isset($_SESSION["start"])) {
            return $_SESSION["activity"] - $_SESSION["start"];
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function curl($url){
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER,1);
        $data = curl_exec($channel);
        curl_close($channel);
        return $data;
    }
    
    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getIp() {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) { 
          $ip = $_SERVER["HTTP_CLIENT_IP"];
        } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
          $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        return $ip;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function geoCheckIP($ip){
        if(!filter_var($ip, FILTER_VALIDATE_IP)){
            throw new InvalidArgumentException("IP is not valid");
        }
        
        $response = curl('http://www.netip.de/search?query='.$ip);    
        
        if (empty($response)){
            throw new InvalidArgumentException("Error contacting Geo-IP-Server");
        }
        
        $patterns = array();
        $patterns["domain"] = '#Domain: (.*?)&nbsp;#i';
        $patterns["country"] = '#Country: (.*?)&nbsp;#i';
        $patterns["state"] = '#State/Region: (.*?)<br#i';
        $patterns["town"] = '#City: (.*?)<br#i';
      
        $ipInfo = array();
     
        foreach ($patterns as $key => $pattern){
            $ipInfo[$key] = preg_match($pattern,$response,$value) && !empty($value[1]) ? $value[1] : 'not found';
        }
        
        $ipData = "City: ".$ipInfo['town']. ", State/Region: ".$ipInfo['state'].", Country: ".substr($ipInfo['country'], 4); 
        
        return $ipData;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function createFileName($file){
        setlocale(LC_ALL, "en_US.UTF8");
        $file = str_replace(array("'", "\""), "-", $file);
        $file = iconv("UTF-8", "ASCII//TRANSLIT", $file);
        $file = preg_replace("#[^a-zA-Z0-9.]+#", "-", $file);
        $file = preg_replace("#(-){2,}#", "$1", $file);
        $file = strtolower($file);
        $file = trim($file, "-");
        return $file;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function cleanUrl($url) {
        $normalChars = array(
            'C'=>'C', 'c'=>'c','C'=>'C', 'c'=>'c','Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','d'=>'d','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
            'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
            'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
            'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
            'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
            'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
            'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f', '?'=>'dz', '?'=>'lj', '?'=>'nj',
            '?'=>'a', '?'=>'b', '?'=>'v', '?'=>'g', '?'=>'d', '?'=>'dj', '?'=>'e', '?'=>'z', '?'=>'z', '?'=>'i', '?'=>'j',
            '?'=>'k', '?'=>'l', '?'=>'m', '?'=>'n', '?'=>'o', '?'=>'p', '?'=>'r', '?'=>'s', '?'=>'t', '?'=>'c', '?'=>'u', 
            '?'=>'f', '?'=>'h', '?'=>'c', '?'=>'c', '?'=>'m', '?'=>'DZ','?'=>'Lj','?'=>'Nj','?'=>'A', '?'=>'B', '?'=>'V', 
            '?'=>'G', '?'=>'D', '?'=>'DJ', '?'=>'E', '?'=>'Z', '?'=>'Z', '?'=>'I', '?'=>'J', '?'=>'K', '?'=>'L', '?'=>'M',
            '?'=>'N', '?'=>'O', '?'=>'P', '?'=>'R', '?'=>'S', '?'=>'T', '?'=>'C', '?'=>'U', '?'=>'F', '?'=>'H', '?'=>'C', 
            '?'=>'C', '?'=>'S'
        );    
        $url = str_replace(array_keys($normalChars),array_values($normalChars),$url);
        $url = trim(preg_replace('/[^a-zA-Z0-9\s]/isU', "", $url));
        return $url;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function isGet(){
        if($_SERVER["REQUEST_METHOD"] == "GET"){
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
    public function isPost(){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
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
    public function encrypt($string) {
        $encryption_key = ENC_KEY;
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, utf8_encode($string), MCRYPT_MODE_CBC, $iv);
        return $encrypted_string;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function decrypt($string) {
        $encryption_key = ENC_KEY;
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $string, MCRYPT_MODE_CBC, $iv);
        return $decrypted_string;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function obfuscate($string) {
        $key = ENC_KEY;
        $string = base_convert($string, 10, 36);
        $data = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $string, 'ecb');
        $data = bin2hex($data);
        return $data;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function deobfuscate($string) {
        $key = ENC_KEY;
        $data = pack('H*', $string); // Translate back to binary
        $data = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $data, 'ecb');
        $data = base_convert($data, 36, 10);
        return $data;
        }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function formatBytes($size, $precision=2) { 
        $base = log($size) / log(1024);
        $suffixes = array(' Bytes', ' KB', ' MB', ' GB', ' TB');   

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function getMaximumUpload() { 
        $maxUpload = (int)(ini_get('upload_max_filesize'));
        $maxPost = (int)(ini_get('post_max_size'));
        $memoryLimit = (int)(ini_get('memory_limit'));
        $maxFile = min($maxUpload, $maxPost, $memoryLimit);

        return $maxFile;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function reArrayFiles(&$file_post) { 
        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public static function log($data) { 
        $log = fopen("log/log.txt", "a+");
        fputs($log, $data);
        fclose($log);
    }

}