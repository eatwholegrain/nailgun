<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if ($users->isAdmin($session->get("userid"))) {

            $allUsers = $users->listAllUsers($session->get("account"), "role");

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
<title><?php echo APPLICATION_TITLE ?> - Users</title>

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

        $("ul.listing > li").click(function() {
            window.location.href="user.php?uid="+$(this).attr("data-id");
        });

        $("#search-field").autocomplete({
            source: "lib/api/get-my-tasks.php",
            minLength: 3,
            select: function(event, ui) {
                window.location.href = ui.item.id;
            }
        });

        // initial sort by name
        $("ul.listing > li").tsort('',{attr:'data-title'});
        $("#by-title").addClass("sorted");
        
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
                <?php 
                if ($users->isOwner($session->get("userid"))) {
                ?>
                <a class="tip" href="all-projects.php" title="All Projects"><img src="images/all-projects.png"></a>
                <a class="tip" href="all-tasks.php" title="All Tasks"><img src="images/all-tasks.png"></a>
                <?php } ?>
                <?php 
                if ($users->isAdmin($session->get("userid"))) {
                ?>
                <a class="tip" href="#" title="Users"><img src="images/all-users.png"></a>
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
                <h1>USERS</h1>
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
                    <div class="task-header user-header">
                        <div class="task-title user-title">
                            <p><a class="tip" id="by-title" href="#" title="Sort users by first name">Name</a></p>
                        </div>
                        <div class="user-date">
                            <p><a class="tip" id="by-email" href="#" title="Sort users by email address">Email</a></p>
                        </div>
                        <div class="user-user">
                            <p><a class="tip" id="by-role" href="#" title="Sort users by role">Role</a></p>
                        </div>
                    </div>
                    <!-- /user header -->

                    <ul class="listing">

                    <?php
                    for ($i=0; $i < count($allUsers); $i++) {
                    ?>

                    <li data-id="<?php echo $allUsers[$i]["id"]; ?>" data-title="<?php echo $allUsers[$i]["firstname"]; ?>" data-email="<?php echo $allUsers[$i]["email"]; ?>" data-role="<?php echo $allUsers[$i]["role"]; ?>" >

                    <!-- user -->
                    <div class="task task-bg <?php echo $utilities->setUserColorClass($allUsers[$i]["role"]); ?>">
                        <div class="user-title">
                            <p><a class="tip" href="user.php?uid=<?php echo $allUsers[$i]["id"]; ?>" title="Registered on <?php echo $utilities->formatDateTime($allUsers[$i]['created'], LONG_DATE_FORMAT, TIME_FORMAT); ?>"><?php echo $allUsers[$i]["firstname"]." ".$allUsers[$i]["lastname"]; ?></a></p>
                        </div>
                        <div class="user-date">
                            <p><a class="tip" href="mailto:<?php echo $allUsers[$i]["email"]; ?>" title="Send message to: <?php echo $allUsers[$i]["email"]; ?>"><?php echo $allUsers[$i]["email"]; ?></a></p>
                        </div>
                        <div class="user-user">
                            <p><?php echo $users->getUserStatus($allUsers[$i]["id"]); ?></p>
                        </div>
                        
                        <?php 
                        if ($users->isAdmin($session->get("userid"))) {
                        ?>
                        <div class="view-user-stat transitions">
                            <p><a class="tip" href="statistic-users.php?uid=<?php echo $allUsers[$i]["id"]; ?>" role="link" title="View <?php echo $allUsers[$i]["firstname"]; ?> Statistics"><img src="images/statistics.png"></a></p>
                        </div>
                        <div class="all-user-todo transitions">
                            <p><a class="tip" href="user-todos.php?uid=<?php echo $allUsers[$i]["id"]; ?>" role="link" title="View all <?php echo $allUsers[$i]["firstname"]; ?> Assignments"><img src="images/all-todos.png"></a></p>
                        </div>
                        <div class="add-user-todo transitions">
                            <p><a class="tip" href="add-todo.php?uid=<?php echo $allUsers[$i]["id"]; ?>" role="link" title="Create Assignment for <?php echo $allUsers[$i]["firstname"]; ?>"><img src="images/add-todo.png"></a></p>
                        </div>
                        <?php } ?>
                    </div>
                    <!-- /user -->

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