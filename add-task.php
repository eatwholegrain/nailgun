<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if ($users->isAdmin($session->get("userid")) || $roles->isProjectManager($utilities->filter($_GET["pid"]), $session->get("userid")) || $roles->isProjectClient($utilities->filter($_GET["pid"]), $session->get("userid"))) {

            if (!empty($_GET["pid"])) {

                $pid = $utilities->filter($_GET["pid"]);

                if ($projects->isAccountProject($pid, $session->get("account"))) {

                    $project = $projects->getProject($pid);

                    if (isset($project)) {

                        //$allUsers = $users->listAllUsers();
                        $allUsers = $users->listAllProjectUsers($pid);

                    } else {
                        // project not exist
                        $utilities->redirect("error.php?code=6");
                    }

                    // add task
                    if ($utilities->isPost()) {

                        if (!empty($_POST["task-name"]) && !empty($_POST["task-descriptions"]) && !empty($_POST["radio"]) && !empty($_POST["selected-user"])) {

                            $title = $utilities->filter($_POST["task-name"]);
                            $description = $utilities->filter($_POST["task-descriptions"]);
                            $author = $session->get("userid");
                            $assigned = $utilities->filter($_POST["selected-user"]);
                            $created = $utilities->getDate();
                            $expire = $utilities->filter($_POST["radio"]);
                            $priority = $utilities->filter($_POST["priority"]);
                            $private = $utilities->filter($_POST["private"]);

                            $expire = $utilities->setExpirationTime($expire);

                            $allFiles = $utilities->reArrayFiles($_FILES['file']);

                            $task = $tasks->createTask($session->get("account"), $title, $description, $pid, $author, $assigned, $created, $expire, $priority, $private, 1);

                            if (is_numeric($task)) {

                                $receiver = $users->getUser($assigned);

                                $notifications->newTaskNotify(array($receiver[0]["email"]), $pid, $task, $project[0]["title"], $title, $description, $utilities->formatRemainingDate($expire, SHORT_DATE_FORMAT), $users->getUserEmail($author));
                                
                                $notice = "Task for <b>".$project[0]["title"]."</b> successfully created and assigned to <b>".$receiver[0]["firstname"]."</b><br><br>";
                                
                                $success = true;

                                // add file
                                if (!empty($allFiles[0]['tmp_name'])) {

                                    foreach ($allFiles as $file) {

                                        $fileIdentifier = $utilities->createFileName($utilities->getDate()."_".$file['name']);
                                        $filePath = UPLOAD."project/".$pid."/".$task."/".$fileIdentifier;

                                        $uploadStatus = $uploads->uploadTaskFile($pid, $task, $fileIdentifier, $file['tmp_name']);

                                        if ($uploadStatus) {

                                            $uid = 0;

                                            $uploadUpdate = $uploads->createUpload($session->get("account"), $file['name'], $fileIdentifier, $file['size'], $file['type'], $filePath, $pid, $task, 0, $uid, $author, $created, 1);

                                            $notice .= "File <b>".$file['name']."</b> was successfully uploaded <br>";
                                        
                                        } else {

                                            if(!empty($file['tmp_name'])) {

                                                $notice .= "Error while uploading file: <b>".$file['name']."</b><br>";

                                            }
                                        }

                                    }

                                }

                            } else {

                                $success = false;
                                $notice = "Error while creating task";
                            }

                        } else {

                            $success = false;
                            $notice = "Enter all required information";
                        }
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
<title><?php echo APPLICATION_TITLE ?> - <?php echo $project[0]["title"]; ?> - Add New Task</title>

<!-- styles -->

<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/fonts.css" />
<link rel="stylesheet" href="css/jquery.ui.custom.css" />
<link rel="stylesheet" href="css/jquery.ui.selectmenu.css" />
<link rel="stylesheet" href="css/jquery.ui.achtung.css" />
<link rel="stylesheet" href="css/jquery.fileinput.css" />
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
<script src="js/jquery.fileinput.js"></script>
<script src="js/jquery.tiptip.js"></script>
<script src="js/jquery.autosize.js"></script>
<script src="js/livevalidation.js"></script>
<script src="js/script.js"></script>

<!-- remove for production -->
<script src="js/cssrefresh.js"></script>


<!-- javascript -->

<script>

    $(document).ready(function() {

        window.file_selected = false;

        $("select#selected-user").selectmenu({
            width: 292
        });

        $("#file-upload").customFileInput({
            width: 282,
            onChange: function () {
                window.file_selected = true;
                $("#more-files").show();
            }
        });

        $("#file-upload-more").customFileInput({
            width: 282,
            onChange: function () {
                window.file_selected = true;
                $("#even-more-files").show();
            }
        });

        $("#file-upload-even-more").customFileInput({
            width: 282,
            onChange: function () {
                window.file_selected = true;
            }
        });

        $("#radio").buttonset();

        $("#priority").buttonset();

        $("#private").buttonset();

        $("#task-date").slideUp();

        $("#datepicker").datepicker({
            altField: "#task-date", 
            altFormat: "d. MM yy",
            minDate: '<?php echo date("m")."/".(date("d"))."/".date("Y");?>',
            onSelect: function(dateText) {
                $("#datepicker").datepicker().slideUp();
                $("#radio2").val(dateText);
            }
        }).hide();

        $("#private :radio").click(function() {
            if($("#private :radio:checked").attr("id") == "private1") {
                $.achtung({message: 'Make sure that task is not assigned to client.', timeout: 7});
            } 
        });

        $("#radio :radio").click(function() {
            if($("#radio :radio:checked").attr("id") == "radio2") {
                $("#datepicker").datepicker().slideDown();
                $("#task-date").fadeIn();
            } else {
                $("#datepicker").datepicker().slideUp();
                $("#task-date").slideUp();
            }
        });

        $("#task-date").focus(function() {
            $("#datepicker").datepicker().slideDown();
        });

        $("#task-descriptions").autosize();

        $("#create-task").click(function(event) {
            event.preventDefault();
            var taskName = $("#task-name").val();
            var taskDescriptions = $("#task-descriptions").val();
            var dueDate = $("#radio :radio:checked").val();
            var assigned = $("#selected-user").val();

            if(taskName == "" || taskDescriptions == "" || dueDate == undefined || assigned == "") {
                $.achtung({message: 'Please enter all required information', timeout: 7});
            } else {
                if(window.file_selected){
                    $("#upload-message").dialog({
                        modal: true,
                    });
                }
                document['add-task-form'].submit();
            }

        }); 

        <?php
        $utilities->notify($notice, 7);
        ?>

        <?php
        if (isset($success)) {
            if ($success) {
            ?>
            $(function() {
                $("#dialog-message").dialog({
                    modal: true,
                    buttons: {
                        Yes: function() {
                            $(this).dialog("close");
                            window.location.href="add-task.php?pid=<?php echo $pid; ?>";
                        },
                        No: function() {
                            window.location.href="project.php?pid=<?php echo $pid; ?>";
                            $(this).dialog("close");
                        }
                    }
                });
            });

        <?php
            }
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
                <a class="tip" href="project.php?pid=<?php echo $project[0]["id"]; ?>" title="Project: <?php echo $project[0]["title"]; ?>"><img src="images/project.png"></a>
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

                    <form name="add-task-form" action="add-task.php?pid=<?php echo $pid; ?>" method="post" enctype="multipart/form-data">
                        
                        <fieldset>
                            <label class="large-label">Create a new task in <?php echo $project[0]["title"]; ?></label>
                            <input name="task-name" type="text" id="task-name" class="text-input rounded" placeholder="Task name" required>
                            <script> 
                                var taskName = new LiveValidation("task-name", {onlyOnSubmit: false, validMessage: "OK" });
                                taskName.add(Validate.Presence);
                                taskName.add(Validate.Length, {minimum: 3});
                            </script>
                            <textarea name="task-descriptions" id="task-descriptions" class="text-area rounded animated" placeholder="Describe the task..."></textarea>
                            <script> 
                                var taskDescriptions = new LiveValidation("task-descriptions", {onlyOnSubmit: false, validMessage: "OK" });
                                taskDescriptions.add(Validate.Presence);
                                taskDescriptions.add(Validate.Length, {minimum: 10});
                            </script>
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset style='display: block'>
                            <label class="small-label" for="file">Add files <a class="tip" href="#" title="Your server configuration allow maximum file size of <?php echo $utilities->getMaximumUpload(); ?> MB.<br>To select multiple files hold <b>Ctrl</b> key."><img src="images/help.png"></a></label>
                            <input type="file" name="file[]" id="file-upload" multiple>
                        </fieldset>

                        <fieldset id="more-files" style="display: none; margin-top: 20px;">
                            <label class="small-label" for="file">Add more files</label>
                            <input type="file" name="file[]" id="file-upload-more" multiple="multiple">
                        </fieldset>

                        <fieldset id="even-more-files" style="display: none; margin-top: 20px;">
                            <label class="small-label" for="file">Add more files</label>
                            <input type="file" name="file[]" id="file-upload-even-more" multiple="multiple">
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <label class="small-label">Task due by</label>
                            <div id="radio">
                                <label for="radio1">TODAY</label>
                                <input type="radio" id="radio1" value="<?php echo date("m")."/".date("d")."/".date("Y");?>" name="radio">
                                <label for="radio2"><img src="images/calendar-ico.png" alt="Select date"></label>
                                <input type="radio" id="radio2" value="" name="radio">
                                <label for="radio3">WHENEVER</label>
                                <input type="radio" id="radio3" value="-1" name="radio">

                                <div id="datepicker"></div> 
                            </div>
                            <input name="task-date" type="text" value="<?php echo date("m")."/".(date("d"))."/".date("Y");?>" id="task-date" class="text-input rounded" style="float: right;">         
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <label class="small-label" for="selected-user">Assign to</label>
                            <select name="selected-user" id="selected-user">
                                <option value="">Select user...</option>
                                <optgroup label="Project Allocated Users">
                                <?php
                                for ($i=0; $i < count($allUsers); $i++) {
                                ?>
                                <option value="<?php echo $allUsers[$i]["user"]; ?>"><?php echo $users->getFullUserName($allUsers[$i]["user"]); ?></option>
                                <?php } ?>
                                </optgroup>
                            </select>
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <label class="small-label" style="width: 250px;" for="priority">Mark as High Priority</label>
                            <div id="priority" style="float: right;">
                                <label for="priority1">YES</label>
                                <input type="radio" id="priority1" value="1" name="priority">
                                <label for="priority2">NO</label>
                                <input type="radio" id="priority2" value="0" name="priority" checked>
                            </div>
                        </fieldset>

                        <div class="spacer"></div>

                        <div style="<?php if($roles->isProjectClient($pid, $session->get("userid"))) { echo 'display: none'; } ?>">

                            <fieldset>
                                <label class="small-label" for="priority">Mark as Private <a class="tip" href="#" title="Private task is visible only to Project Managers and Workers. Client can't see this task."><img src="images/help.png"></a></label>
                                <div id="private" style="float: right;">
                                    <label for="private1">YES</label>
                                    <input type="radio" id="private1" value="1" name="private">
                                    <label for="private2">NO</label>
                                    <input type="radio" id="private2" value="0" name="private" checked>
                                </div>
                            </fieldset>

                            <div class="spacer"></div>

                        </div>

                        <fieldset>
                            <a class="orange-button default-button tip" id="create-task" role="button" href="#" title="Create new task">CREATE</a>
                            <a class="link-button tip" id="cancel-task" role="link" href="project.php?pid=<?php echo $project[0]["id"]; ?>" title="Cancel and return to project">Cancel</a>
                        </fieldset>
    

                    </form>

                </article>
                <!-- /page content -->
            </div>             
            <!-- /main content -->
        </section>
        <!-- /main wrapper -->
        
        <div id="dialog-message" title="New Task" style="display: none;">
            <p>Do you want to create another task?</p>
        </div>

        <div id="upload-message" title="Upload in progress..." style="display: none;">
            <div style="width: 32px; height: 32px; margin: 30px auto 0 auto;"><img src="css/images/loading.gif"></div>
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