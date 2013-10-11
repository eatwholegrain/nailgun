<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        $openProjects = $projects->listProjectsByStatus($session->get("account"), 1);
        $closedProjects = $projects->listProjectsByStatus($session->get("account"), 0);
        $allUserTodos = $todos->listUserTodos($session->get("account"), $session->get("userid"), 1);

    } else {
        // user not loged
        $utilities->redirect("error.php?code=12");
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?> - My Projects</title>

<!-- styles -->

<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/fonts.css" />
<link rel="stylesheet" href="css/jquery.ui.custom.css" />
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
<script src="js/jquery.stickypanel.js"></script>
<script src="js/jquery.tiptip.js"></script>
<script src="js/jquery.tinysort.js"></script>
<script src="js/script.js"></script>

<!-- remove for production -->
<script src="js/cssrefresh.js"></script>


<!-- javascript -->

<script>
    $(document).ready(function() {

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

        $("#by-tasks").click(function() {
            $("ul.listing > li").tsort('',{order:'desc', attr:'data-tasks'});
            $(".tip").removeClass("sorted");
            $(this).addClass("sorted");
            return false;
        });

        $("ul.listing > li").click(function() {
            window.location.href="project.php?pid="+$(this).attr("data-id");
        });

        $("ul.assignment-listing > li").click(function() {
            window.location.href="my-todos.php?uid="+$(this).attr("data-id");
        });

        $("ul.listing > li").tsort('',{attr:'data-title'});
        $("#by-title").addClass("sorted");

        $("#search-field").autocomplete({
            source: "lib/api/get-my-tasks.php",
            minLength: 3,
            select: function(event, ui) {
                window.location.href = ui.item.id;
            }
        });
        
    })   
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

            <?php if (defined("SHORTCUTS") && SHORTCUTS){ ?>
            <!-- breadcrumbs -->
            <div class="breadcrumbs">
                <a class="tip" href="home.php" title="Home: Select project"><img src="images/home.png"></a>
                <?php 
                if ($users->isOwner($session->get("userid"))) {
                ?>
                <a class="separator"><img src="images/separator.png"></a>
                <a class="tip" href="all-projects.php" title="All Projects"><img src="images/all-projects.png"></a>
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
                if ($users->isOwner($session->get("userid"))) {
                ?>
                <!-- add project -->
                <div class="add">
                    <a id="add-project-button" class="tip" href="add-project.php" role="link" title="Add new project">Add project</a>
                </div>
                <!-- /add project -->
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
                <h1>YOUR PROJECTS</h1>
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

                <!-- project tasks -->
                <article id="project-tasks">

                    <!-- project header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p><a class="tip" id="by-title" href="#" title="Sort by project name">Open projects</a></p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip" id="by-date" href="#" title="Sort by project expiration date">Due</a></p>
                        </div>
                        <div class="task-user">
                            <p><a class="tip" id="by-tasks" href="#" title="Sort by project open tasks">Open tasks</a> <a class="tip info-icon" title="Only task assigned to me" href="#"><img src="images/help.png"></a></p>
                        </div>
                    </div>
                    <!-- /project header -->

                    <ul class="listing">

                    <?php

                    $userOpenProjects = 0;

                    for ($i=0; $i < count($openProjects); $i++) {
                        if ($roles->canViewProject($openProjects[$i]["id"], $session->get("userid"))) {
                        ?>

                        <li data-id="<?php echo $openProjects[$i]["id"]; ?>" data-expire="<?php echo $openProjects[$i]["expire"]; ?>" data-created="<?php echo $openProjects[$i]["created"]; ?>" data-tasks="<?php echo $tasks->countUserOpenTasks($openProjects[$i]["id"], $session->get("userid")); ?>" data-title="<?php echo $openProjects[$i]["title"]; ?>">

                        <!-- project -->
                        <div class="task task-bg <?php echo $utilities->setColorClass($openProjects[$i]["expire"]); ?>">
                            <div class="task-title <?php if($projects->isProjectExpired($openProjects[$i]["id"])){ echo 'striked';} ?>">
                                <p><a class="tip" href="project.php?pid=<?php echo $openProjects[$i]["id"]; ?>" role="link" title="<?php echo strip_tags($openProjects[$i]["description"]); ?>"><?php echo $openProjects[$i]["title"]; ?></a></p>
                            </div>
                            <div class="task-date">
                                <p><a class="tip" href="#" role="link" title="Due: <?php echo $utilities->formatRemainingDate($openProjects[$i]["expire"], SHORT_DATE_FORMAT); ?>"><?php echo $utilities->formatDate($openProjects[$i]["expire"], SHORT_DATE_FORMAT); ?></a></p>
                            </div>
                            <div class="task-user">
                                <p><?php echo $tasks->countUserOpenTasks($openProjects[$i]["id"], $session->get("userid")); ?></p>
                            </div>
                        </div>
                        <!-- /project -->

                        </li>

                    <?php
                        $userOpenProjects++;
                        } 
                    } 
                    ?>

                    </ul>
                            

                    <!-- project header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p>Closed projects</p>
                        </div>
                    </div>
                    <!-- /project header -->

                    <ul class="listing">

                    <?php
                    for ($i=0; $i < count($closedProjects); $i++) {
                        if ($roles->canViewProject($closedProjects[$i]["id"], $session->get("userid"))) {
                        ?>

                        <li data-id="<?php echo $closedProjects[$i]["id"]; ?>" data-expire="<?php echo $closedProjects[$i]["expire"]; ?>" data-created="<?php echo $closedProjects[$i]["created"]; ?>" data-tasks="<?php echo $tasks->countUserOpenTasks($closedProjects[$i]["id"], $session->get("userid")); ?>" data-title="<?php echo $closedProjects[$i]["title"]; ?>">

                        <!-- project -->
                        <div class="task task-bg-closed">
                            <div class="task-title">
                                <p><a class="tip" href="#" role="link" title="<?php echo strip_tags($closedProjects[$i]["description"]); ?>"><?php echo $closedProjects[$i]["title"]; ?></a></p>
                            </div>
                        </div>
                        <!-- /project -->

                        </li>

                    <?php 
                        } else {
                            $userClosedProjects = 0;
                        }
                    } 
                    ?>

                    </ul>

                    <?php if($userOpenProjects == 0) { ?>
                    <div id="add-first-task" style="margin-bottom: 20px;">
                        <a class="blue-button default-button shadow tip" role="button" href="#" title="There are no open projects assigned to you at this moment" onClick="return false;">There are no open projects assigned to you</a>
                    </div>
                    <?php } ?>

                    <?php 
                    if(!empty($allUserTodos)) {  
                    ?>

                    <!-- todo header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p>Loose Nails</p>
                        </div>
                    </div>
                    <!-- /todo header -->

                    <ul class="assignment-listing">

                    <li data-id="<?php echo $session->get("userid"); ?>">
                    <!-- todo -->
                    <div class="task task-bg">
                        <div class="task-title">
                            <p><a class="tip" href="my-todos.php?uid=<?php echo $session->get("userid"); ?>" role="link" title="View All Your Loose Tasks">Loose Tasks</a></p>
                        </div>
                        <div class="task-date">
                            <p></p>
                        </div>
                        <div class="task-user">
                            <p><a class="tip" href="#" role="link" title=""><?php echo count($allUserTodos); ?></a></p>
                        </div>
                    </div>
                    <!-- /todo -->

                    </li>

                    </ul>

                    <?php
                    }
                    ?>

                </article>
                <!-- project tasks -->

            </div>             
            <!-- /main content -->
        </section>
        <!-- /main wrapper -->
        
        
        <!-- footer -->
        <footer>
            <!-- logo -->
            <div id="logo">
                <img class="ng-logo" src="images/logo.png" alt="<?php echo APPLICATION_TITLE ?> logo" />
            </div>
            <!-- /logo -->
            
        </footer>
        <!-- /footer -->
            
    </div>
    <!-- /wrap -->
</body>
</html>