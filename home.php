<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        $tasksCount = $tasks->countUserActiveTasks($session->get("userid"));
        $assignmentCount = $todos->countUserTodos($session->get("userid"));

        $notice = "Hi <strong>".$session->get("firstname")."</strong>. Please select project to begin";

    } else {
        // user not loged
        $utilities->redirect("index.php");
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?> - Home</title>

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
<script src="js/script.js"></script>


<!-- remove for production -->
<script src="js/cssrefresh.js"></script>


<!-- javascript -->

<script>
    $(document).ready(function() {
        //
        $("select#selected-project").selectmenu({
            width: 320,
            maxHeight: 320,
            change: function(e, object) {
                if (object.value > 0) {
                    window.location.href = "project.php?pid="+ object.value;
                } else {
                    if (object.value == -1) {
                        window.location.href = "my-todos.php?uid=<?php echo $session->get("userid"); ?>";
                    } else {
                        $.achtung({message: 'Select one of the projects to continue'});
                    }
                }
            },
            open: function(e, object) {
                
            },
            close: function(e, object) {
                
            } 
        });

        $("#nail-it").click(function() {

            var project = $("select#selected-project").val();

            if (project > 0) {
                window.location.href = "project.php?pid="+ project;
            } else {
                $.achtung({message: 'Select one of the projects to continue', timeout: 7});
            }

            return false;
        });

        $("#search-field").autocomplete({
            source: "lib/api/get-my-tasks.php",
            minLength: 3,
            select: function(event, ui) {
                window.location.href = ui.item.id;
            }
        });

        <?php
        $utilities->notify($notice, 7);

        if($tasksCount>0 || $assignmentCount>0) {
            $utilities->notify("You have <b>".$tasksCount."</b> open task".$utilities->singular($tasksCount)." and <b>".$assignmentCount."</b> assignment".$utilities->singular($assignmentCount)."", 7);
        } else {
            $utilities->notify("You currently have no open tasks and assignments", 7);
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
            <?php if (defined("SHORTCUTS") && SHORTCUTS) { ?>
            <!-- breadcrumbs -->
            <div class="breadcrumbs">
                <a class="tip" href="#" title="Home: Select project"><img src="images/home.png"></a>
                <a class="separator"><img src="images/separator.png"></a>
                <?php
                if (!$users->isAdmin($session->get("userid"))) {
                ?>
                <a class="tip" href="my-tasks.php" title="Tell me what I need to do today"><img src="images/user.png"></a>
                <?php } ?>
                <?php 
                if ($users->isOwner($session->get("userid"))) {
                ?>
                <a class="tip" href="all-projects.php" title="All Projects"><img src="images/all-projects.png"></a>
                <a class="tip" href="all-tasks.php" title="All Tasks"><img src="images/all-tasks.png"></a>
                <a class="tip" href="all-users.php" title="All Users"><img src="images/all-users.png"></a>
                <?php } ?>
                <?php
                if ($users->isAdmin($session->get("userid"))) {
                ?>
                <a class="tip" href="statistic-users.php" title="User statistics"><img src="images/statistics.png"></a>
                <?php } ?>
            </div>
            <!-- /breadcrumbs -->
            <?php } ?>

            <!-- welcome message -->
            <div id="welcome-message">
                <p>Welcome to <?php echo APPLICATION_TITLE ?> 
                    <span class="orange"><?php echo $session->get("firstname"); ?></span>
                    <?php if($tasksCount > 0) { ?><a class="tip tasks-count" href="my-tasks.php" role="link" title="You have <b><?php echo $tasksCount; ?></b> open task<?php echo $utilities->singular($tasksCount); ?>"><b><?php echo $tasksCount; ?></b> task<?php echo $utilities->singular($tasksCount); ?></a><?php } ?>
                    <?php if($assignmentCount > 0) { ?><a class="tip assignment-count" href="my-todos.php" role="link" title="You have <b><?php echo $assignmentCount; ?></b> open loose task<?php echo $utilities->singular($assignmentCount); ?>"><b><?php echo $assignmentCount; ?></b> loose task<?php echo $utilities->singular($assignmentCount); ?></a><?php } ?>
                </p>
            </div>
            <!-- /welcome message -->

            <!-- top panel -->
            <div id="top-panel">

                <?php 
                if ($users->isOwner($session->get("userid"))) {
                ?>
                <!-- add project -->
                <div  class="add">
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

            <!-- loader -->
            <div id="loader">
                <img src="images/loading.gif" alt="Loading page" />
            </div>
            <!-- /loader -->
            
        </header>
        <!-- /header -->
        
        <!-- main wrapper -->
        <section id="main-wrapper" class="no-bg">
            <!-- main content -->
            <div id="main-content">
                <!-- page content -->
                <article id="select-project">

                    <form id="project-list" action="search-tasks.php" method="get">
                                    
                        <fieldset>
                            <label class="large-label" for="selected-project">Choose a project</label>
                            <select name="selected-project" id="selected-project">
                                <option value="0">...</option>
                            <optgroup label="Loose Tasks">
                                <option value="-1">Loose Nails</option>
                            </optgroup>
                            <optgroup label="All Projects">
                            <?php
                            $allProjects = $projects->listAllProjects($session->get("account"));
                            for ($i=0; $i < count($allProjects); $i++) {
                                if ($users->isAdmin($session->get("userid")) || $roles->canViewProject($allProjects[$i]["id"], $session->get("userid"))) {
                                ?>
                                    <option value="<?php echo $allProjects[$i]["id"]; ?>"><?php echo $allProjects[$i]["title"]; ?></option>
                                <?php 
                                }
                            } 
                            ?>
                            </select>

                            <a class="orange-button default-button tip" id="nail-it" role="button" href="#" title="View all tasks for selected project">NAIL IT</a>
                        </fieldset>

                        <br/>
                        <br/>

                        <fieldset>
                            <label class="large-label">Or</label>
                            <a class="blue-button default-button tip" id="todo" role="button" href="my-tasks.php" title="You have <b><?php echo $tasksCount; ?></b> open task<?php echo $utilities->singular($tasksCount); ?> and <b><?php echo $assignmentCount; ?></b> assignment<?php echo $utilities->singular($assignmentCount); ?>">Tell me what I need to do today</a>
                            
                            <!--<a class="blue-button default-button tip" href="#"><?php echo $tasksCount; ?></a>-->
                        </fieldset>

                        <br/>
                        <br/>

                        <fieldset>
                            <label class="large-label">Or</label>
                            <input style="float: left;" name="s" type="text" id="search-field" class="text-input rounded" placeholder="Search tasks" required>
                            <a style="float: left; margin: 11px 0 11px 5px;" class="blue-button default-button tip" id="search-button" role="button" href="#" title="">SEARCH</a>
                        </fieldset>

                    </form>

                </article>
                <!-- /page content -->
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