<?php

	require("bootstrap.php");

	if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

		$activity = $utilities->lastActivity();

		echo $activity;

    } 
?>