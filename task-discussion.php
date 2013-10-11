<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid")) && defined("DISQUS") && DISQUS) {


        if (!empty($_GET["pid"]) && !empty($_GET["tid"])) {

            $pid = $utilities->filter($_GET["pid"]);
            $tid = $utilities->filter($_GET["tid"]);

            // get task info if user have permission (not implemented)
            $project = $projects->getProject($pid);
            $task = $tasks->getTask($pid, $tid);
            $allUsers = $users->listAllUsers($session->get("account"));

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
        $utilities->redirect("error.php?code=12");
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?> - <?php echo $project[0]["title"]; ?> - <?php echo $task[0]["title"]; ?> - Discussion</title>

<link rel="alternate" type="application/rss+xml" title="<?php echo $project[0]["title"]; ?> - <?php echo $task[0]["title"]; ?> updates" href="rss.php?pid=<?php echo $pid; ?>&tid=<?php echo $tid; ?>&key=<?php echo ACCESS_KEY; ?>"/>
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
<script src="js/jquery.autosize.js"></script>
<script src="js/livevalidation.js"></script>
<script src="js/script.js"></script>

<!-- remove for production -->
<script src="js/cssrefresh.js"></script>


<!-- javascript -->

<script>
    $(document).ready(function() {

        <?php
        $utilities->notify($notice, 7);
        ?>

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
                <a class="tip" href="task.php?pid=<?php echo $project[0]["id"]; ?>&tid=<?php echo $task[0]["id"]; ?>" title="Task: <?php echo $task[0]["title"]; ?>"><img src="images/task.png"></a>
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
                <h1>TASK DISCUSSION: <span class="<?php if($tasks->isTaskExpired($pid, $task[0]["id"])){ echo 'striked';} ?>"><?php echo $task[0]["title"]; ?></span></h1>
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
                <!-- project text -->
                <article id="project-description">
                    <div id="project-meta" class="rounded transitions">
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
                        <div id="project-timing">
                            <p>Due: <strong><a class="tip underlined" title="<?php echo $utilities->formatDateTime($task[0]["expire"], LONG_DATE_FORMAT, TIME_FORMAT); ?>" href="#"><?php echo $utilities->formatRemainingDate($task[0]["expire"], SHORT_DATE_FORMAT); ?></a></strong></p>
                            <p>Status: <strong><?php echo $tasks->getTaskStatus($tid)?></strong></p>
                            <p>Created: <strong><a class="tip underlined" title="<?php echo $utilities->formatDateTime($task[0]["created"], LONG_DATE_FORMAT, TIME_FORMAT); ?>" href="#"><?php echo $utilities->elapsedTime($task[0]["created"])?></a></strong></p>
                        </div>
                    </div>

                    <!-- task description -->
                    <div class="default-text">
                        <p><?php echo $utilities->createLinks($utilities->parseSmileys(nl2br($task[0]["description"]))); ?></p>
                    </div>
                    <!-- /task description -->
                    
                </article>
                <!-- /project text -->

                <!-- project tasks -->
                <article id="project-updates">
                               
                    <!-- complete  -->
                    <?php 
                    if (!empty($task[0]["finished"]) && $tasks->getTaskStatus($tid) != "OPEN") {
                    ?>
                    <div class="complete" style="margin-top: 20px;">
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
                <!-- project tasks -->

            </div>             
            <!-- /main content -->
        </section>
        <!-- /main wrapper -->

        <!---->
        <section id="disqus-wrapper">

            <div id="disqus_thread"></div>

            <script type="text/javascript">
                var disqus_shortname = '<?php echo DISQUS_ID; ?>';
                (function() {
                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                    dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                })();
            </script>

        </section>
        
        
        
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