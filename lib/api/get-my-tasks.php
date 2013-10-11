<?php

	header("Content-type: application/json");

	require("bootstrap.php");

	if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

		if (empty($_GET['term'])) {
			exit;
		}

		$q = strtolower($_GET["term"]);

		if (get_magic_quotes_gpc()) {
			$q = stripslashes($q);
		}

        $allActiveUserTasks = $tasks->listUserTasksByTerm($session->get("userid"), 1, $q);

        $result = array();

        for ($i=0; $i < count($allActiveUserTasks); $i++) {

        	array_push($result, array("id"=>"task.php?tid=".$allActiveUserTasks[$i]["id"]."&pid=".$allActiveUserTasks[$i]["project"], "label"=>$allActiveUserTasks[$i]["title"], "value" => $allActiveUserTasks[$i]["title"]));
        }
        /*
        foreach ($items as $key=>$value) {

			if (strpos(strtolower($key), $q) !== false) {
				array_push($result, array("id"=>$value, "label"=>$key, "value" => strip_tags($key)));
			}

			if (count($result) > 11) {
				break;
			}
		}
		*/

	// json_encode is available in PHP 5.2 and above, or you can install a PECL module in earlier versions
	echo json_encode($result);

    } 
?>