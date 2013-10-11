<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if (!empty($_GET["uid"])) {

            $uid = $utilities->filter($_GET["uid"]);
            $user = $users->getUser($uid);

            $allActiveUserTodos = $todos->listUserTodos($session->get("account"), $uid, 1);
            $allResolvedUserTodos = $todos->listUserTodos($session->get("account"), $uid, 2);
            $allClosedUserTodos = $todos->listUserTodos($session->get("account"), $uid, 3);

        } else {
            // user not specified
            $utilities->redirect("error.php?code=3");
        }


    } else {
        // user not loged
        $utilities->redirect("index.php?redirection=my-todos.php");
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?> - All <?php echo $user[0]["firstname"]; ?>'s Loose Nails</title>

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
            $("ul.assignment-listing > li").tsort('',{attr:'data-title'});
            $(".tip").removeClass("sorted");
            $(this).addClass("sorted");
            return false;
        });

        $("#by-date").click(function() {
            $("ul.assignment-listing > li").tsort('',{attr:'data-expire'});
            $(".tip").removeClass("sorted");
            $(this).addClass("sorted");
            return false;
        });

        $("ul.assignment-listing > li").click(function() {
            window.location.href="todo.php?aid="+$(this).attr("data-id");
        });

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
                <?php 
                if ($users->isOwner($session->get("userid"))) {
                ?>
                <a class="separator"><img src="images/separator.png"></a>
                <a class="tip" href="all-users.php" title="Users"><img src="images/all-users.png"></a>
                <a class="tip" href="user.php?uid=<?php echo $user[0]["id"]; ?>" title="User: <?php echo $user[0]["firstname"]; ?>"><img src="images/user.png"></a>
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
                if ($users->isAdmin($session->get("userid"))) {
                ?>
                <!-- add project -->
                <div class="add">
                    <a id="add-project-button" class="tip" href="add-todo.php?uid=<?php echo $user[0]["id"]; ?>" role="link" title="Add new lose task to <?php echo $user[0]["firstname"]; ?>">Add loose task to <?php echo $user[0]["firstname"]; ?></a>
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
                <h1>USER LOOSE NAILS</h1>
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

                    <div class="sortable">

                    <!-- task header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p><a class="tip" id="by-title" href="#" title="Sort by assignment name">Open Loose Tasks</a></p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip sorted" id="by-date" href="#" title="Sort by assignment expiration date">Due</a></p>
                        </div>
                    </div>
                    <!-- /task header -->

                    <ul class="assignment-listing">

                    <?php
                    for ($i=0; $i < count($allActiveUserTodos); $i++) {
                    ?>

                    <li data-id="<?php echo $allActiveUserTodos[$i]["id"]; ?>" data-expire="<?php echo $allActiveUserTodos[$i]["expire"]; ?>" data-created="<?php echo $allActiveUserTodos[$i]["created"]; ?>" data-assigned="<?php echo $allActiveUserTodos[$i]["assigned"];?>" data-title="<?php echo $allActiveUserTodos[$i]["title"]; ?>">
                    <!-- task -->
                    <div class="task task-bg <?php if ($todos->isTodoHasPriority($allActiveUserTodos[$i]["id"])) { echo 'high'; } ?> <?php echo $utilities->setColorClass($allActiveUserTodos[$i]["expire"]); ?>">
                        <div class="task-title <?php if($todos->isTodoExpired($allActiveUserTodos[$i]["id"])){ echo'striked';} ?>">
                            <p><a class="tip" href="todo.php?aid=<?php echo $allActiveUserTodos[$i]["id"]; ?>" role="link" title="<?php echo strip_tags($allActiveUserTodos[$i]["description"]); ?>"><?php echo $allActiveUserTodos[$i]["title"]; ?></a></p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip" href="#" role="link" title="Due: <?php echo $utilities->formatRemainingDate($allActiveUserTodos[$i]["expire"], SHORT_DATE_FORMAT); ?>"><?php echo $utilities->formatDate($allActiveUserTodos[$i]["expire"], SHORT_DATE_FORMAT); ?></a></p>
                        </div>
                    </div>
                    <!-- /task -->

                    </li>

                    <?php 
                        }
                    ?>

                    </ul>

                    <?php 
                    if(empty($allActiveUserTodos)) {  
                    ?>
                    <div id="add-first-task" style="margin-bottom: 20px;">
                        <a class="blue-button default-button shadow tip" role="button" href="#" title="There are no assignments for <?php echo $user[0]["firstname"]; ?> at this moment" onClick="return false;">There are no loose tasks for <?php echo $user[0]["firstname"]; ?></a>
                    </div>
                    <?php 
                    } 
                    ?>

                    <!-- task header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p>Resolved Loose Tasks</p>
                        </div>
                    </div>
                    <!-- /task header -->

                    <ul class="assignment-listing">

                    <?php
                    for ($i=0; $i < count($allResolvedUserTodos); $i++) {
                    ?>

                    <li data-id="<?php echo $allResolvedUserTodos[$i]["id"]; ?>" data-expire="<?php echo $allResolvedUserTodos[$i]["expire"]; ?>" data-created="<?php echo $allResolvedUserTodos[$i]["created"]; ?>" data-assigned="<?php echo $allResolvedUserTodos[$i]["assigned"];?>" data-title="<?php echo $allResolvedUserTodos[$i]["title"]; ?>">
                    <!-- task -->
                    <div class="task task-bg <?php if ($todos->isTodoHasPriority($allResolvedUserTodos[$i]["id"])) { echo 'high'; } ?> <?php echo $utilities->setColorClass($allResolvedUserTodos[$i]["expire"]); ?>">
                        <div class="task-title <?php if($todos->isTodoExpired($allResolvedUserTodos[$i]["id"])){ echo'striked';} ?>">
                            <p><a class="tip" href="todo.php?aid=<?php echo $allResolvedUserTodos[$i]["id"]; ?>" role="link" title="<?php echo strip_tags($allResolvedUserTodos[$i]["description"]); ?>"><?php echo $allResolvedUserTodos[$i]["title"]; ?></a></p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip" href="#" role="link" title="Due: <?php echo $utilities->formatRemainingDate($allResolvedUserTodos[$i]["expire"], SHORT_DATE_FORMAT); ?>"><?php echo $utilities->formatDate($allResolvedUserTodos[$i]["expire"], SHORT_DATE_FORMAT); ?></a></p>
                        </div>
                    </div>
                    <!-- /task -->

                    </li>

                    <?php 
                        }
                    ?>

                    </ul>


                    <!-- task header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p>Closed Loose Tasks</p>
                        </div>
                    </div>
                    <!-- /task header -->

                    <ul class="assignment-listing">

                    <?php
                    for ($i=0; $i < count($allClosedUserTodos); $i++) {
                    ?>

                    <li data-id="<?php echo $allClosedUserTodos[$i]["id"]; ?>" data-expire="<?php echo $allClosedUserTodos[$i]["expire"]; ?>" data-created="<?php echo $allClosedUserTodos[$i]["created"]; ?>" data-assigned="<?php echo $allClosedUserTodos[$i]["assigned"];?>" data-title="<?php echo $allClosedUserTodos[$i]["title"]; ?>">
                    <!-- task -->
                    <div class="task task-bg <?php if ($todos->isTodoHasPriority($allClosedUserTodos[$i]["id"])) { echo 'high'; } ?> <?php echo $utilities->setColorClass($allClosedUserTodos[$i]["expire"]); ?>">
                        <div class="task-title <?php if($todos->isTodoExpired($allClosedUserTodos[$i]["id"])){ echo'striked';} ?>">
                            <p><a class="tip" href="todo.php?aid=<?php echo $allClosedUserTodos[$i]["id"]; ?>" role="link" title="<?php echo strip_tags($allClosedUserTodos[$i]["description"]); ?>"><?php echo $allClosedUserTodos[$i]["title"]; ?></a></p>
                        </div>
                        <div class="task-date">
                            <p><a class="tip" href="#" role="link" title="Due: <?php echo $utilities->formatRemainingDate($allClosedUserTodos[$i]["expire"], SHORT_DATE_FORMAT); ?>"><?php echo $utilities->formatDate($allClosedUserTodos[$i]["expire"], SHORT_DATE_FORMAT); ?></a></p>
                        </div>
                    </div>
                    <!-- /task -->

                    </li>

                    <?php 
                        }
                    ?>

                    </ul>

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