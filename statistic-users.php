<?php
    set_time_limit(50);

    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if ($users->isAdmin($session->get("userid"))) {

            if (!empty($_GET["uid"])) {

                $uid = $utilities->filter($_GET["uid"]);
                $allUsers = $users->listSpecificUsers($uid, "role");

            } else {

                $allUsers = $users->listAllUsers($session->get("account"), "role");
            }
            
            $openProjects = $projects->listProjectsByStatus($session->get("account"), 1);

        } else {
            // permission problem
            $utilities->redirect("error.php?code=5");
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
<title><?php echo APPLICATION_TITLE ?> - User Statistics</title>

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

        $("#by-email").click(function() {
            $("ul.listing > li").tsort('',{attr:'data-email'});
            $(".tip").removeClass("sorted");
            $(this).addClass("sorted");
            return false;
        });

        $("#by-role").click(function() {
            $("ul.listing > li").tsort('',{order:'asc', attr:'data-role'});
            $(".tip").removeClass("sorted");
            $(this).addClass("sorted");
            return false;
        });

        $("#search-field").autocomplete({
            source: "lib/api/get-my-tasks.php",
            minLength: 3,
            select: function(event, ui) {
                window.location.href = ui.item.id;
            }
        });

        $("ul.listing > li").click(function() {
            $(this).find(".accordion").slideDown();
        });

        // initial sort by name
        $("ul.listing > li").tsort('',{attr:'data-title'});
        $("#by-title").addClass("sorted");

        $(".accordion").accordion({
            collapsible: true,
            heightStyle: "content",
            active : 'none'
        });

        $("ul.listing li").find(".accordion[data-user='<?php echo $session->get("userid"); ?>']").css("margin-bottom", "20px").slideToggle();

        $(function() {
            var $this = $("ul.listing li[data-id='<?php echo $session->get("userid"); ?>']");
            $this.insertBefore($this.siblings(':eq(0)'));
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
                <a class="tip" href="all-tasks.php" title="All Tasks"><img src="images/all-tasks.png"></a>
                <?php } ?>
                <?php 
                if ($users->isAdmin($session->get("userid"))) {
                ?>
                <a class="tip" href="all-users.php" title="Users"><img src="images/all-users.png"></a>
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
                <!-- add user -->
                <div class="add">
                    <a id="add-user-button" class="tip" href="add-user.php" role="link" title="Add new user">Add user</a>
                </div>
                <!-- /add user -->
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
                <h1>USER STATISTICS</h1>
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

                    <!-- user header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p><a class="tip" id="by-title" href="#" title="Sort users by first name">Name</a></p>
                        </div>
                        <div class="user-date">
                            <p><a class="tip" id="by-email" href="#" title="Sort users by email address">Email</a></p>
                        </div>
                        <div class="user-user">
                            <p><a class="tip" id="by-role" href="#" title="Sort users by role">Role</a>&nbsp;&nbsp;<a class="tip info-icon" title="Number of open tasks in project" href="#"><img src="images/help.png"></a></p>
                        </div>
                    </div>
                    <!-- /user header -->

                    <ul class="listing accordion-listing">

                    <?php
                    for ($i=0; $i < count($allUsers); $i++) {
                    ?>

                    <li data-id="<?php echo $allUsers[$i]["id"]; ?>" data-title="<?php echo $allUsers[$i]["firstname"]; ?>" data-email="<?php echo $allUsers[$i]["email"]; ?>" data-role="<?php echo $allUsers[$i]["role"]; ?>" >

                    <!-- user -->
                    <div class="task task-bg <?php echo $utilities->setUserColorClass($allUsers[$i]["role"]); ?>">
                        <div class="user-title">
                            <p><?php echo $allUsers[$i]["firstname"]." ".$allUsers[$i]["lastname"]; ?></p>
                        </div>
                        <div class="user-date">
                            <p><a class="tip" href="mailto:<?php echo $allUsers[$i]["email"]; ?>" title="Send message to: <?php echo $allUsers[$i]["email"]; ?>"><?php echo $allUsers[$i]["email"]; ?></a></p>
                        </div>
                        <div class="user-user">
                            <p><?php echo $users->getUserStatus($allUsers[$i]["id"]); ?></p>
                        </div>

                        
                    </div>
                    <!-- /user -->

                    <div class="accordion" data-user="<?php echo $allUsers[$i]["id"]; ?>">
                        <?php
                        for ($x=0; $x < count($openProjects); $x++) {
                            $open = $tasks->countUserOpenTasks($openProjects[$x]["id"], $allUsers[$i]["id"]);
                            //$resolved = $tasks->countUserResolvedTasks($openProjects[$x]["id"], $allUsers[$i]["id"]);
                            //$closed = $tasks->countUserClosedTasks($openProjects[$x]["id"], $allUsers[$i]["id"]);
                        if($open > 0) {
                            $allActiveUserTasks = $tasks->listUserProjectTasks($openProjects[$x]["id"], $allUsers[$i]["id"], 1);
                            ?>
                            <h3 data-project="<?php echo $openProjects[$x]["id"]; ?>" data-user="<?php echo $allUsers[$i]["id"]; ?>"><?php echo $openProjects[$x]["title"]; ?> <a class="tip user-open-task" href="#" title="<?php echo $allUsers[$i]["firstname"]?> has <?php echo $open; ?> task(s) open in this project"><?php echo $open; ?></a></h3>
                            <div>
                                <?php
                                for ($y=0; $y < count($allActiveUserTasks); $y++) {
                                ?>
                                <div class="task-list-item">
                                    <a class="tip" href="task.php?tid=<?php echo $allActiveUserTasks[$y]["id"]; ?>&pid=<?php echo $allActiveUserTasks[$y]["project"]; ?>" title="View <?php echo $allActiveUserTasks[$y]["title"]; ?> task"><?php echo $allActiveUserTasks[$y]["title"]; ?> <span class="task-list-item-expire"><?php echo $utilities->formatRemainingDate($allActiveUserTasks[$y]["expire"], SHORT_DATE_FORMAT); ?></span></a>
                                </div>
                                <?php
                                }
                                ?>
                            </div>

                        <?php
                        }}
                        ?>
                    </div>

                    </li>

                    <?php } ?>

                    </ul>

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