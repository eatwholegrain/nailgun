<?php
require("lib/bootstrap.php");
require(PATH."/lib/classes/ics/ics.class.php");

if (!empty($_GET["p"]) && empty($_GET["t"])) {

    $pid = $utilities->deobfuscate($utilities->filter($_GET["p"]));

    if ($pid > 0){

    	$project = $projects->getProject($pid);
        $allTasks = $tasks->listActiveTasks($pid);

        $ics = new ICS($project[0]["title"]);

        for ($i=0; $i < count($allTasks); $i++) {
            $ics->addEvent($allTasks[$i]["created"], $allTasks[$i]["expire"], $allTasks[$i]["title"], $allTasks[$i]["description"], ROOT."task.php?pid=".$project[0]["id"]."&tid=".$allTasks[$i]["id"]);
        }

        $ics->render();
    }
} else if (!empty($_GET["p"]) && !empty($_GET["t"])) {

    $pid = $utilities->deobfuscate($utilities->filter($_GET["p"]));
    $tid = $utilities->deobfuscate($utilities->filter($_GET["t"]));

    if ($pid > 0 && $tid > 0) {

        $project = $projects->getProject($pid);
        $task = $tasks->getTask($pid, $tid);

        $ics = new ICS($task[0]["title"]);

        $ics->addEvent($task[0]["created"], $task[0]["expire"], $project[0]["title"]." / ".$task[0]["title"], $task[0]["description"], ROOT."task.php?pid=".$project[0]["id"]."&tid=".$task[0]["id"]);

        $ics->render();
    }

} else {

    die("Nothing specified");
}
?>