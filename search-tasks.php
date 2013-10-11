<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        $term = $utilities->filter($_GET["s"]);
        $allSearchedTasks = $searches->searchAllTasks($term);
        $allActiveUserTasks = $tasks->listUserTasks($session->get("userid"), 1);

    } else {
        // user not loged
        $utilities->redirect("error.php?code=12");
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?> - Search Results</title>

<!-- styles -->

<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/fonts.css" />
<link rel="stylesheet" href="css/jquery.ui.custom.css" />
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
<script src="js/jquery.stickypanel.js"></script>
<script src="js/jquery.ui.achtung.js"></script>
<script src="js/jquery.tiptip.js"></script>
<script src="js/jquery.tinysort.js"></script>
<script src="js/jquery.highlight.js"></script>
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

        $("#by-project").click(function() {
            $("ul.listing > li").tsort('',{attr:'data-project'});
            $(".tip").removeClass("sorted");
            $(this).addClass("sorted");
            return false;
        });

        $("ul.listing > li").click(function() {
            window.location.href="task.php?pid="+$(this).attr("data-projectid")+"&tid="+$(this).attr("data-id");
        });

        $('.listing').highlight('<?php echo $term;?>');

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
                <?php 
                if ($users->isOwner($session->get("userid"))) {
                ?>
                <a class="separator"><img src="images/separator.png"></a>
                <a class="tip" href="all-projects.php" title="All Projects"><img src="images/all-projects.png"></a>
                <a class="tip" href="#" title="All Tasks"><img src="images/all-tasks.png"></a>
                <a class="tip" href="all-users.php" title="All Users"><img src="images/all-users.png"></a>
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
                <h1>SEARCH RESULTS</h1>
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

                <!-- project description -->
                <article id="project-description">
                    <p>Search results for: <strong><?php echo $term;?></strong></p>
                </article> 
                <!-- /project description -->

                <!-- project tasks -->
                <article id="project-tasks">

                    <div class="sortable">

                    <!-- task header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p><a class="tip" id="by-title" href="#" title="Sort by task name">Tasks</a></p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip sorted" id="by-date" href="#" title="Sort by task expiration date">Due</a></p>
                        </div>
                        <div class="task-user">
                            <p><a class="tip" id="by-project" href="#" title="Sort by project name">Project</a></p>
                        </div>
                    </div>
                    <!-- /task header -->

                    <ul class="listing">

                    <?php
                    $index = 0;
                    for ($i=0; $i < count($allSearchedTasks); $i++) {
                    ?>
                        <?php
                        $activeTasksProject = $projects->getProject($allSearchedTasks[$i]["project"]);

                        if ($projects->isProjectOpen($activeTasksProject[0]["id"]) && $roles->canViewProject($activeTasksProject[0]["id"], $session->get("userid"))) {
                        ?>

                        <li data-id="<?php echo $allSearchedTasks[$i]["id"]; ?>" data-expire="<?php echo $allSearchedTasks[$i]["expire"]; ?>" data-created="<?php echo $allSearchedTasks[$i]["created"]; ?>" data-assigned="<?php echo $users->getShortUserName($tasks->getAssignedTaskUser($activeTasksProject[0]["id"], $allSearchedTasks[$i]["id"])); ?>" data-title="<?php echo $allSearchedTasks[$i]["title"]; ?>" data-project="<?php echo $projects->getProjectTitle($activeTasksProject[0]["id"]);?>" data-projectid="<?php echo $projects->getProjectId($activeTasksProject[0]["id"]);?>">

                        <!-- task -->
                        <div class="task task-bg <?php if ($tasks->isTaskHasPriority($allSearchedTasks[$i]["project"], $allSearchedTasks[$i]["id"])) { echo 'high'; } ?> <?php echo $utilities->setColorClass($allSearchedTasks[$i]["expire"]); ?>">
                            <div class="task-title <?php if($tasks->isTaskExpired($allSearchedTasks[$i]["project"], $allSearchedTasks[$i]["id"])){ echo'striked';} ?>">
                                <p><a class="tip" href="task.php?tid=<?php echo $allSearchedTasks[$i]["id"]; ?>&pid=<?php echo $allSearchedTasks[$i]["project"]; ?>" role="link" title="<?php echo strip_tags($allSearchedTasks[$i]["description"]); ?>"><?php echo $allSearchedTasks[$i]["title"]; ?></a></p>
                            </div>
                            <div class="task-date">
                                <p><a class="tip" href="#" role="link" title="Due: <?php echo $utilities->formatRemainingDate($allSearchedTasks[$i]["expire"], SHORT_DATE_FORMAT); ?>"><?php echo $utilities->formatDate($allSearchedTasks[$i]["expire"], SHORT_DATE_FORMAT); ?></a></p>
                            </div>
                            <div class="task-user task-project">
                                <p><a class="tip" href="project.php?pid=<?php echo $allSearchedTasks[$i]["project"]; ?>" role="link" title="<?php echo strip_tags($activeTasksProject[0]["description"]); ?>"><?php echo $activeTasksProject[0]["title"]; ?></a></p>
                            </div>
                        </div>
                        <!-- /task -->

                        </li>

                    <?php

                        $index++;

                        }
                        
                    } 
                    ?>

                    </ul>

                    <?php if($index == 0) {?>
                    <!-- results has no tasks -->
                    <div id="add-first-task" style="margin-bottom: 20px;">
                        <a class="blue-button default-button shadow tip" role="button" href="#" title="" onClick="return false;">No results for <?php echo $term;?></a>
                    </div>
                    <!-- /results has no tasks -->
                    <?php } ?>

                    </div>

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