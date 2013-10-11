<?php

//error_reporting("E_ALL");

//require("configuration.php");

//require(PATH."/lib/classes/database-debug.class.php");

//$db = new Database();

//var_dump($db);

//echo $db->getDbName();

//echo $db->getHost();
//echo $db->connect();

/*
try {
	$connection = new PDO("mysql:host=134.0.76.16;dbname=nailguna_pp", "nailguna_usr", "ngapp#2012/");
	var_dump($connection);
}
	catch (PDOException $e) {
	print "Error: " . $e->getMessage() . "<br/>";
}
*/


//echo $db->getError();

require("lib/bootstrap.php");
//define("PATH", dirname(__FILE__));
//echo PATH;

//echo "<hr>";

//echo ROOT;

//echo "<hr>";

//echo phpversion();

//echo $utilities->getIp();

//phpinfo();

//echo "<hr>";

//var_dump($session->get("active"));

//echo "<hr>";

//var_dump($auth->isLogedIn());

//echo "<hr>";

//var_dump($session->get("userid"));

//echo "<hr>";

//var_dump($session->get("role"));

//echo "<hr>";

/*
echo session_id(). "<hr>"; 
foreach($_SESSION as $key => $value) { 
    echo $key . " = " . $value . "<br>"; 
}
*/
//echo "<hr>";
 //var_dump($session->isLogedIn());

/**/
//$notify = $notifications->taskUpdateNotify("milantrax@gmail.com", "Milan", "Project Title ".$i, "taskTitle", "taskAssignee", 1, $i, "milantrax@live.com");
//var_dump($notify);


/*
$notify = $notifications->taskChangeNotify("milantrax@gmail.com", "Milan", "Project Title ".$i, "taskTitle", "taskAssignee", 1, $i, "milantrax@live.com");
var_dump($notify);
*/

/*  
for($i=0; $i < 10; $i++) {
    $notify = $notifications->taskChangeNotify("milantrax@gmail.com", "Milan", "Project Title ".$i, "taskTitle", "taskAssignee", 1, $i, "milantrax@live.com");
    var_dump($notify);
}
*/ 

/**/
$newPassword = $auth->createHash("md5", "blank", HASH_PASSWORD_KEY);
echo $newPassword;


/*
$encrypted = $utilities->encrypt(123456879.3);
echo $encrypted;
$decrypted = $utilities->decrypt($encrypted);
echo $decrypted;
*/

/*
echo "<hr>";
$max_upload = (int)(ini_get('upload_max_filesize'));
$max_post = (int)(ini_get('post_max_size'));
$memory_limit = (int)(ini_get('memory_limit'));
$upload_mb = min($max_upload, $max_post, $memory_limit);

echo "upload_max_filesize: ".$max_upload." MB<br>";
echo "post_max_size: ".$max_post." MB<br>";
echo "memory_limit: ".$memory_limit." MB<br>";
echo "<hr>";
echo "max file size: ".$upload_mb." MB<br>";
*/

/*
require("lib/classes/phpmailer/class.phpmailer.php");

try {
    $mail = new PHPMailer(); //New instance, with exceptions enabled

    $body             = "<body>Hello</body>";
    $body             = preg_replace('/\\\\/','', $body); //Strip backslashes

    $mail->IsSMTP();                           // tell the class to use SMTP
    $mail->SMTPAuth   = false;                  // enable SMTP authentication
    $mail->Port       = 25;                    // set the SMTP server port
    $mail->Host       = "localhost"; // SMTP server
    $mail->Username   = "nailguna";     // SMTP server username
    $mail->Password   = "28Nail$";            // SMTP server password

    //$mail->IsSendmail();  // tell the class to use Sendmail

    $mail->AddReplyTo("admin@nailgunapp.com","NailGun");

    $mail->From       = "admin@nailgunapp.com";
    $mail->FromName   = "First Last";

    $to = "milantrax@gmail.com";

    $mail->AddAddress($to);

    $mail->Subject  = "First PHPMailer Message";

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 80; // set word wrap

    $mail->MsgHTML($body);

    $mail->IsHTML(true); // send as HTML

    $mail->Send();

    echo 'Message has been sent.';
} catch (phpmailerException $e) {
    echo $e->errorMessage();
}
*/

?>
<!--<img src="lib/classes/timthumb/timthumb.php?src=../../../uploads/project/2/13/1354725279-wireframes3-home.jpg&h=95&zc=1">-->