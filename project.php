<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if (!empty($_GET["pid"])) {

            $pid = $utilities->filter($_GET["pid"]);

            if ($projects->isAccountProject($pid, $session->get("account"))) {
            
                $session->set("redirection", "project.php?pid=".$pid);

                // open or close project
                if ($utilities->isGet() && !empty($_GET["action"]) && $users->isOwner($session->get("userid"))) {
                        
                    $action = $utilities->filter($_GET["action"]);
                    $completed = $utilities->getDate();

                    if ($action == "close") {

                        $closingStatus = $projects->closeProject($pid, $session->get("userid"), $completed);

                        if ($closingStatus) {

                            $tasks->closeAllProjectTasks($pid, $session->get("userid"), $completed);
                            $notice = "Project and all project tasks are closed";
                        }
                        
                    } else if ($action == "open") {

                        $projects->openProject($pid);
                        $notice = "Project is open again. Please open all required tasks";

                    } 
                }

                // if can view project
                if ($roles->canViewProject($_GET["pid"], $session->get("userid")) && ($projects->isProjectOpen($pid) || $users->isOwner($session->get("userid")))) {

                    $project = $projects->getProject($pid);

                    if (isset($project)) {

                        $activeTasks = $tasks->listActiveTasks($pid);
                        $resolvedTasks = $tasks->listResolvedTasks($pid);
                        $closedTasks = $tasks->listClosedTasks($pid);

                    } else {
                        // project not exist
                        $utilities->redirect("error.php?code=6");
                    }
                    
                    if(empty($notice)) {
                        $notice = "Welcome to <b>".$project[0]["title"]."</b> project<b>";
                    }

                } else {
                    // permission problem
                    $utilities->redirect("error.php?code=5");
                }

            } else {
                // account permission problem
                $utilities->redirect("error.php?code=5");
            }


        } else {
            // project not specified
            $utilities->redirect("error.php?code=1");
        }

    } else {
        // user not loged
        $utilities->redirect("index.php?redirection=project.php?pid=".$_GET["pid"]);
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?> - <?php echo $project[0]["title"]; ?></title>

<!-- styles -->

<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/fonts.css" />
<link rel="stylesheet" href="css/jquery.ui.custom.css" />
<link rel="stylesheet" href="css/jquery.ui.selectmenu.css" />
<link rel="stylesheet" href="css/jquery.ui.achtung.css" />
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
<script src="js/jquery.tinysort.js"></script>
<script src="js/jquery.cookie.js"></script>
<script src="js/script.js"></script>

<!-- remove for production -->
<script src="js/cssrefresh.js"></script>


<!-- javascript -->

<script>
    $(document).ready(function() {

        <?php
        $utilities->notify($notice, 7);
        ?>

        $("#by-title").click(function() {
            $("ul.listing > li").tsort('',{attr:'data-title'});
            $(".tip").removeClass("sorted");
            $(this).addClass("sorted");
            return false;
        });

        $("#by-date").click(function() {
            $("ul.listing > li").tsort('',{attr:'data-expire'});
            $(".tip").removeClass("sorted");
            $(this).addClass("sorted");
            return false;
        });

        $("#by-assigned").click(function() {
            $("ul.listing > li").tsort('',{attr:'data-assigned'});
            $(".tip").removeClass("sorted");
            $(this).addClass("sorted");
            return false;
        });

        $("#by-resolved").click(function() {
            $("ul.listing > li").tsort('',{attr:'data-completed', order: 'desc'});
            $(".tip").removeClass("sorted");
            $(this).addClass("sorted");
            return false;
        });

        $("#by-completed").click(function() {
            $("ul.listing > li").tsort('',{attr:'data-completed', order: 'desc'});
            $(".tip").removeClass("sorted");
            $(this).addClass("sorted");
            return false;
        });

        $("#my").click(function() {
            onlyMe();
        });

        $("ul.listing > li").click(function() {
            window.location.href="task.php?pid=<?php echo $pid; ?>&tid="+$(this).attr("data-id");
        });

        $("#close-project").click(function(event) {

            event.preventDefault();

            $("#dialog-message").dialog({
                modal: true,
                buttons: {
                    Yes: function() {
                        $(this).dialog("close");
                        window.location.href="project.php?pid=<?php echo $project[0]["id"]; ?>&action=close";
                    },
                    No: function() {
                        $(this).dialog("close");
                    }
                }
            });

        });

        onlyMe();

        $("#search-field").autocomplete({
            source: "lib/api/get-my-tasks.php",
            minLength: 3,
            select: function(event, ui) {
                window.location.href = ui.item.id;
            }
        });

    });

    function onlyMe() {
        if ($("#my").is(":checked")) {
            $("ul.listing li").each(function() {
                if ($(this).attr("data-mine") == 0) {
                    $(this).hide(500);
                }
            });
            $.cookie("me", 1, {expires: 1}); //path: '/'
            $.achtung({message: 'Showing only tasks assigned to you', timeout: 7});
        } else {
            $("ul.listing li").each(function() {
                if ($(this).attr("data-mine") == 0) {
                    $(this).show(250);
                }
            });
            $.removeCookie("me");
        }
    }

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
                <a class="tip" href="calendar.php?pid=<?php echo $project[0]["id"]; ?>" title="Calendar for: <?php echo $project[0]["title"]; ?>"><img src="images/calendar.png"></a>
                <a class="tip" href="project-files.php?pid=<?php echo $project[0]["id"]; ?>" title="Files: <?php echo $project[0]["title"]; ?>"><img src="images/task-files.png"></a>
                <?php 
                if ($users->isOwner($session->get("userid"))) {
                    ?>
                    <a id="edit-project" class="tip" href="edit-project.php?pid=<?php echo $project[0]["id"]; ?>" title="Edit Project: <?php echo $project[0]["title"]; ?>"><img src="images/edit-project.png"></a>
                    <?php
                    if ($projects->isProjectOpen($project[0]["id"])) {
                    ?>
                    <a id="close-project" class="tip" href="project.php?pid=<?php echo $project[0]["id"]; ?>&action=close" title="Close Project: <?php echo $project[0]["title"]; ?>"><img src="images/close-projects.png"></a>
                    <?php } ?>

                    <?php
                    if ($projects->isProjectClosed($project[0]["id"])) {
                    ?>
                    <a class="tip" href="project.php?pid=<?php echo $project[0]["id"]; ?>&action=open" title="Open Project: <?php echo $project[0]["title"]; ?>"><img src="images/open-projects.png"></a>
                    <?php } ?>

                <?php } ?>
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
        <section id="main-wrapper">
            <!-- main content -->
            <div id="main-content" class="project-tasks clearer">
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
                                    <?php
                                    if ($users->isAdmin($session->get("userid"))) {
                                    ?>
                                    <a class="tip project-managers" href="user.php?uid=<?php echo $projectManagers[$i]["user"]; ?>" title="<?php echo $users->getFullUserName($projectManagers[$i]["user"]); ?> (<?php echo $users->getUserEmail($projectManagers[$i]["user"]); ?>)"><?php echo $users->getShortUserName($projectManagers[$i]["user"]); ?></a>
                                    <?php } else { ?>
                                    <a class="tip project-managers" href="#" title="<?php echo $users->getFullUserName($projectManagers[$i]["user"]); ?> (<?php echo $users->getUserEmail($projectManagers[$i]["user"]); ?>)"><?php echo $users->getShortUserName($projectManagers[$i]["user"]); ?></a>
                                    <?php } ?>
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
                                    <?php
                                    if ($users->isAdmin($session->get("userid"))) {
                                    ?>
                                    <a class="tip project-managers" href="user.php?uid=<?php echo $projectUsers[$i]["user"]; ?>" title="<?php echo $users->getFullUserName($projectUsers[$i]["user"]); ?> (<?php echo $users->getUserEmail($projectUsers[$i]["user"]); ?>)"><?php echo $users->getShortUserName($projectUsers[$i]["user"]); ?></a>
                                    <?php } else { ?>
                                    <a class="tip project-managers" href="#" title="<?php echo $users->getFullUserName($projectUsers[$i]["user"]); ?> (<?php echo $users->getUserEmail($projectUsers[$i]["user"]); ?>)"><?php echo $users->getShortUserName($projectUsers[$i]["user"]); ?></a>
                                    <?php } ?>
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

                <!-- project tasks -->
                <article id="project-tasks">

                    <div class="sortable">

                    <!-- task header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p><a class="tip" id="by-title" href="#" title="Sort by task name">Open tasks</a></p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip sorted" id="by-date" href="#" title="Sort by task expiration date">Due</a></p>
                        </div>
                        <div class="task-user">
                            <p><a class="tip" id="by-assigned" href="#" title="Sort by assigned user">Assignee</a></p>
                        </div>
                        <div class="task-mine">
                            <p><a class="tip" id="by-mine" href="#" title="Show only my tasks"><img src="images/user-me.png"></a>&nbsp;<input type="checkbox" name="my" id="my" <?php if(isset($_COOKIE["me"]) && $_COOKIE["me"] == 1) { echo 'checked';} ?>></p>
                        </div>
                    </div>
                    <!-- /task header -->

                    

                    <ul class="listing">

                    <?php
                    for ($i=0; $i < count($activeTasks); $i++) {
                    ?>

                    <li data-id="<?php echo $activeTasks[$i]["id"]; ?>" data-expire="<?php echo $activeTasks[$i]["expire"]; ?>" data-created="<?php echo $activeTasks[$i]["created"]; ?>" data-assigned="<?php echo $users->getShortUserName($tasks->getAssignedTaskUser($pid, $activeTasks[$i]["id"])); ?>" data-title="<?php echo $activeTasks[$i]["title"]; ?>" data-mine="<?php if ($tasks->isTaskMine($activeTasks[$i]["project"], $activeTasks[$i]["id"], $session->get("userid"))) { echo '1'; } else { echo '0'; } ?>">

                    <!-- task  -->
                    <div class="task task-bg <?php if ($tasks->isTaskHasPriority($activeTasks[$i]["project"], $activeTasks[$i]["id"])) { echo 'high'; } ?> <?php echo $utilities->setColorClass($activeTasks[$i]["expire"]); ?>">
                        <div class="task-title <?php if($tasks->isTaskExpired($pid, $activeTasks[$i]["id"])){ echo'striked';} ?>">
                            <p><a class="tip" href="task.php?tid=<?php echo $activeTasks[$i]["id"]; ?>&pid=<?php echo $pid; ?>" role="link" title="<?php echo strip_tags($activeTasks[$i]["description"]); ?>"><?php echo $activeTasks[$i]["title"]; ?></a></p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip" href="#" role="link" title="Due: <?php echo $utilities->formatRemainingDate($activeTasks[$i]["expire"], SHORT_DATE_FORMAT); ?>"><?php echo $utilities->formatDate($activeTasks[$i]["expire"], SHORT_DATE_FORMAT); ?></a></p>
                        </div>
                        <div class="task-user">
                            <p>
                                <a class="tip" href="#" role="link" title="<?php echo $users->getFullUserName($tasks->getAssignedTaskUser($pid, $activeTasks[$i]["id"])); ?>">
                                <?php 
                                    if (isset($activeTasks[$i]["assigned"])) { 
                                        echo $users->getShortUserName($tasks->getAssignedTaskUser($pid, $activeTasks[$i]["id"])); 
                                    }; 
                                ?>
                                </a>
                            </p>
                        </div>
                        <?php if (defined("UPDATE_COUNTER") && UPDATE_COUNTER && !$tasks->isTaskEmpty($pid, $activeTasks[$i]["id"])) { ?>

                        <?php } ?>
                        <?php if (defined("UPDATE_COUNTER") && UPDATE_COUNTER && !$tasks->isTaskEmpty($pid, $activeTasks[$i]["id"])) { ?>
                        <div class="task-update-counter transitions">
                            <p><a class="tip" href="task.php?tid=<?php echo $activeTasks[$i]["id"]; ?>&pid=<?php echo $pid; ?>" role="link" title="This task has been updated <b><?php echo $updates->countTasksUpdates($pid, $activeTasks[$i]["id"]); ?></b> times"><?php echo $updates->countTasksUpdates($pid, $activeTasks[$i]["id"]); ?></a></p>
                        </div>
                        <?php } ?>
                    </div>
                    <!-- /task -->

                    </li>

                    <?php } ?>
                    
                    </ul>

                    </div>

                    <!-- task header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p>Resolved tasks</p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip sorted" id="by-resolved" href="#" title="Sort by date of resolving the task">Resolved</a></p>
                        </div>
                        <div class="task-user">
                            <p>Resolved by</p>
                        </div>
                    </div>
                    <!-- /task header -->

                    <ul class="listing">

                    <?php
                    for ($i=0; $i < count($resolvedTasks); $i++) {
                    ?>
                    
                    <li data-id="<?php echo $resolvedTasks[$i]["id"]; ?>" data-expire="<?php echo $resolvedTasks[$i]["expire"]; ?>" data-created="<?php echo $resolvedTasks[$i]["created"]; ?>" data-assigned="<?php echo $users->getShortUserName($tasks->getAssignedTaskUser($pid, $resolvedTasks[$i]["id"])); ?>" data-completed="<?php echo $resolvedTasks[$i]["completed"]; ?>" data-title="<?php echo $resolvedTasks[$i]["title"]; ?>" data-mine="<?php if ($tasks->isTaskMine($resolvedTasks[$i]["project"], $resolvedTasks[$i]["id"], $session->get("userid"))) { echo '1'; } else { echo '0'; } ?>">

                    <!-- task -->
                    <div class="task task-bg-resolved">
                        <div class="task-title">
                            <p><a class="tip" href="task.php?tid=<?php echo $resolvedTasks[$i]["id"]; ?>&pid=<?php echo $pid; ?>" role="link" title="<?php echo strip_tags($resolvedTasks[$i]["description"]); ?>"><?php echo $resolvedTasks[$i]["title"]; ?></a></p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip" href="#" role="link" title="<?php echo $utilities->formatRemainingDate($resolvedTasks[$i]["completed"], SHORT_DATE_FORMAT, ""); ?>"><?php echo $utilities->formatDate($resolvedTasks[$i]["completed"], SHORT_DATE_FORMAT, ""); ?></a></p>
                        </div>
                        <div class="task-user">
                            <p>
                                <a class="tip" href="#" role="link" title="<?php echo $users->getFullUserName($tasks->getCompletedTaskUser($pid, $resolvedTasks[$i]["id"])); ?>">
                                <?php 
                                    if (isset($resolvedTasks[$i]["finished"])) { 
                                        echo $users->getShortUserName($tasks->getCompletedTaskUser($pid, $resolvedTasks[$i]["id"])); 
                                    }; 
                                ?>
                                </a>
                            </p>
                        </div>
                        <?php if (defined("UPDATE_COUNTER") && UPDATE_COUNTER && !$tasks->isTaskEmpty($pid, $resolvedTasks[$i]["id"])) { ?>
                        <div class="task-update-counter transitions">
                            <p><a class="tip" href="task.php?tid=<?php echo $resolvedTasks[$i]["id"]; ?>&pid=<?php echo $pid; ?>" role="link" title="This task has been updated <b><?php echo $updates->countTasksUpdates($pid, $resolvedTasks[$i]["id"]); ?></b> times"><?php echo $updates->countTasksUpdates($pid, $resolvedTasks[$i]["id"]); ?></a></p>
                        </div>
                        <?php } ?>
                    </div>
                    <!-- /task -->

                    </li>

                    <?php } ?>

                    </ul>



                    <!-- task header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p>Closed tasks</p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip sorted" id="by-completed" href="#" title="Sort by task completion date">Completed</a></p>
                        </div>
                        <div class="task-user">
                            <p>Completed by</p>
                        </div>
                    </div>
                    <!-- /task header -->

                    <ul class="listing">

                    <?php
                    for ($i=0; $i < count($closedTasks); $i++) {
                    ?>

                    <li data-id="<?php echo $closedTasks[$i]["id"]; ?>" data-expire="<?php echo $closedTasks[$i]["expire"]; ?>" data-created="<?php echo $closedTasks[$i]["created"]; ?>" data-assigned="<?php echo $users->getShortUserName($tasks->getAssignedTaskUser($pid, $closedTasks[$i]["id"])); ?>" data-completed="<?php echo $closedTasks[$i]["completed"]; ?>" data-title="<?php echo $closedTasks[$i]["title"]; ?>" data-mine="<?php if ($tasks->isTaskMine($closedTasks[$i]["project"], $closedTasks[$i]["id"], $session->get("userid"))) { echo '1'; } else { echo '0'; } ?>">

                    <!-- task -->
                    <div class="task task-bg-closed">
                        <div class="task-title">
                            <p><a class="tip" href="task.php?tid=<?php echo $closedTasks[$i]["id"]; ?>&pid=<?php echo $pid; ?>" role="link" title="<?php echo strip_tags($closedTasks[$i]["description"]); ?>"><?php echo $closedTasks[$i]["title"]; ?></a></p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip" href="#" role="link" title="<?php echo $utilities->formatRemainingDate($closedTasks[$i]["completed"], SHORT_DATE_FORMAT, ""); ?>"><?php echo $utilities->formatDate($closedTasks[$i]["completed"], SHORT_DATE_FORMAT, ""); ?></a></p>
                        </div>
                        <div class="task-user">
                            <p>
                                <a class="tip" href="#" role="link" title="<?php echo $users->getFullUserName($tasks->getCompletedTaskUser($pid, $closedTasks[$i]["id"])); ?>">
                                <?php 
                                    if (isset($closedTasks[$i]["finished"])) { 
                                        echo $users->getShortUserName($tasks->getCompletedTaskUser($pid, $closedTasks[$i]["id"])); 
                                    }; 
                                ?>
                                </a>
                            </p>
                        </div>
                        <?php if (defined("UPDATE_COUNTER") && UPDATE_COUNTER && !$tasks->isTaskEmpty($pid, $closedTasks[$i]["id"])) { ?>
                        <div class="task-update-counter transitions">
                            <p><a class="tip" href="task.php?tid=<?php echo $closedTasks[$i]["id"]; ?>&pid=<?php echo $pid; ?>" role="link" title="This task has been updated <b><?php echo $updates->countTasksUpdates($pid, $closedTasks[$i]["id"]); ?></b> times"><?php echo $updates->countTasksUpdates($pid, $closedTasks[$i]["id"]); ?></a></p>
                        </div>
                        <?php } ?>
                    </div>
                    <!-- /task -->

                    </li>

                    <?php } ?>

                    </ul>

                    

                </article>
                <!-- project tasks -->

                <!-- complete  -->
                <?php 
                if (!empty($project[0]["completed"]) && $projects->isProjectClosed($pid)) {
                ?>
                <div class="complete" style="margin-top: 20px;">
                <p>
                    <strong>CLOSED</strong> 
                    by:
                    <strong>
                    <?php 
                    echo $users->getShortUserName($project[0]["finished"]);
                    ?>
                    </strong>
                    on
                    <?php echo date(SHORT_DATE_FORMAT, $project[0]["completed"])." at ".date(TIME_FORMAT, $project[0]["completed"]); ?>
                </p>
                </div>
                <?php } ?>
                <!-- /complete  -->

                <?php 
                if ($projects->isProjectEmpty($project[0]["id"])) {
                ?>
                <div id="add-first-task">
                    <a class="blue-button default-button tip shadow" id="add-first" role="button" href="add-task.php?pid=<?php echo $project[0]["id"]; ?>" title="Add first task to project">Add first task to project</a>
                </div>
                <?php } ?>

            </div>             
            <!-- /main content -->

            <div id="dialog-message" title="Closing project" style="display: none;">
                <p>Are you sure you want to close this project? This action will also close all tasks within the project.</p>
            </div>

        </section>
        <!-- /main wrapper -->
        
        
        <!-- footer -->
        <footer>
            <!-- logo -->
            <div id="logo">
                <div id="export">
                    <a class="tip export-link" href="ics.php?pid=<?php echo $pid; ?>&key=<?php echo ACCESS_KEY; ?>" title="Export calendar in ICS format for <?php echo $project[0]["title"]; ?> project">Export project calendar</a>
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