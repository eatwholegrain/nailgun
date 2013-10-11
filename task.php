<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if (!empty($_GET["pid"]) && !empty($_GET["tid"])) {

            $pid = $utilities->filter($_GET["pid"]);
            $tid = $utilities->filter($_GET["tid"]);

            $project = $projects->getProject($pid);
            $task = $tasks->getTask($pid, $tid);

            $taskTitle = $task[0]["title"];
            $taskDescription = $task[0]["description"];

            $firstAssigned = $task[0]["assigned"];
            $firstStatus = $tasks->getTaskStatus($tid);
            $firstPriority = $task[0]["priority"];

            $taskChanged = false;
            $taskRedirection = false;

            /* task update */
            if ($utilities->isPost()) {
            	
                $author = $session->get("userid");
                $assigned = $task[0]["assigned"];
                $created = $utilities->getDate();

                if (!empty($_POST["task-update-descriptions"])) {
                    $description = $utilities->filter($_POST["task-update-descriptions"]);
                }                
                
                $expire = $utilities->filter($_POST["radioA"]);
                $status = $utilities->filter($_POST["radioB"]);
                $reassigned = $utilities->filter($_POST["selected-user"]);
                
                $expire = $utilities->setExpirationTime($expire);
                $completed = $utilities->getDate();

                $priority = $utilities->filter($_POST["priority"]);

                $managerEmail = $users->getUserEmail($task[0]["author"]);

                $fileTempName = $_FILES["file"]["tmp_name"][0];

                $allFiles = $utilities->reArrayFiles($_FILES['file']);

                /* update task text */

                // update text and file
                if (!empty($description) || ($allFiles[0]['tmp_name'] != "")) {

                    if (empty($description)) {
                        $description = "<p></p>";
                    }

                    $update = $updates->createUpdate($session->get("account"), $description, $pid, $tid, 0, $author, $assigned, $created, 1);

                    $uid = $update;

                    if (is_numeric($update)) {

                        // add file
                        if (!empty($allFiles[0]['tmp_name'])) {

                            $uploadedFiles = array();

                            foreach ($allFiles as $file) {

                                $fileIdentifier = $utilities->createFileName($utilities->getDate()."_".$file['name']);
                                $filePath = UPLOAD."project/".$pid."/".$tid."/".$fileIdentifier;

                                $uploadStatus = $uploads->uploadTaskFile($pid, $tid, $fileIdentifier, $file['tmp_name']);

                                if ($uploadStatus) {

                                    $uid = $update;

                                    $uploadUpdate = $uploads->createUpload($session->get("account"), $file['name'], $fileIdentifier, $file['size'], $file['type'], $filePath, $pid, $tid, 0, $uid, $author, $created, 1);

                                    $notice .= "File <b>".$file['name']."</b> was successfully uploaded <br>";

                                    array_push($uploadedFiles, $filePath);
                                
                                } else {

                                    if(!empty($file['tmp_name'])) {

                                        $notice .= "Error while uploading file: <b>".$file['name']."</b><br>";

                                    }

                                }

                            }
                        }

                        /* notifications */

                        $projectManagers = $roles->getProjectManagers($pid);

                        // text update notification
                        if($description != "<p></p>") {
                            
                            /* notify all managers */
                            /*
                            for($i=0; $i<count($projectManagers); $i++) {

                                $notifications->taskUpdateNotify(array($users->getUserEmail($projectManagers[$i]["user"])), $project[0]["title"], $task[0]["title"], $description, $session->get("firstname"), $pid, $tid, $users->getUserEmail($author));

                            }
                            */
                            /* notify assigned user */
                            /*
                            if (!$roles->isProjectManager($pid, $assigned)) {
                            	
                                $notifications->taskUpdateNotify(array($users->getUserEmail($assigned)), $project[0]["title"], $task[0]["title"], $description, $session->get("firstname"), $pid, $tid, $users->getUserEmail($author));
                            }
                            */
                            $notifications->taskUpdateNotify(array($users->getUserEmail($assigned), $managerEmail), $project[0]["title"], $task[0]["title"], $description, $session->get("firstname"), $pid, $tid, $users->getUserEmail($author));

                            $notice .= "Task successfully updated <br>";

                        }

                        // file update notification
                        if(!empty($fileTempName)) {

                            /* notify all managers */
                            /*
                            for($i=0; $i<count($projectManagers); $i++) {

                                $notifications->taskFileNotify(array($users->getUserEmail($projectManagers[$i]["user"])), $project[0]["title"], $task[0]["title"], $filePath, $session->get("firstname"), $pid, $tid, $users->getUserEmail($author));

                            }
                            */
                            /* notify assigned user */
                            /*
                            if (!$roles->isProjectManager($pid, $assigned)) {
                                $notifications->taskFileNotify(array($users->getUserEmail($assigned)), $project[0]["title"], $task[0]["title"], $filePath, $session->get("firstname"), $pid, $tid, $users->getUserEmail($author));
                            }
                            */
                            $notifications->taskFileNotify(array($users->getUserEmail($assigned), $managerEmail), $project[0]["title"], $task[0]["title"], $uploadedFiles, $session->get("firstname"), $pid, $tid, $users->getUserEmail($author));

                        }

                    }

                    // redirect after update
                    $taskRedirection = true;

                }

                /* update task settings */

                // update settings
                if (!empty($reassigned) && !empty($expire) && !empty($status)) {

                    $taskUpdate = $tasks->updateTask($tid, $reassigned, $expire, $priority, $status);

                    if ($taskUpdate) {

                        // update task status
                        if ($status == 2 || $status == 3) {

                            $taskComplete = $tasks->completeTask($tid, $session->get("userid"), $completed);

                            $taskChanged = true;
                            $taskRedirection = true;

                            if ($firstStatus == "OPEN") { 

                                $taskChanged = true;
                            }

                        }

                        if ($firstPriority != $priority) { 

                            $taskChanged = true;
                        }

                        // task reassign notification
                        if ($firstAssigned != $reassigned) {
                            
                            $notifications->newTaskNotify(array($users->getUserEmail($reassigned)), $pid, $tid, $project[0]["title"], $task[0]["title"], $task[0]["description"], $utilities->formatRemainingDate($expire, SHORT_DATE_FORMAT), $users->getUserEmail($author));

                            $taskChanged = true;

                        }

                        // task changes notification
                        if($taskChanged) {
                            
                            $notifications->taskChangeNotify(array($managerEmail), $project[0]["title"], $task[0]["title"], $users->getUserFirstName($session->get("userid")), $pid, $tid, $users->getUserEmail($author));

                            $notice .= "Task successfully changed <br>";
                        }

                    } else {

                        $notice .= "Error while changing task <br>";

                    }
                }
                
            }

            /* task update and update file delete */

            // delete update and files
            if ($utilities->isGet() && !empty($_GET["action"]) && !empty($_GET["context"]) && !empty($_GET["uid"])) {
                
                $uid = $utilities->filter($_GET["uid"]);
                $action = $utilities->filter($_GET["action"]);
                $context = $utilities->filter($_GET["context"]);

                if ($updates->isUpdateAuthor($uid, $session->get("userid")) && $action == "delete" && $context == "update-file") {

                    $updates->deleteUpdate($uid);

                    $updateFiles = $uploads->getUpdateUploads($pid, $tid, $uid);

                    if ($updateFiles) {

                        for ($i=0; $i<count($updateFiles); $i++) {

                        $updateFile = $uploads->getUpload($updateFiles[$i]["id"]);

                        @unlink($updateFile[0]["path"]);

                        }

                        $uploads->deleteUploads($pid, $tid, $uid);

                    }

                    $notice .= "Task update deleted <br>";

                } else {

                    $notice .= "You cannot delete this update <br>";

                }
            }

            /* task file delete */

            // delete task file
            if ($utilities->isGet() && !empty($_GET["action"]) && !empty($_GET["context"]) && !empty($_GET["fid"])) {
                
                $fid = $utilities->filter($_GET["fid"]);
                $action = $utilities->filter($_GET["action"]);
                $context = $utilities->filter($_GET["context"]);

                if ($roles->isProjectManager($pid, $session->get("userid")) && $action == "delete" && $context == "task-file") {

                    $updateFile = $uploads->getUpload($fid);

                    @unlink($updateFile[0]["path"]);

                    $uploads->deleteUpload($updateFile[0]["id"]);


                    $notice .= "Task file deleted <br>";

                } else {

                    $notice .= "You cannot delete this file <br>";

                }
            }

            /* task information */

            // get task info if user have permission (not implemented)
            $project = $projects->getProject($pid);
            $task = $tasks->getTask($pid, $tid);
            $allUsers = $users->listAllProjectUsers($pid);

            //permissions
            $canChange = $roles->canChangeTask($pid, $tid, $session->get("userid"));
            $canClose = $roles->canCloseTask($pid, $tid, $session->get("userid"));  

            if (isset($project)) {

                if (isset($task)) {

                    $taskUpdates = $updates->listAllTaskUpdates($pid, $tid);
                    $user = $users->getUser($task[0]["assigned"]);

                } else {
                    // task not exist
                    $utilities->redirect("error.php?code=7");
                }

            } else {
                // project not exist
                $utilities->redirect("error.php?code=6");
            }

        } else {
            // project or task not specified
            $utilities->redirect("error.php?code=4");
        }

    } else {
        // user not loged
        $utilities->redirect("index.php?redirection=task.php?pid=".$_GET["pid"]."|tid=".$_GET["tid"]);
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?> - <?php echo $project[0]["title"]; ?> - <?php echo $task[0]["title"]; ?></title>

<link rel="alternate" type="application/rss+xml" title="<?php echo $project[0]["title"]; ?> - <?php echo $task[0]["title"]; ?> updates" href="rss.php?pid=<?php echo $pid; ?>&tid=<?php echo $tid; ?>&key=<?php echo ACCESS_KEY; ?>"/>

<!-- styles -->

<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/fonts.css" />
<link rel="stylesheet" href="css/jquery.ui.custom.css" />
<link rel="stylesheet" href="css/jquery.ui.selectmenu.css" />
<link rel="stylesheet" href="css/jquery.ui.achtung.css" />
<link rel="stylesheet" href="css/jquery.fileinput.css" />
<link rel="stylesheet" href="css/jquery.colorbox.css" />
<link rel="stylesheet" href="css/jquery.tiptip.css" />
<link rel="stylesheet" href="css/style.css" />

<!--[if lt IE 9]>
    <link rel="stylesheet" href="css/ie8.css" />
<![endif]-->
<!--[if lte IE 7]>
    <link rel="stylesheet" href="css/ie7.css" />
<![endif]-->

<link rel="icon" href="favicon.ico" type="image/x-icon" />

<!-- scripts -->

<script src="js/jquery.js"></script>
<!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script src="js/jquery.ui.custom.js"></script>
<script src="js/jquery.ui.selectmenu.js"></script>
<script src="js/jquery.stickypanel.js"></script>
<script src="js/jquery.ui.achtung.js"></script>
<script src="js/jquery.tiptip.js"></script>
<script src="js/jquery.autosize.js"></script>
<script src="js/jquery.fileinput.js"></script>
<script src="js/jquery.colorbox.js"></script>
<script src="js/jquery.arbitrary-anchor.js"></script>
<script src="js/livevalidation.js"></script>
<script src="js/script.js"></script>

<!-- remove for production -->
<script src="js/cssrefresh.js"></script>


<!-- javascript -->

<script>
    $(document).ready(function() {

        window.file_selected = false;

        $("select#selected-user").selectmenu({
            style: 'dropdown',
            width: 282,
            maxHeight: 180
        });

        $("#file-upload").customFileInput({
            onChange: function () {
                window.file_selected = true;
                $("#more-files").show();
            }
        });

        $("#file-upload-more").customFileInput({
            onChange: function () {
                window.file_selected = true;
                $("#even-more-files").show();
            }
        });

        $("#file-upload-even-more").customFileInput({
            onChange: function () {
                window.file_selected = true;
            }
        });

        $("#radioA").buttonset();

        $("#radioB").buttonset();

        $("#priority").buttonset();

        $("#datepicker").datepicker({
            altField: "#task-update-date", 
            altFormat: "d. MM yy",
            defaultDate: "<?php if($task[0]['expire'] == 1924902000){ echo date('m').'/'.(date('d')).'/'.date('Y'); } else { echo date('m/d/Y', $task[0]['expire']);} ?>",
            minDate: "-0",
            onSelect: function(dateText) {
                $("#datepicker").datepicker().slideUp();
                $("#radioA2").val(dateText);
            }
        }).hide();

        $("#radioA :radio").click(function() {
            if($("#radioA :radio:checked").attr("id") == "radioA2") {
                $("#datepicker").datepicker().slideDown();
                $("#task-update-date").fadeIn();
            } else {
                $("#datepicker").datepicker().slideUp();
                $("#task-update-date").slideUp();
            }
        });

        $("#update-task").click(function() {
            event.preventDefault();
            if(window.file_selected){
                $("#upload-message").dialog({
                    modal: true,
                });
            }
            return true;
        });

        $(".delete-file").click(function(event) {
            event.preventDefault();
            var link = $(this).attr("href");
            $("#delete-message").dialog({
                modal: true,
                buttons: {
                    Yes: function() {
                        $(this).dialog("close");
                        window.location.href=link;
                    },
                    No: function() {
                        $(this).dialog("close");
                    }
                }
            });
        });

        $("#task-update-date").focus(function() {
            $("#datepicker").datepicker().slideDown();
        });

        $("#task-update-descriptions").autosize();

        $(".update-description a").each(function() {
            var currentlink = $(this);
            var linkloc = currentlink.attr("href");
            var httpcheck = linkloc.substr(0,7);
            
            if(httpcheck != "http://") {
                if(httpcheck != "https:/") {
                    currentlink.attr('href','http://'+linkloc);
                }
                
            }

        });

        <?php if (defined("AUTOSCROLL") && AUTOSCROLL) { ?>
        window.location.href = "##last-update";
        <?php } ?>
        
        <?php
        $utilities->notify($notice, 7);
        ?>

        $(".update-file").delegate("a[rel='lightbox']", "click", function (event) {
            event.preventDefault();
            $.colorbox({href: $(this).attr("href"),
                overlayClose: true,
                iframe: false,
                opacity: 0.3,
                photo: true,
                maxWidth: "100%",
                maxHeight: "100%"
            });
        });

        $("#search-field").autocomplete({
            source: "lib/api/get-my-tasks.php",
            minLength: 3,
            select: function(event, ui) {
                window.location.href = ui.item.id;
            }
        });

        <?php

        if ($taskRedirection) {

            $utilities->notify("Redirecting...", 7);

            $redirectionUrl = $session->get("redirection");

            if ($redirectionUrl == "my-tasks.php") {
            ?>   
                window.setTimeout(function() { window.location.href='<?php echo $session->get("redirection"); ?>'; }, 3000);
            <?php
            } else {
            ?>
                window.setTimeout(function() { window.location.href='project.php?pid=<?php echo $pid; ?>'; }, 3000);
            <?php 
            }
        }
        ?>
        
    });
</script>

</head>
<body>
    <!-- wrap -->
    <div id="wrap">
        <!-- header -->
        <header>
            <!-- search -->
            <div id="search-bar">
                <form id="search-form" action="search-tasks.php" method="get">
                    <fieldset>
                        <input name="s" type="text" id="search-field" class="text-input rounded" placeholder="Search tasks" required></input>
                        <a class="blue-button default-button tip" id="search-button" role="button" href="#" title="Search open tasks"></a>
                        <a class="blue-button default-button tip" id="close-button" role="button" href="#" title="Close search panel"></a>
                    </fieldset>
                </form>
            </div>
            <!-- /search -->

            <?php if (defined("SHORTCUTS") && SHORTCUTS) { ?>
            <!-- breadcrumbs -->
            <div class="breadcrumbs">
                <a class="tip" href="home.php" title="Home: Select project"><img src="images/home.png"></a>
                <a class="separator"><img src="images/separator.png"></a>
                <a class="tip" href="project.php?pid=<?php echo $project[0]["id"]; ?>" title="Project: <?php echo $project[0]["title"]; ?>"><img src="images/project.png"></a>
                <a class="tip" href="task-files.php?pid=<?php echo $project[0]["id"]; ?>&tid=<?php echo $task[0]["id"]; ?>" title="Files: <?php echo $task[0]["title"]; ?>"><img src="images/task-files.png"></a>
                <?php
                if ($users->isOwner($session->get("userid")) || $tasks->isTaskAuthor($project[0]["id"], $task[0]["id"], $session->get("userid"))) {
                ?>
                <a id="edit-task" class="tip" href="edit-task.php?pid=<?php echo $project[0]["id"]; ?>&tid=<?php echo $task[0]["id"]; ?>" title="Edit Task: <?php echo $task[0]["title"]; ?>"><img src="images/edit-project.png"></a>
                <?php 
                }
                ?>
                <a class="separator"><img src="images/separator.png"></a>
                <a class="tip" href="#" id="search-trigger" title="Search"><img src="images/search.png"></a>
            </div>
            <!-- /breadcrumbs -->
            <?php } ?>

            <!-- welcome message -->
            <div id="welcome-message">
                <p>Welcome to <?php echo APPLICATION_TITLE ?> <span class="orange"><?php echo $session->get("firstname"); ?></span></p>
            </div>
            <!-- /welcome message -->

            <!-- top panel -->
            <div id="top-panel">

                <?php 
                if (($users->isAdmin($session->get("userid")) || $roles->isProjectManager($pid, $session->get("userid"))) && $projects->isProjectOpen($project[0]["id"])) {
                ?>
                <!-- add task -->
                <div class="add">
                    <a id="add-task-button" class="tip" href="add-task.php?pid=<?php echo $pid; ?>" role="link" title="Add new task">Add task</a>
                </div>
                <!-- /add task -->
                <?php } ?>

                <!-- settings -->
                <div id="settings">
                    <ul id="settings-menu">
                        <li><a class="tip" href="my-tasks.php" role="link" title="List my tasks"><img src="images/all-tasks-gray.png">My Tasks</a></li>
                        <li><a class="tip" href="my-projects.php" role="link" title="List my projects"><img src="images/all-projects-gray.png">My Projects</a></li>
                        <?php 
                        if ($users->isAdmin($session->get("userid"))) {
                        ?>
                        <li><a class="tip" href="all-users.php" role="link" title="List all users"><img src="images/all-users-gray.png">Users</a></li>
                        <?php } ?>
                        <li><a class="tip" href="my-account.php" role="link" title="My account details"><img src="images/user-gray.png">My Account</a></li>
                        <li><a class="tip" href="index.php?action=logout" role="link" title="Logout"><img src="images/logout-gray.png">Logout</a></li>
                    </ul>
                    <a id="settings-button" class="tip" href="#" role="link" title="Your settings"><img src="images/settings.png"></a>
                </div>
                <!-- /settings -->

            </div>
            <!-- /top panel -->

            <div id="header-title">
                <h1>TASK: <span class="<?php if($tasks->isTaskExpired($pid, $task[0]["id"])){ echo 'striked';} ?>"><?php echo $task[0]["title"]; ?></span></h1>
            </div>

            <div id="project-title">
                <h1><a class="tip" href="project.php?pid=<?php echo $project[0]["id"]; ?>" role="link" title="Back to <?php echo $project[0]["title"]; ?> project"><span class="<?php if($projects->isProjectExpired($pid)) { echo 'striked';} ?>"><?php echo $project[0]["title"]; ?></span></a></h1>
            </div>

            <!-- loader -->
            <div id="loader">
                <img src="images/loading.gif" alt="Loading page" />
            </div>
            <!-- /loader -->

        </header>
        <!-- /header -->
        
        <!-- main wrapper -->
        <section id="main-wrapper" style="padding-bottom: 30px;">
            <!-- main content -->
            <div id="main-content" class="project-tasks clearer" style="min-height: 0px;">
                <!-- task text -->
                <article id="project-description">

                    <!-- task meta -->
                    <div id="project-meta" class="rounded">
                        <!-- task author -->
                        <div id="project-author">
                            <p>Assigned to: 
                            <strong>
                                <?php 
                                if (isset($task[0]["assigned"])) {
                                    echo $users->getShortUserName($task[0]["assigned"]);
                                }
                                ?>
                            </strong>
                            </p>
                            <p>Created by: 
                                <strong>
                                <?php 
                                if (isset($task[0]["author"])) {
                                    echo $users->getShortUserName($task[0]["author"]);
                                }
                                ?>
                                </strong>
                            </p>

                        </div>
                        <!-- /task author -->
                        <!-- task due -->
                        <div id="project-timing">
                            <p>Due: <strong><a class="tip underlined" title="<?php echo $utilities->formatDateTime($task[0]["expire"], LONG_DATE_FORMAT, TIME_FORMAT); ?>" href="#"><?php echo $utilities->formatRemainingDate($task[0]["expire"], SHORT_DATE_FORMAT); ?></a></strong></p>
                            <p>Status: <strong><?php echo $tasks->getTaskStatus($tid)?></strong></p>
                            <p>Created: <strong><a class="tip underlined" title="<?php echo $utilities->formatDateTime($task[0]["created"], LONG_DATE_FORMAT, TIME_FORMAT); ?>" href="#"><?php echo $utilities->elapsedTime($task[0]["created"])?></a></strong></p>
                        </div>
                        <!-- /task due -->
                    </div>
                    <!-- /task meta -->

                    <!-- task description -->
                    <div class="default-text">
                        <p><?php echo $utilities->createLinks($utilities->parseSmileys(nl2br($task[0]["description"]))); ?></p>
                    </div>
                    <!-- /task description -->

                    <?php
                    $taskFiles = $uploads->getTaskUploads($pid, $tid);
                    if ($taskFiles) {
                    ?>

                    <!-- task files -->
                    <div class="default-text">

                        <!-- task files label -->
                        <p class="attachment-label">Task files:</p>
                        <!-- /task files label -->

                        <?php
                        for ($n=0; $n < count($taskFiles); $n++) {
                            $taskFile = $uploads->getUpload($taskFiles[$n]["id"]);
                        ?>

                        <!-- task file -->
                        <div class="update-file rounded">
                            <!-- task file icon -->
                            <p><img src="images/file-ico.png"><a class="tip" href="#" role="link" title="File Size: <?php echo $uploads->getFileSize($taskFile[0]["id"]); ?><br>File Type: <?php echo $uploads->getFileType($taskFile[0]["id"]); ?><br>File Extension: <?php echo $uploads->getFileExtension($taskFile[0]["id"]); ?>"><?php echo $taskFile[0]["name"]; ?></a></p>
                            <!-- /task file icon -->
                            <br>
                            <!-- task file meta -->
                            <p class="smaller-text">File Size: <strong><?php echo $uploads->getFileSize($taskFile[0]["id"]); ?></strong></p>
                            <p class="smaller-text">File Type: <strong><?php echo $uploads->getFileType($taskFile[0]["id"]); ?></strong></p>
                            <!-- /task file meta -->
                            <br>
                            <!-- task file buttons -->
                            <p>
                                <a class="download-link" href="file-download.php?pid=<?php echo $pid; ?>&tid=<?php echo $tid; ?>&uid=0&fid=<?php echo $taskFile[0]["id"]; ?>">Download</a>
                                <?php if($uploads->getFileType($taskFile[0]["id"]) == "image") { ?>
                                <a rel="lightbox" class="download-link" href="<?php echo $taskFile[0]["path"]; ?>">View</a>
                                <?php } ?>
                                <?php if ($roles->isProjectManager($pid, $session->get("userid"))) { ?>
                                <a class="download-link delete-file" href="task.php?tid=<?php echo $tid; ?>&pid=<?php echo $pid; ?>&fid=<?php echo $taskFile[0]["id"]; ?>&action=delete&context=task-file">Delete</a>
                                <?php } ?>
                            </p>
                            <!-- /task file buttons -->

                            <?php if($uploads->getFileType($taskFile[0]["id"]) == "image") { ?>
                            <!-- task file preview -->
                            <div class="image-preview">
                                <a rel="lightbox" href="<?php echo $taskFile[0]["path"]; ?>">
                                <img class="rounded hidden" src="lib/classes/timthumb/timthumb.php?src=<?php echo PATH."/".$taskFile[0]["path"]; ?>&h=95&zc=1" alt=""/>
                                </a>
                            </div>
                            <!-- /task file preview -->
                            <?php } ?>
                        </div>
                        <!-- /task file -->
                        <?php
                        }
                        ?>
                    </div>
                    <!-- /task files -->
                    <?php
                    }
                    ?>

                </article>
                <!-- /task text -->

                <!-- task updates -->
                <article id="project-updates">

                    <!-- task header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p>Updates</p>
                        </div>
                    </div>
                    <!-- /task header -->

                    <?php 
                    if($tasks->isTaskEmpty($pid, $tid)) {  
                    ?>
                    <!-- task has no updates -->
                    <div id="add-first-task" style="margin-bottom: 20px;">
                        <a class="blue-button default-button shadow tip" role="button" href="#" title="This task has no updates at this moment. You can track updates using RSS feed from footer" onClick="return false;">There are no updates for this task</a>
                    </div>
                    <!-- /task has no updates -->
                    <?php 
                    } 
                    ?>

                    <?php

                    $index = 1;
                    $totalUpdates = count($taskUpdates);
                    
                    for ($i=0; $i < $totalUpdates; $i++) {
                    ?>

                    <?php
                    if($index == $totalUpdates) {
                    ?>
                    <a id="last-update" style="position: relative; display: block; float: left; width: 900px; margin-top: -100px;"></a>
                    <?php
                    }
                    ?>

                    <!-- update -->
                    <div class="updates <?php if ($roles->isTaskAssigned($tid, $taskUpdates[$i]["author"])) { echo 'updates-bg2'; } else { echo 'updates-bg1';} ?>" id="<?php if($index == ($totalUpdates-1)) { echo 'last-updates';} ?>">
                        
                        <!-- update content -->
                        <div class="updates-text">
                            <?php 
                            if ($updates->isUpdateAuthor($taskUpdates[$i]["id"], $session->get("userid"))) {
                            ?>
                            <!-- update remove button -->
                            <a class="remove-update tip delete-file" title="Remove this update" href="task.php?tid=<?php echo $tid; ?>&pid=<?php echo $pid; ?>&uid=<?php echo $taskUpdates[$i]["id"]; ?>&action=delete&context=update-file"><img src="images/remove.png"></a>
                            <!-- /update remove button -->
                            <?php } ?>

                            <!-- update text and attachment -->
                            <div class="default-text">
                                <!-- update text -->
                                <p class="update-description"><?php echo $utilities->createLinks($utilities->parseSmileys(nl2br($taskUpdates[$i]["description"]))); ?></p>
                                <!-- /update text -->

                                <?php
                                $updateFiles = $uploads->getUpdateUploads($pid, $tid, $taskUpdates[$i]["id"]);
                                if ($updateFiles) {
                                ?>

                                <!-- update attachment label -->
                                <p class="attachment-label">Attachment:</p>
                                <!-- /update attachment label -->

                                <?php
                                for ($n=0; $n < count($updateFiles); $n++) {
                                    $updateFile = $uploads->getUpload($updateFiles[$n]["id"]);
                                ?>
                                    <!-- update attachment -->
                                    <div class="update-file rounded">
                                        <!-- update attachment icon -->
                                        <p><img src="images/file-ico.png"><a class="tip" href="#" role="link" title="File Size: <?php echo $uploads->getFileSize($updateFile[0]["id"]); ?><br>File Type: <?php echo $uploads->getFileType($updateFile[0]["id"]); ?><br>File Extension: <?php echo $uploads->getFileExtension($updateFile[0]["id"]); ?>"><?php echo $updateFile[0]["name"]; ?></a></p>
                                        <!-- /update attachment icon -->
                                        <br>
                                        <!-- update attachment meta -->
                                        <p class="smaller-text">File Size: <strong><?php echo $uploads->getFileSize($updateFile[0]["id"]); ?></strong></p>
                                        <p class="smaller-text">File Type: <strong><?php echo $uploads->getFileType($updateFile[0]["id"]); ?></strong></p>
                                        <!-- /update attachment meta -->
                                        <br>
                                        <!-- update attachment buttons -->
                                        <p>
                                            <a class="download-link" href="file-download.php?pid=<?php echo $pid; ?>&tid=<?php echo $tid; ?>&uid=<?php echo $taskUpdates[$i]["id"]; ?>&fid=<?php echo $updateFile[0]["id"]; ?>">Download</a>
                                            <?php if($uploads->getFileType($updateFile[0]["id"]) == "image") { ?>
                                            <a rel="lightbox" class="download-link" href="<?php echo $updateFile[0]["path"]; ?>">View</a>
                                            
                                            <?php } ?>
                                        </p>
                                        <!-- /update attachment buttons -->

                                        <?php if($uploads->getFileType($updateFile[0]["id"]) == "image") { ?>
                                        <!-- update attachment preview -->
                                        <div class="image-preview">
                                            <a rel="lightbox" href="<?php echo $updateFile[0]["path"]; ?>">
                                            <img class="rounded hidden" src="lib/classes/timthumb/timthumb.php?src=<?php echo PATH."/".$updateFile[0]["path"]; ?>&h=95&zc=1" alt=""/>
                                            </a>
                                        </div>
                                        <!-- /update attachment preview -->
                                        <?php } ?>
                                    </div>
                                    <!-- /update attachment -->
                                <?php
                                }
                                }
                                ?>
                            </div>
                            <!-- /update text and attacments -->
                        </div>
                        <!-- update content -->
                        <!-- update meta -->
                        <div class="updates-meta">
                            <p>
                            <strong>
                            <img src="images/author.png">
                            <?php 
                            if (isset($taskUpdates[$i]["author"])) {
                                echo $users->getShortUserName($taskUpdates[$i]["author"]);
                            }
                            ?>
                            </strong>
                            on <img src="images/date.png">                               
                            <?php echo $utilities->formatDateTime($taskUpdates[$i]["created"], LONG_DATE_FORMAT, TIME_FORMAT); ?>
                            Assigned <img src="images/assigned.png">
                            <strong>
                            <?php 
                            if (isset($taskUpdates[$i]["assigned"])) {
                                echo $users->getShortUserName($taskUpdates[$i]["assigned"]);
                            }
                            ?>
                            </strong>
                            </p>
                        </div>
                        <!-- /update meta -->
                    </div>
                    <!-- /update  -->

                    <?php
                        $index++;
                    } 
                    ?>
                               
                    <!-- complete  -->
                    <?php 
                    if (!empty($task[0]["finished"]) && $tasks->getTaskStatus($tid) != "OPEN") {
                    ?>
                    <div class="complete">
                        <p>
                        <strong><?php echo $tasks->getTaskStatus($tid); ?></strong> 
                        by:                  
                        <strong><?php echo $users->getShortUserName($task[0]["finished"]); ?></strong>     
                    
                        <?php 
                        if (!empty($task[0]["completed"])) { 
                        ?> 
                        on 
                        <?php echo $utilities->formatDateTime($task[0]["completed"], LONG_DATE_FORMAT, TIME_FORMAT); ?>
                        </p>
                    </div>
                    <?php 
                        }
                    } 
                    ?> 
                    <!-- /complete  -->

                </article>
                <!-- task updates -->

            </div>             
            <!-- /main content -->
        </section>
        <!-- /main wrapper -->

        <?php
        if ($roles->canUpdateTask($pid, $tid, $session->get("userid"))) {
        ?>

        <!-- task update -->
        <section id="task-update-section">

            <div id="task-update-content" class="project-tasks clearer">

                <article id="project-comment">

                    <form name="update-task-form" action="task.php?tid=<?php echo $tid; ?>&pid=<?php echo $pid; ?>" method="post" enctype="multipart/form-data">
                        
                        <!-- comment part -->
                        <div>
                            <!-- task comment -->
                            <fieldset>
                                <label class="small-label">Update task</label>
                                <textarea name="task-update-descriptions" id="task-update-descriptions" class="text-area rounded animated" placeholder="Leave a comment..." tabindex="1"></textarea>
                                <script> 
                                    var taskUpdateDescriptions = new LiveValidation("task-update-descriptions", {onlyOnSubmit: false, validMessage: "OK" });
                                    taskUpdateDescriptions.add(Validate.Presence);
                                    taskUpdateDescriptions.add(Validate.Length, {minimum: 10});
                                </script>
                            </fieldset>
                            <!-- task comment -->

                            <div class="spacer"></div>
                        </div>
                        <!-- /comment part -->

                        <!-- files part -->
                        <div>
                            <!-- task files -->
                            <fieldset>
                                <label class="small-label" for="file">Add files <a class="tip" href="#" title="Your server configuration allow maximum file size of <?php echo $utilities->getMaximumUpload(); ?> MB.<br>To select multiple files hold <b>Ctrl</b> key."><img src="images/help.png"></a></label>
                                <input type="file" name="file[]" id="file-upload" multiple="multiple">
                            </fieldset>
                            <!-- /task files -->

                            <!-- task files -->
                            <fieldset id="more-files" style="display: none; margin-top: 20px;">
                                <label class="small-label" for="file">Add more files</label>
                                <input type="file" name="file[]" id="file-upload-more" multiple="multiple">
                            </fieldset>
                            <!-- /task files -->

                            <!-- task files -->
                            <fieldset id="even-more-files" style="display: none; margin-top: 20px;">
                                <label class="small-label" for="file">Add even more files</label>
                                <input type="file" name="file[]" id="file-upload-even-more" multiple="multiple">
                            </fieldset>
                            <!-- /task files -->

                            <div class="spacer"></div>
                        </div>
                        <!-- /files part -->

                        <!-- task change part -->
                        <div style='display:<?php if ($canChange) { echo 'block'; } else { echo 'none'; } ?>'>
                            
                            <!-- change date part -->
                            <div style='display:<?php if ($canClose) { echo "block"; } else { echo "none"; } ?>;'>
                                <!-- change date -->
                                <fieldset>
                                    <label class="small-label">Change date to</label>
                                    <div id="radioA" style="position: relative;">
                                        <label for="radioA1">TODAY</label>
                                        <input type="radio" id="radioA1" value="<?php echo date("m")."/".date("d")."/".date("Y");?>" name="radioA" <?php if($utilities->isToday($task[0]["expire"])){ echo "checked"; } ?>>
                                        <label for="radioA2"><img src="images/calendar-ico.png" alt="Select date"></label>
                                        <input type="radio" id="radioA2" value="<?php echo date("m/d/Y", $task[0]["expire"]);?>" name="radioA" <?php if(!$utilities->isToday($task[0]["expire"]) && $task[0]["expire"] != 1924902000){ echo "checked"; } ?>>
                                        <label for="radioA3">WHENEVER</label>
                                        <input type="radio" id="radioA3" value="-1" name="radioA" <?php if($task[0]["expire"] == 1924902000){ echo "checked"; } ?>>

                                        <div id="datepicker" style="top: 0px; left: 630px;"></div> 
                                    </div>
                                    <input name="task-update-date" type="text" value="<?php if($task[0]["expire"] == 1924902000){ echo date("m")."/".(date("d"))."/".date("Y"); } ?>" id="task-update-date" class="text-input rounded" style='display: <?php if(!$utilities->isToday($task[0]["expire"]) && $task[0]["expire"] < 1924902000){ echo "block"; } else { echo "none";} ?>; margin-left: -8px;'>         
                                </fieldset>
                                <!-- change date -->
                                
                                <div class="spacer"></div>
                            </div>
                            <!-- /change date part -->

                            <!-- change status part -->
                            <div>
                                <!-- task status -->
                                <fieldset>
                                    <label class="small-label">Change status to</label>
                                    <div id="radioB">
                                        <label for="radioB1">OPEN</label>
                                        <input type="radio" id="radioB1" value="1" name="radioB" <?php if ($task[0]["status"]==1) { echo "checked";} ?>>
                                        <label for="radioB2">RESOLVED</label>
                                        <input type="radio" id="radioB2" value="2" name="radioB" <?php if ($task[0]["status"]==2) { echo "checked";} ?>>
                                        <label for="radioB3">COMPLETE</label>
                                        <input type="radio" id="radioB3" value="3" name="radioB" <?php if ($task[0]["status"]==3) { echo "checked";} ?> <?php if (!$canClose) { echo "disabled"; } ?>>
                                    </div>
                                    <input name="user-role" type="hidden" id="user-role" class="text-input rounded" value="">         
                                </fieldset>
                                <!-- /task status -->

                                <div class="spacer"></div>
                            </div>
                            <!-- /change status part -->

                            <!-- reassignment part -->
                            <div style='display:<?php if ($canClose) { echo "block"; } else { echo "none"; } ?>;'>

                                <!-- task reassignment -->
                                <fieldset>
                                    <label class="small-label" for="selected-user">Re-assign to</label>
                                    <select name="selected-user" id="selected-user">
                                        <optgroup label="Project Allocated Users">
                                        <?php
                                        for ($i=0; $i < count($allUsers); $i++) {
                                        ?>
                                        <option value="<?php echo $allUsers[$i]["user"]; ?>" <?php if ($task[0]["assigned"] == $allUsers[$i]["user"]) { echo "selected";} ?>><?php echo $users->getFullUserName($allUsers[$i]["user"]) ?></option>
                                        <?php } ?>
                                        </optgroup>
                                    </select>
                                </fieldset>
                                <!-- /task reassignment -->

                                <div class="spacer"></div>
                            </div>
                            <!-- /reassignment part -->

                            <!-- priority part -->
                            <div style='display:<?php if ($canClose) { echo "block"; } else { echo "none"; } ?>;'>    <!-- change priority -->
                                <fieldset>
                                    <label class="small-label" for="priority">High Priority</label>
                                    <div id="priority">
                                        <label for="priority1">YES</label>
                                        <input type="radio" id="priority1" value="1" name="priority" <?php if ($tasks->isTaskHasPriority($task[0]["project"], $task[0]["id"])) { echo 'checked'; } ?>>
                                        <label for="priority2">NO</label>
                                        <input type="radio" id="priority2" value="0" name="priority" <?php if (!$tasks->isTaskHasPriority($task[0]["project"], $task[0]["id"])) { echo 'checked'; } ?>>
                                    </div>
                                </fieldset>
                                <!-- /change priority -->

                                <div class="spacer"></div>
                            </div>
                            <!-- /priority part -->

                        </div>
                        <!-- /change part -->

                        <!-- submit -->
                        <fieldset>
                            <a class="orange-button default-button tip" id="update-task" role="button" href="#" onClick="document['update-task-form'].submit(); return false;" title="Update task" tabindex="2">UPDATE</a>

                            <a class="link-button tip" id="cancel-task-update" role="link" href="project.php?pid=<?php echo $project[0]["id"]; ?>" title="Cancel and return to project">Cancel</a>
                        </fieldset>
                        <!-- /submit -->

                    </form>

                </article>

                <a class="tip" id="to-top" href="#" title="Scroll to top" tabindex="12"></a>

            </div>

        </section>
        <!-- /task update -->

        <?php } ?>

        <div id="upload-message" title="Upload in progress..." style="display: none;">
            <div style="width: 32px; height: 32px; margin: 30px auto 0 auto;"><img src="css/images/loading.gif"></div>
        </div>

        <div id="delete-message" title="Delete update" style="display: none;">
            <p>Do you really want to delete this update?</p>
        </div>
        
        <!-- footer -->
        <footer>
            <!-- logo -->
            <div id="logo">
                <div id="export">
                    <a class="tip export-link" href="rss.php?pid=<?php echo $pid; ?>&tid=<?php echo $tid; ?>&key=<?php echo ACCESS_KEY; ?>" title="View updates for <?php echo $task[0]["title"]; ?> task using RSS channel">RSS</a>
                    <a class="tip export-link" href="ics.php?pid=<?php echo $pid; ?>&tid=<?php echo $tid; ?>&key=<?php echo ACCESS_KEY; ?>" title="Export calendar in ICS format for <?php echo $task[0]["title"]; ?> task">Export task calendar</a>
                    <?php if (defined("DISQUS") && DISQUS) { ?>
                    <a class="tip export-link" href="task-discussion.php?pid=<?php echo $pid; ?>&tid=<?php echo $tid; ?>" title="Task discussion for <?php echo $task[0]["title"]; ?>">Task discussion</a>
                    <?php } ?>
                </div>
                <img class="ng-logo" src="images/logo.png" alt="<?php echo APPLICATION_TITLE ?> logo" />
            </div>
            <!-- /logo -->
            
        </footer>
        <!-- /footer -->
            
    </div>
    <!-- /wrap -->
</body>
</html>