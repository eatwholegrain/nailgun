<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {


        if (!empty($_GET["pid"])) {

            $pid = $utilities->filter($_GET["pid"]);

            if ($roles->canViewProject($_GET["pid"], $session->get("userid")) && ($projects->isProjectOpen($pid) || $users->isOwner($session->get("userid")))) {

                // delete update
                if ($utilities->isGet() && !empty($_GET["action"]) && !empty($_GET["context"]) && !empty($_GET["uid"])) {
                    
                    $tid = $utilities->filter($_GET["tid"]);
                    $uid = $utilities->filter($_GET["uid"]);
                    $action = $utilities->filter($_GET["action"]);
                    $context = $utilities->filter($_GET["context"]);

                    if ($updates->isUpdateAuthor($pid, $tid, $uid, $session->get("userid")) && $action == "delete" && $context == "update-file") {

                        $updates->deleteUpdate($pid, $tid, $uid);

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

                // get task info if user have permission (not implemented)
                $project = $projects->getProject($pid);
                $allUsers = $users->listAllUsers($session->get("account"));

                if (isset($project)) {

                    $activeTasks = $tasks->listAllProjectTasks($pid);


                } else {
                    // project not exist
                    $utilities->redirect("error.php?code=6");
                }

            } else {
                // permission problem
                $utilities->redirect("error.php?code=5");
            }

        } else {
            // project or task not specified
            $utilities->redirect("error.php?code=4");
        }

    } else {
        // user not loged
        $utilities->redirect("error.php?code=12");
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?> - <?php echo $project[0]["title"]; ?> Files</title>

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
<script src="js/livevalidation.js"></script>
<script src="js/script.js"></script>

<!-- remove for production -->
<script src="js/cssrefresh.js"></script>


<!-- javascript -->

<script>
    $(document).ready(function() {

        <?php if (defined("AUTOSCROLL") && AUTOSCROLL) { ?>
        $("html, body").animate({ scrollTop: $(document).height() }, 1500);
        <?php } ?>
        
        <?php
        $utilities->notify($notice, 7);
        ?>

        <?php
        if (isset($_POST["radioB"])) {
            if ($_POST["radioB"] == 3) {
                $utilities->notify("Task completed. Redirecting...", 7);
                ?>   
                window.setTimeout(function(){ window.location.href="project.php?pid=<?php echo $pid; ?>"; }, 3000);
                <?php
            } else if ($_POST["radioB"] == 2) {
                $utilities->notify("Task resolved. Redirecting...", 7);
                ?>   
                window.setTimeout(function(){ window.location.href="project.php?pid=<?php echo $pid; ?>"; }, 3000);
                <?php
            }
        }
        ?>

        $(".update-file").delegate("a[rel='lightbox']", "click", function (event) {
            event.preventDefault();
            $.colorbox({href: $(this).attr("href"),
                overlayClose: true,
                iframe: false,
                opacity: 0.3,
                photo: true
            });
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

        $("#search-field").autocomplete({
            source: "lib/api/get-my-tasks.php",
            minLength: 3,
            select: function(event, ui) {
                window.location.href = ui.item.id;
            }
        });
        
    }); 
</script>

<?php if (defined("CHAT") && CHAT) { ?>

<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//cdn.zopim.com/?<?php if (defined("ZOPIM_ID")){ echo ZOPIM_ID;} ?>';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
</script>

<?php } ?>

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
                <h1>PROJECT: <span class="<?php if($projects->isProjectExpired($project[0]["id"])){ echo 'striked';} ?>"><?php echo $project[0]["title"]; ?></span></h1>
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

                <!-- project text -->
                <article id="project-description">

                    <?php if (defined("PROJECT_INFO") && PROJECT_INFO) { ?>
                    <div id="project-meta" class="rounded">
                        <div id="project-author">
                            <p>Created by: 
                                <strong>
                                <?php 
                                if (isset($project[0]["author"])) {
                                    echo $users->getShortUserName($project[0]["author"]);
                                }
                                ?>
                                </strong>
                            </p>
                            <?php
                            $projectManagers = $roles->getProjectManagers($project[0]["id"]);

                            if ($projectManagers) { 
                            ?>
                            <p>Project managers: 
                                <strong>
                                <?php 
                                
                                for($i=0; $i<count($projectManagers); $i++) {
                                ?>
                                    <a class="tip project-managers" title="<?php echo $users->getFullUserName($projectManagers[$i]["user"]); ?> (<?php echo $users->getUserEmail($projectManagers[$i]["user"]); ?>)"><?php echo $users->getShortUserName($projectManagers[$i]["user"]); ?></a>
                                <?php
                                }
                                ?>
                                </strong>
                            </p>
                            <?php 
                            } 
                            ?>
                            <?php
                            $projectUsers = $roles->getProjectUsers($project[0]["id"]);

                            if ($projectUsers) { 
                            ?>
                            <p>Project users: 
                                <strong>
                                <?php 
                                for($i=0; $i<count($projectUsers); $i++) {
                                ?>
                                    <a class="tip project-managers" title="<?php echo $users->getFullUserName($projectUsers[$i]["user"]); ?> (<?php echo $users->getUserEmail($projectUsers[$i]["user"]); ?>)"><?php echo $users->getShortUserName($projectUsers[$i]["user"]); ?></a>
                                <?php
                                }
                                ?>
                                </strong>
                            </p>
                            <?php 
                            } 
                            ?>
                        </div>
                        <div id="project-timing">
                            <p>Due: <strong><a class="tip underlined" title="<?php echo $utilities->formatDateTime($project[0]["expire"], LONG_DATE_FORMAT, TIME_FORMAT); ?>" href="#"><?php echo $utilities->formatRemainingDate($project[0]["expire"], SHORT_DATE_FORMAT); ?></a></strong></p>
                            <p>Status: <strong><?php echo $projects->getProjectStatus($pid)?></strong></p>
                            <p>Created: <strong><a class="tip underlined" title="<?php echo $utilities->formatDateTime($project[0]["created"], LONG_DATE_FORMAT, TIME_FORMAT); ?>" href="#"><?php echo $utilities->elapsedTime($project[0]["created"])?></a></strong></p>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="default-text">
                        <p><?php echo $utilities->createLinks($utilities->parseSmileys(nl2br($project[0]["description"]))); ?></p>
                    </div>
                </article>
                <!-- /project text -->

                <!-- task header -->
                <div class="task-header">
                    <div class="task-title">
                        <p>Files from all Project Tasks</p>
                    </div>
                </div>
                <!-- /task header -->

                <?php
                for ($x=0; $x < count($activeTasks); $x++) {
                ?>

                <?php 
                $taskUploadsCount = $uploads->hasTaskHaveUploads($pid, $activeTasks[$x]["id"]);
                if (count($taskUploadsCount)>0) {
                ?>

                <!-- task updates -->
                <article id="project-updates">

                    <!-- task header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p><a href="task.php?tid=<?php echo $activeTasks[$x]["id"];?>&pid=<?php echo $pid; ?>"><img src="images/all-tasks-gray.png"> <?php echo $activeTasks[$x]["title"];?></a></p>
                        </div>
                    </div>
                    <!-- /task header -->

                    <?php
                    $taskFiles = $uploads->getTaskUploads($pid, $activeTasks[$x]["id"]);
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
                                <a class="download-link" href="file-download.php?pid=<?php echo $pid; ?>&tid=<?php echo $activeTasks[$x]["id"]; ?>&uid=0&fid=<?php echo $taskFile[0]["id"]; ?>">Download</a>
                                <?php if($uploads->getFileType($taskFile[0]["id"]) == "image") { ?>
                                <a rel="lightbox" class="download-link" href="<?php echo $taskFile[0]["path"]; ?>">View</a>
                                <?php } ?>
                                <?php if ($roles->isProjectManager($pid, $session->get("userid"))) { ?>
                                <a class="download-link delete-file" href="project-files.php?tid=<?php echo $activeTasks[$x]["id"]; ?>&pid=<?php echo $pid; ?>&fid=<?php echo $taskFile[0]["id"]; ?>&action=delete&context=task-file">Delete</a>
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

                    <?php
                    $taskUpdates = $updates->listAllTaskUpdates($pid, $activeTasks[$x]["id"]);

                    $index = 1;
                    for ($i=0; $i < count($taskUpdates); $i++) {
                        $updateFiles = $uploads->getUpdateUploads($pid, $activeTasks[$x]["id"], $taskUpdates[$i]["id"]);
                        if (count($updateFiles)>0) {
                        ?>

                        <!-- update  -->
                        <div class="updates <?php if ($roles->isTaskAssigned($activeTasks[$x]["id"], $taskUpdates[$i]["author"])) { echo 'updates-bg2'; } else { echo 'updates-bg1';} ?>">
                            
                            <!-- update text -->
                            <div class="updates-text">
                                <?php 
                                if ($updates->isUpdateAuthor($pid, $activeTasks[$x]["id"], $taskUpdates[$i]["id"], $session->get("userid"))) {

                                ?> 
                                <a class="remove-update tip delete-file" title="Remove this update" href="project-files.php?tid=<?php echo $activeTasks[$x]["id"]; ?>&pid=<?php echo $pid; ?>&uid=<?php echo $taskUpdates[$i]["id"]; ?>&action=delete&context=update-file"><img src="images/remove.png"></a>
                                <?php } ?>

                                <div class="default-text">

                                    <p class="attachment-label">Attachment:</p>

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
                                            <a class="download-link" href="file-download.php?pid=<?php echo $pid; ?>&tid=<?php echo $activeTasks[$x]["id"]; ?>&uid=<?php echo $taskUpdates[$i]["id"]; ?>&fid=<?php echo $updateFile[0]["id"]; ?>">Download</a>
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
                                    
                                    ?>
                                </div>
                            </div>
                            <!-- /update text -->

                            <!-- update meta  -->
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
                                </p>
                            </div>
                            <!-- /update meta  -->

                        </div>
                        <!-- /update  -->

                        <?php
                        $index++;

                        }
                    }
                    ?>
                               

                </article>
                <!-- task updates -->

                <?php }} ?>


            </div>             
            <!-- /main content -->
        </section>
        <!-- /main wrapper -->

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