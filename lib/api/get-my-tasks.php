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

		echo json_encode($result);
    } 
?>