<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if (!empty($_GET["pid"])) {

            $pid = $utilities->filter($_GET["pid"]);

            if ($projects->isAccountProject($pid, $session->get("account"))) {

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
                        
                    } else if ($action == "open"){

                        $projects->openProject($pid);
                        $notice = "Project is open again. Please open all required tasks";

                    } 
                }

                // who can view projects
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
        $utilities->redirect("error.php?code=12");
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?> - Calendar for <?php echo $project[0]["title"]; ?></title>

<!-- styles -->

<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/fonts.css" />
<link rel="stylesheet" href="css/jquery.ui.custom.css" />
<link rel="stylesheet" href="css/jquery.ui.achtung.css" />
<link rel="stylesheet" href="css/jquery.fullcalendar.css" />
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
<script src="js/jquery.fullcalendar.js"></script>
<script src="js/script.js"></script>

<!-- remove for production -->
<script src="js/cssrefresh.js"></script>


<!-- javascript -->

<script>
    $(document).ready(function() {
        
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        
        
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay'
            },
            editable: false,
            firstDay: 1,
            events: [

                <?php
                for ($i=0; $i < count($activeTasks); $i++) {
                ?>
                {
                    title: '<?php echo $activeTasks[$i]["title"]; ?>',
                    start: new Date(<?php echo date("Y", $activeTasks[$i]["created"]); ?>, <?php echo date("n-1", $activeTasks[$i]["created"]); ?>, <?php echo date("j", $activeTasks[$i]["created"]); ?>),
                    end: new Date(<?php echo date("Y", $activeTasks[$i]["expire"]); ?>, <?php echo date("n-1", $activeTasks[$i]["expire"]); ?>, <?php echo date("j", $activeTasks[$i]["expire"]); ?>),
                    url: 'task.php?tid=<?php echo $activeTasks[$i]["id"]; ?>&pid=<?php echo $pid; ?>',
                    allDay: true,
                    className: '<?php echo $utilities->setColorClass($activeTasks[$i]["expire"]); ?>'

                },
                <?php
                }
                ?>
            ],
            eventClick: function(event, jsEvent, view) {},
            eventMouseover: function(event, jsEvent, view) {},
            eventMouseout: function(event, jsEvent, view) {}
            
        });

        //$.fullCalendar({dayNamesShort: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']});
        
        $("#search-field").autocomplete({
            source: "lib/api/get-my-tasks.php",
            minLength: 3,
            select: function(event, ui) {
                window.location.href = ui.item.id;
            }
        });
    })   
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
                <a class="tip" href="project-files.php?pid=<?php echo $project[0]["id"]; ?>" title="Files: <?php echo $project[0]["title"]; ?>"><img src="images/task-files.png"></a>
                <?php 
                if ($users->isOwner($session->get("userid"))) {
                    ?>
                    <a id="edit-project" class="tip" href="edit-project.php?pid=<?php echo $project[0]["id"]; ?>" title="Edit Project: <?php echo $project[0]["title"]; ?>"><img src="images/edit-project.png"></a>
                    <?php
                    if ($projects->isProjectOpen($project[0]["id"])) {
                    ?>
                    <a class="tip" href="project.php?pid=<?php echo $project[0]["id"]; ?>&action=close" title="Close Project: <?php echo $project[0]["title"]; ?>"><img src="images/close-projects.png"></a>
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
                <h1><span class="<?php if($projects->isProjectExpired($project[0]["id"])){ echo 'striked';} ?>"><?php echo $project[0]["title"]; ?></span></h1>
            </div>
            
            
        </header>
        <!-- /header -->
        
        <!-- main wrapper -->
        <section id="main-wrapper">
            <!-- main content -->
            <div id="main-content" class="clearer">

                <article>

                    <div class="spacer"></div>

                    <!-- calendar -->
                    <div id="calendar"></div>
                    <!-- /calendar -->

                </article>

            </div>             
            <!-- /main content -->
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