<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if (!empty($_GET["pid"])) {

            $pid = $utilities->filter($_GET["pid"]);

            if ($projects->isAccountProject($pid, $session->get("account"))) {
            
                $projectRedirection = false;

                if ($users->isOwner($session->get("userid"))) {

                    $allUsers = $users->listAllUsers($session->get("account"), "firstname");

                    if ($utilities->isPost()) {

                        if (!empty($_POST["project-name"]) && !empty($_POST["project-descriptions"]) && !empty($_POST["radio"])) {

                            $title = $utilities->filter($_POST["project-name"]);
                            $description = $utilities->filter($_POST["project-descriptions"]);
                            $author = $session->get("userid");
                            $created = $utilities->getDate();
                            $expire = $utilities->filter($_POST["radio"]);
                            $expire = $utilities->setExpirationTime($expire);
                            
                            $project = $projects->editProject($pid, $title, $description, $expire);

                            $receivers = array();

                            if ($project) {

                                for ($i=0; $i < count($allUsers); $i++) {

                                    if (!empty($_POST["radio-".$allUsers[$i]["id"]])) {

                                        if ($roles->isRoleSet($pid, $allUsers[$i]["id"])) {

                                            $role = $roles->updateRole($pid, $allUsers[$i]["id"], $_POST["radio-".$allUsers[$i]["id"]]);

                                        } else {

                                            $role = $roles->createRole($pid, $allUsers[$i]["id"], $_POST["radio-".$allUsers[$i]["id"]]);

                                            array_push($receivers, $allUsers[$i]["email"]);

                                        }

                                        //$role = $roles->createRole($project, $allUsers[$i]["id"], $_POST["radio-".$allUsers[$i]["id"]]);

                                    } else {

                                        $role = $roles->deleteRoles($pid, $allUsers[$i]["id"]);
                                    }

                                }

                                $notifications->newProjectNotify($receivers, $pid, $title, $users->getUserEmail($author));

                                $notice = "Project successfully updated.";

                                $projectRedirection = true;

                                $utilities->redirect("project.php?pid=".$pid, 3);

                            } else {
                                $notice = "Error while updating project";
                            }

                        } else {
                            $notice = "Enter all required information";

                        }
                    
                    }

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

                    $project = $projects->getProject($pid);

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
<script src="js/jquery.autosize.js"></script>
<script src="js/livevalidation.js"></script>
<script src="js/script.js"></script>

<!-- remove for production -->
<script src="js/cssrefresh.js"></script>


<!-- javascript -->

<script>
    $(document).ready(function() {

        $("#radio").buttonset();

        $("#project-date").slideUp();

        $("#datepicker").datepicker({
            altField: "#project-date", 
            altFormat: "d. MM yy",
            defaultDate: "<?php if($project[0]['expire'] == 1924902000){ echo date('m').'/'.(date('d')).'/'.date('Y'); } else { echo date('m/d/Y', $project[0]['expire']);} ?>",
            minDate: "-0",
            minDate: '<?php echo date("m")."/".(date("d"))."/".date("Y");?>',
            onSelect: function(dateText) {
                $("#datepicker").datepicker().slideUp();
                $("#radio2").val(dateText);
            }
        }).hide();

        $("#radio :radio").click(function(){
            if($("#radio :radio:checked").attr("id") == "radio2") {
                $("#datepicker").datepicker().slideDown();
                $("#project-date").fadeIn();
            } else {
                $("#datepicker").datepicker().slideUp();
                $("#project-date").slideUp();
            } 
        });

        $("#project-date").focus(function(){
            $("#datepicker").datepicker().slideDown();
        });

        $("#project-descriptions").autosize();
        $("#project-descriptions").trigger("autosize.resize");

        $("#close-project, #close-project2").click(function(event) {

            event.preventDefault();

            $("#dialog-message").dialog({
                modal: true,
                buttons: {
                    Yes: function() {
                        $(this).dialog("close");
                        window.location.href="edit-project.php?pid=<?php echo $project[0]["id"]; ?>&action=close";
                    },
                    No: function() {
                        $(this).dialog("close");
                    }
                }
            });

        });
        
        <?php
        $utilities->notify($notice, 7);

        if($projectRedirection) {
            $utilities->notify("Redirecting...", 7);
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
                <a class="tip" href="home.php" title="Home: Select project"><img src="images/home.png"></a>
                <a class="separator"><img src="images/separator.png"></a>
                <a class="tip current" href="project.php?pid=<?php echo $project[0]["id"]; ?>" title="Project: <?php echo $project[0]["title"]; ?>"><img src="images/task.png"></a>
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
                <article id="add-task">

                    <form name="update-project-form" action="edit-project.php?pid=<?php echo $pid; ?>" method="post">
                        
                        <fieldset>
                            <label class="large-label">Edit project: <?php echo $project[0]["title"]; ?></label>
                            <input name="project-name" type="text" id="project-name" class="text-input rounded" value="<?php echo $project[0]["title"]; ?>" placeholder="Project name" required>
                            <script> 
                                var projectName = new LiveValidation("project-name", {onlyOnSubmit: false, validMessage: "OK" });
                                projectName.add(Validate.Presence);
                                projectName.add(Validate.Length, {minimum: 3});
                            </script>
                            <textarea name="project-descriptions" id="project-descriptions" class="text-area rounded animated" placeholder="Describe the project..."><?php echo $project[0]["description"]; ?></textarea>
                            <script> 
                                var projectDescriptions = new LiveValidation("project-descriptions", {onlyOnSubmit: false, validMessage: "OK" });
                                projectDescriptions.add(Validate.Presence);
                                projectDescriptions.add(Validate.Length, {minimum: 10});
                            </script>
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <label class="small-label">Project due by</label>
                            <div id="radio">
                                    <label for="radio1">TODAY</label>
                                    <input type="radio" id="radio1" value="<?php echo date("m")."/".date("d")."/".date("Y");?>" name="radio" <?php if($utilities->isToday($project[0]["expire"])){ echo "checked"; } ?>>
                                    <label for="radio2"><img src="images/calendar-ico.png" alt="Select date"></label>
                                    <input type="radio" id="radio2" value="<?php echo date("m/d/Y", $project[0]["expire"]);?>" name="radio" <?php if(!$utilities->isToday($project[0]["expire"]) && $project[0]["expire"] != 1924902000){ echo "checked"; } ?>>
                                    <label for="radio3">WHENEVER</label>
                                    <input type="radio" id="radio3" value="-1" name="radio" <?php if($project[0]["expire"] == 1924902000){ echo "checked"; } ?>>

                                    <div id="datepicker"></div> 
                                </div>
                            <input name="project-date" type="text" value="<?php if($project[0]["expire"] == 1924902000){ echo date("m")."/".(date("d"))."/".date("Y"); } ?>" id="project-date" class="text-input rounded" style='float: right; display: <?php if(!$utilities->isToday($project[0]["expire"]) && $project[0]["expire"] < 1924902000){ echo "block"; } else { echo "none";} ?>;'>
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>

                            <div class="check-table">
                                <!-- user role header -->
                                <div class="check-table-header">
                                    <div class="check-table-col1">
                                        <p>Project Team</p>
                                    </div>
                                    <div class="check-table-col2">
                                        <p>User</p>
                                    </div>
                                    <div class="check-table-col3">
                                        <p>Manager</p>
                                    </div>
                                </div>
                                <!-- /user role header -->

                                <?php
                                for ($i=0; $i < count($allUsers); $i++) {
                                ?>

                                <div class="check-table-row">
                                    <div class="check-table-col1">
                                        <p><?php echo $allUsers[$i]["firstname"]." ".$allUsers[$i]["lastname"]; ?></p>
                                    </div>
                                    <div class="check-table-col2">
                                        <p><input type="radio" id="radio-user-<?php echo $i; ?>" value="2" name="radio-<?php echo $allUsers[$i]["id"]; ?>" <?php if($roles->isProjectUser($pid, $allUsers[$i]["id"])){ echo 'checked';} ?>></p>
                                    </div>
                                    <div class="check-table-col3">
                                        <p><input type="radio" id="radio-manager-<?php echo $i; ?>" value="1" name="radio-<?php echo $allUsers[$i]["id"]; ?>" <?php if($roles->isProjectManager($pid, $allUsers[$i]["id"])){ echo 'checked';} ?>></p>
                                    </div>
                                    <div class="check-table-col4">
                                        <a class="remove-update tip" title="Remove this role" href="#" onClick="$('#radio-user-<?php echo $i; ?>, #radio-manager-<?php echo $i; ?>').prop('checked', false); return false;"><img src="images/delete.png"></a>
                                    </div>
                                </div>

                                <?php } ?>

                                
                            </div>
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <a class="orange-button default-button tip" id="update-project" role="button" href="#" onClick="document['update-project-form'].submit(); return false;" title="Update Project">UPDATE</a>
                            <?php
                            if ($projects->isProjectOpen($pid)) {               
                            ?>
                            <a class="orange-button default-button tip" id="close-project2" role="button" href="#" title="Close Project">CLOSE</a>
                            <?php 
                            } 
                            ?>
                            <?php
                            if ($projects->isProjectClosed($pid)) {               
                            ?>
                            <a class="orange-button default-button tip" id="open-project" role="button" href="edit-project.php?pid=<?php echo $project[0]["id"]; ?>&action=open" title="Open Project">OPEN</a>
                            <?php 
                            } 
                            ?>
                            <a class="link-button tip" id="cancel-task" role="link" href="project.php?pid=<?php echo $pid; ?>" title="Cancel and return">Cancel</a>
                        </fieldset>
    

                    </form>

                </article>
                <!-- /page content -->
            </div>             
            <!-- /main content -->
        </section>
        <!-- /main wrapper -->
        
        <div id="dialog-message" title="Closing project" style="display: none;">
            <p>Are you sure you want to close this project? This action will also close all tasks within the project.</p>
        </div>
        
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