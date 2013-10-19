<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if (!empty($_GET["pid"]) && !empty($_GET["tid"])) {

            $pid = $utilities->filter($_GET["pid"]);
            $tid = $utilities->filter($_GET["tid"]);

            if ($tasks->isAccountTask($tid, $session->get("account"))) {

                $firstAssigned = $tasks->getAssignedTaskUser($pid, $tid);
                $firstStatus = $tasks->getTaskStatus($pid, $tid);
                $firstDate = $tasks->getTaskDateExpired($pid, $tid);

                $taskRedirection = false;

                if ($users->isOwner($session->get("userid"))) {

                    if ($utilities->isPost()) {

                        if (!empty($_POST["task-name"]) && !empty($_POST["task-descriptions"]) && !empty($_POST["radioA"]) && !empty($_POST["radioB"]) && !empty($_POST["selected-user"])) {

                            $title = $utilities->filter($_POST["task-name"]);
                            $description = $utilities->filter($_POST["task-descriptions"]);
                            $status = $utilities->filter($_POST["radioB"]);
                            $reassigned = $utilities->filter($_POST["selected-user"]);                      
                            $expire = $utilities->filter($_POST["radioA"]);
                            $expire = $utilities->setExpirationTime($expire);
                            $priority = $utilities->filter($_POST["priority"]);
                            $private = $utilities->filter($_POST["private"]);

                            $author = $session->get("userid");
                            $projectTitle = $projects->getProjectTitle($pid);
                            $created = $utilities->getDate();

                            $allFiles = $utilities->reArrayFiles($_FILES['file']);

                            $taskChanged = false;
                            
                            $task = $tasks->editTask($tid, $title, $description, $reassigned, $expire, $priority, $private, $status);

                            if ($task) {

                                // check this

                                if (!empty($allFiles[0]['tmp_name'])) {

                                    foreach ($allFiles as $file) {

                                        $fileIdentifier = $utilities->createFileName($utilities->getDate()."_".$file['name']);
                                        $filePath = UPLOAD."project/".$pid."/".$tid."/".$fileIdentifier;

                                        $uploadStatus = $uploads->uploadTaskFile($pid, $tid, $fileIdentifier, $file['tmp_name']);

                                        if ($uploadStatus) {

                                            $uploadUpdate = $uploads->createUpload($session->get("account"), $file['name'], $fileIdentifier, $file['size'], $file['type'], $filePath, $pid, $tid, 0, 0, $author, $created, 1);

                                            $notice .= "File <b>".$file['name']."</b> was successfully uploaded <br>";
                                        
                                        } else {

                                            if(!empty($file['tmp_name'])) {

                                                $notice .= "Error while uploading file: <b>".$file['name']."</b><br>";

                                            }

                                        }

                                    }

                                }

                                if ($firstAssigned != $reassigned) {
                                    // reassign task
                                    $notifications->newTaskNotify(array($users->getUserEmail($reassigned)), $pid, $tid, $projectTitle, $title, $description, $utilities->formatRemainingDate($expire, SHORT_DATE_FORMAT), $users->getUserEmail($author));
                                    $taskChanged = true;
                                }

                                if ($firstStatus != $status) { 
                                    $taskChanged = true;
                                }

                                if ($firstDate != $expire) { 
                                    $taskChanged = true;
                                }

                                // task changes observer routine


                                $notice .= "Task successfully updated.";

                                $taskRedirection = true;

                                $utilities->redirect("task.php?pid=".$pid."&tid=".$tid, 3);

                            } else {
                                $notice = "Error while updating task";
                            }

                        } else {
                            $notice = "Enter all required information";

                        }
                    
                    }

                    $project = $projects->getProject($pid);
                    $task = $tasks->getTask($pid, $tid);
                    $allUsers = $users->listAllProjectUsers($pid);

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
<title><?php echo APPLICATION_TITLE ?> - Edit <?php echo $task[0]["title"]; ?></title>

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
            style: 'dropdown',
            width: 282,
            maxHeight: 180
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

        $("#priority").buttonset();

        $("#private").buttonset();

        $("#radioA").buttonset();

        $("#radioB").buttonset();

        $("#task-date").slideUp();

        $("#datepicker").datepicker({
            altField: "#task-date", 
            altFormat: "d. MM yy",
            defaultDate: "<?php if($task[0]['expire'] == 1924902000){ echo date('m').'/'.(date('d')).'/'.date('Y'); } else { echo date('m/d/Y', $task[0]['expire']);} ?>",
            minDate: "-0",
            minDate: '<?php echo date("m")."/".(date("d"))."/".date("Y");?>',
            onSelect: function(dateText) {
                $("#datepicker").datepicker().slideUp();
                $("#radio2").val(dateText);
            }
        }).hide();

        $("#radioA :radio").click(function(){
            if($("#radioA :radio:checked").attr("id") == "radioA2") {
                $("#datepicker").datepicker().slideDown();
                $("#task-date").fadeIn();
            } else {
                $("#datepicker").datepicker().slideUp();
                $("#task-date").slideUp();
            } 
        });

        $("#task-date").focus(function(){
            $("#datepicker").datepicker().slideDown();
        });

        $("#task-descriptions").autosize();
        $("#task-descriptions").trigger("autosize.resize");

        $("#edit-task").click(function(event) {
            event.preventDefault();
            var taskName = $("#task-name").val();
            var taskDescriptions = $("#task-descriptions").val();
            var dueDate = $("#radioA :radio:checked").val();
            var status = $("#radioB :radio:checked").val();
            var assigned = $("#selected-user").val();

            if(taskName == "" || taskDescriptions == "" || dueDate == undefined || status == undefined || assigned == "") {
                $.achtung({message: 'Please enter all required information', timeout: 7});
            } else {
                if(window.file_selected){
                    $("#upload-message").dialog({
                        modal: true,
                    });
                }
                document['edit-task-form'].submit();
            }

        });
        
        <?php
        $utilities->notify($notice, 7);
        if($taskRedirection) {
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
                <a class="tip" href="project.php?pid=<?php echo $project[0]["id"]; ?>" title="Project: <?php echo $project[0]["title"]; ?>"><img src="images/project.png"></a>
                <a class="tip" href="task.php?pid=<?php echo $project[0]["id"]; ?>&tid=<?php echo $task[0]["id"]; ?>" title="Task: <?php echo $task[0]["title"]; ?>"><img src="images/task.png"></a>                
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

                    <form name="edit-task-form" action="edit-task.php?pid=<?php echo $pid; ?>&tid=<?php echo $tid; ?>" method="post" enctype="multipart/form-data">
                        
                        <fieldset>
                            <label class="large-label">Edit task: <?php echo $task[0]["title"]; ?></label>
                            <input name="task-name" type="text" id="task-name" class="text-input rounded" value="<?php echo $task[0]["title"]; ?>" placeholder="Task name" required>
                            <script> 
                                var taskName = new LiveValidation("task-name", {onlyOnSubmit: false, validMessage: "OK" });
                                taskName.add(Validate.Presence);
                                taskName.add(Validate.Length, {minimum: 3});
                            </script>
                            <textarea name="task-descriptions" id="task-descriptions" class="text-area rounded animated" placeholder="Describe the task..."><?php echo $task[0]["description"]; ?></textarea>
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
                            <div id="radioA" style="position: relative;">
                                    <label for="radioA1">TODAY</label>
                                    <input type="radio" id="radioA1" value="<?php echo date("m")."/".date("d")."/".date("Y");?>" name="radioA" <?php if($utilities->isToday($task[0]["expire"])){ echo "checked"; } ?>>
                                    <label for="radioA2"><img src="images/calendar-ico.png" alt="Select date"></label>
                                    <input type="radio" id="radioA2" value="<?php echo date("m/d/Y", $task[0]["expire"]);?>" name="radioA" <?php if(!$utilities->isToday($task[0]["expire"]) && $task[0]["expire"] != 1924902000){ echo "checked"; } ?>>
                                    <label for="radioA3">WHENEVER</label>
                                    <input type="radio" id="radioA3" value="-1" name="radioA" <?php if($task[0]["expire"] == 1924902000){ echo "checked"; } ?>>

                                    <div id="datepicker"></div> 
                                </div>
                            <input name="task-date" type="text" value="<?php if($task[0]["expire"] == 1924902000){ echo date("m")."/".(date("d"))."/".date("Y"); } ?>" id="task-date" class="text-input rounded" style='float: right; display: <?php if(!$utilities->isToday($task[0]["expire"]) && $task[0]["expire"] < 1924902000){ echo "block"; } else { echo "none";} ?>;'>
                        </fieldset>

                        <div class="spacer"></div>

                        <!-- task status -->
                        <fieldset>
                            <label class="small-label" style="width: 104px;">Status</label>
                            <div id="radioB">
                                <label for="radioB1">OPEN</label>
                                <input type="radio" id="radioB1" value="1" name="radioB" <?php if ($task[0]["status"]==1) { echo "checked";} ?>>
                                <label for="radioB2">RESOLVED</label>
                                <input type="radio" id="radioB2" value="2" name="radioB" <?php if ($task[0]["status"]==2) { echo "checked";} ?>>
                                <label for="radioB3">COMPLETE</label>
                                <input type="radio" id="radioB3" value="3" name="radioB" <?php if ($task[0]["status"]==3) { echo "checked";} ?>>
                            </div>
                            <input name="user-role" type="hidden" id="user-role" class="text-input rounded" value="">         
                        </fieldset>
                        <!-- /task status -->

                        <div class="spacer"></div>

                        <!-- task reassignment -->
                        <fieldset>
                            <label class="small-label" for="selected-user">Re-assign to</label>
                            <select name="selected-user" id="selected-user">
                                <optgroup label="Project Allocated Users">
                                <?php
                                for ($i=0; $i < count($allUsers); $i++) {
                                ?>
                                <option value="<?php echo $allUsers[$i]["user"]; ?>" <?php if ($task[0]["assigned"] == $allUsers[$i]["user"]) { echo "selected";} ?>><?php echo $users->getFullUserName($allUsers[$i]["user"]) ?></option>
                                <?php } ?>
                                </optgroup>
                            </select>
                        </fieldset>
                        <!-- /task reassignment -->

                        <div class="spacer"></div>

                        <fieldset>
                            <label class="small-label" style="width: 250px;" for="priority">Mark as High Priority</label>
                            <div id="priority">
                                <label for="priority1">YES</label>
                                <input type="radio" id="priority1" value="1" name="priority" <?php if ($tasks->isTaskHasPriority($task[0]["project"], $task[0]["id"])) { echo 'checked'; } ?>>
                                <label for="priority2">NO</label>
                                <input type="radio" id="priority2" value="0" name="priority" <?php if (!$tasks->isTaskHasPriority($task[0]["project"], $task[0]["id"])) { echo 'checked'; } ?>>
                            </div>
                        </fieldset>

                        <div class="spacer"></div>

                        <div style="<?php if($roles->isProjectClient($task[0]["project"], $session->get("userid"))) { echo 'display: none'; } ?>">

                            <fieldset>
                                <label class="small-label" for="priority">Mark as Private <a class="tip" href="#" title="Private task is visible only to Project Managers and Workers. Client can't see this task."><img src="images/help.png"></a></label>
                                <div id="private" style="float: right;">
                                    <label for="private1">YES</label>
                                    <input type="radio" id="private1" value="1" name="private" <?php if ($tasks->isTaskPrivate($task[0]["project"], $task[0]["id"])) { echo 'checked'; } ?>>
                                    <label for="private2">NO</label>
                                    <input type="radio" id="private2" value="0" name="private" <?php if (!$tasks->isTaskPrivate($task[0]["project"], $task[0]["id"])) { echo 'checked'; } ?>>
                                </div>
                            </fieldset>

                            <div class="spacer"></div>

                        </div>

                        <fieldset>
                            <a class="orange-button default-button tip" id="edit-task" role="button" href="#" title="Update Task">UPDATE</a>
                            
                            <a class="link-button tip" id="cancel-task" role="link" href="task.php?pid=<?php echo $pid; ?>&tid=<?php echo $tid; ?>" title="Cancel and return">Cancel</a>
                        </fieldset>
    

                    </form>

                </article>
                <!-- /page content -->
            </div>             
            <!-- /main content -->
        </section>
        <!-- /main wrapper -->

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