<?php
require("lib/bootstrap.php");
require(PATH."/lib/classes/rss.class.php");

if (!empty($_GET["p"]) && !empty($_GET["t"])) {

    $pid = $utilities->deobfuscate($utilities->filter($_GET["p"]));
    $tid = $utilities->deobfuscate($utilities->filter($_GET["t"]));

    if ($pid > 0 && $tid > 0) {

        $project = $projects->getProject($pid);
        $task = $tasks->getTask($pid, $tid);
        $taskUpdates = $updates->listAllTaskUpdates($pid, $tid);

        $feed = new RSS();
        $feed->title = $project[0]["title"]." / ".$task[0]["title"]." Latest updates";
        $feed->link = ROOT."";
        $feed->description = $task[0]["description"];
        
        for ($i=0; $i < count($taskUpdates); $i++) {
            
            $description = $taskUpdates[$i]["description"];
            $project = $taskUpdates[$i]["project"];
            $task = $taskUpdates[$i]["task"];
            $author = $taskUpdates[$i]["author"];
            $published = date("D, d M Y H:i:s", $taskUpdates[$i]["created"]);
            
            $html = "<p>".$description."</p><br/><br/>Published: ".$published;

            $item = new RSSItem();
            $item->title = $projects->getProjectTitle($taskUpdates[$i]["project"])." - ".$tasks->getTaskTitle($taskUpdates[$i]["task"]);
            $item->link = ROOT."task.php?pid=".$project."&tid=".$task;
            $item->setPubDate($taskUpdates[$i]["created"]); 
            $item->description = "<![CDATA[$html]]>";
            $feed->addItem($item);
        }

        echo $feed->serve();

    } else {
        die("Cannot access. You need access key");
    }

} else {
    die("Nothing specified");
}
?>