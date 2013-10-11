<?php

require("../../configuration.php");

if (!defined("PATH")) {
  exit("Critical error: Cannot proceed without application path defined.");
}

require(PATH."/lib/classes/database.class.php");
require(PATH."/lib/classes/auth.class.php");
require(PATH."/lib/classes/session.class.php");
require(PATH."/lib/classes/utils.class.php");
require(PATH."/lib/classes/project.class.php");
require(PATH."/lib/classes/task.class.php");
require(PATH."/lib/classes/update.class.php");
require(PATH."/lib/classes/user.class.php");
require(PATH."/lib/classes/role.class.php");
require(PATH."/lib/classes/upload.class.php");
require(PATH."/lib/classes/phpmailer/class.phpmailer.php");
require(PATH."/lib/classes/notification.class.php");
require(PATH."/lib/classes/search.class.php");

$db = new Database();
$auth = new Auth();
$session = new Session();
$utilities = new Utils();
$projects = new Project();
$tasks = new Task();
$updates = new Update();
$users = new User();
$roles = new Role();
$searches = new Search();

$session->init();

$notice = "";
?>