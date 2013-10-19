<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if ($users->isOwner($session->get("userid"))) {

            $allUsers = $users->listAllUsers($session->get("account"), "firstname");

            $projectRedirection = false;

            if ($utilities->isPost()) {

                if (!empty($_POST["project-name"]) && !empty($_POST["project-descriptions"]) && !empty($_POST["radio"])) {

                    $title = $utilities->filter($_POST["project-name"]);
                    $description = $utilities->filter($_POST["project-descriptions"]);
                    $author = $session->get("userid");
                    $created = $utilities->getDate();
                    $expire = $utilities->filter($_POST["radio"]);
                    $expire = $utilities->setExpirationTime($expire);
                    
                    $project = $projects->createProject($session->get("account"), $title, $description, $author, $created, $expire, 1);
                    
                    $receivers = array();

                    if ($project) {

                        if (is_numeric($project)) {

                            for ($i=0; $i < count($allUsers); $i++) {

                                if (!empty($_POST["radio-".$allUsers[$i]["id"]])) {

                                    $role = $roles->createRole($project, $allUsers[$i]["id"], $_POST["radio-".$allUsers[$i]["id"]]);

                                    array_push($receivers, $allUsers[$i]["email"]);

                                }

                            }

                            $notifications->newProjectNotify($receivers, $project, $title, $users->getUserEmail($author));

                            $notice = "Project successfully created.";

                            $projectRedirection = true;

                            $utilities->redirect("project.php?pid=".$project, 3);

                        } else {
                            $notice = "Something is wrong with project index number";
                        }

                    } else {
                        $notice = "Error while creating project";
                    }

                } else {
                    $notice = "Enter all required information";

                }
                
            }

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
<title><?php echo APPLICATION_TITLE ?> - Add New Project</title>

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
            minDate: '<?php echo date("m")."/".(date("d"))."/".date("Y");?>',
            onSelect: function(dateText) {
                $("#datepicker").datepicker().slideUp();
                $("#radio2").val(dateText);
            }
        }).hide();

        $("#radio :radio").click(function() {
            if($("#radio :radio:checked").attr("id") == "radio2") {
                $("#datepicker").datepicker().slideDown();
                $("#project-date").fadeIn();
            } else {
                $("#datepicker").datepicker().slideUp();
                $("#project-date").slideUp();
            } 
        });

        $("#project-date").focus(function() {
            $("#datepicker").datepicker().slideDown();
        });

        $("#project-descriptions").autosize();

        $("#create-project").click(function(event) {
            event.preventDefault();
            var projectName = $("#project-name").val();
            var projectDescriptions = $("#project-descriptions").val();
            var dueDate = $("#radio :radio:checked").val();

            if(projectName == "" || projectDescriptions == "" || dueDate == undefined) {
                $.achtung({message: 'Please enter all required information', timeout: 7});
            } else {
                document['add-project-form'].submit();
            }
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
                <?php 
                if ($users->isOwner($session->get("userid"))) {
                ?>
                <a class="tip" href="all-projects.php" title="All Projects"><img src="images/all-projects.png"></a>
                <?php } ?>
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

                    <form name="add-project-form" action="add-project.php" method="post">
                        
                        <fieldset>
                            <label class="large-label">Create a new project</label>
                            <input name="project-name" type="text" id="project-name" class="text-input rounded" placeholder="Project name" required>
                            <script> 
                                var projectName = new LiveValidation("project-name", {onlyOnSubmit: false, validMessage: "OK" });
                                projectName.add(Validate.Presence);
                                projectName.add(Validate.Length, {minimum: 3});
                            </script>
                            <textarea name="project-descriptions" id="project-descriptions" class="text-area rounded animated" placeholder="Describe the project..."></textarea>
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
                                <input type="radio" id="radio1" value="<?php echo date("m")."/".date("d")."/".date("Y");?>" name="radio">
                                <label for="radio2"><img src="images/calendar-ico.png" alt="Select date"></label>
                                <input type="radio" id="radio2" value="" name="radio">
                                <label for="radio3">WHENEVER</label>
                                <input type="radio" id="radio3" value="-1" name="radio">

                                <div id="datepicker"></div> 
                            </div>
                            <input name="project-date" type="text" value="<?php echo date("m")."/".(date("d"))."/".date("Y");?>" id="project-date" class="text-input rounded" style="float: right;">
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
                                        <p>Client</p>
                                    </div>
                                    <div class="check-table-col2">
                                        <p>Worker</p>
                                    </div>
                                    <div class="check-table-col2">
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
                                        <p><input type="radio" id="radio-user-<?php echo $i; ?>" value="3" name="radio-<?php echo $allUsers[$i]["id"]; ?>"></p>
                                    </div>
                                    <div class="check-table-col2">
                                        <p><input type="radio" id="radio-user-<?php echo $i; ?>" value="2" name="radio-<?php echo $allUsers[$i]["id"]; ?>"></p>
                                    </div>
                                    <div class="check-table-col3">
                                        <p><input type="radio" id="radio-manager-<?php echo $i; ?>" value="1" name="radio-<?php echo $allUsers[$i]["id"]; ?>"></p>
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
                            <a class="orange-button default-button tip" id="create-project" role="button" href="#" title="Create new project">CREATE</a>
                            <a class="link-button tip" id="cancel-project" role="link" href="home.php" title="Cancel and return">Cancel</a>
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