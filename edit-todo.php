<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if ($users->isAdmin($session->get("userid"))) {

            if (!empty($_GET["aid"])) {

                $aid = $utilities->filter($_GET["aid"]);

                if ($todos->isAccountTodo($aid, $session->get("account"))) {

                    $firstAssigned = $todos->getAssignedTodoUser($aid);
                    $firstStatus = $todos->getTodoStatus($aid);
                    $firstDate = $todos->getTodoDateExpired($aid);

                    $todoRedirection = false;

                    // add todo
                    if ($utilities->isPost()) {

                        if (!empty($_POST["todo-name"]) && !empty($_POST["todo-descriptions"]) && !empty($_POST["radioA"]) && !empty($_POST["radioB"]) && !empty($_POST["selected-user"])) {

                            $title = $utilities->filter($_POST["todo-name"]);
                            $description = $utilities->filter($_POST["todo-descriptions"]);
                            $status = $utilities->filter($_POST["radioB"]);
                            $reassigned = $utilities->filter($_POST["selected-user"]);                      
                            $expire = $utilities->filter($_POST["radioA"]);
                            $expire = $utilities->setExpirationTime($expire);
                            $priority = $utilities->filter($_POST["priority"]);

                            $author = $session->get("userid");
                            $created = $utilities->getDate();

                            $allFiles = $utilities->reArrayFiles($_FILES['file']);

                            $todoUpdate = $todos->editTodo($aid, $title, $description, $reassigned, $expire, $priority, $status);

                            if ($todoUpdate) {
                                
                                $success = true;

                                if (!empty($allFiles[0]['tmp_name'])) {

                                    foreach ($allFiles as $file) {

                                        $fileIdentifier = $utilities->createFileName($utilities->getDate()."_".$file['name']);
                                        $filePath = UPLOAD."todo/".$aid."/".$fileIdentifier;

                                        $uploadStatus = $uploads->uploadTodoFile($aid, $fileIdentifier, $file['tmp_name']);

                                        if ($uploadStatus) {

                                            $uploadUpdate = $uploads->createUpload($session->get("account"), $file['name'], $fileIdentifier, $file['size'], $file['type'], $filePath, 0, 0, $aid, 0, $author, $created, 1);

                                            $notice .= "File <b>".$file['name']."</b> was successfully uploaded <br>";
                                        
                                        } else {
                                            
                                            if(!empty($file['tmp_name'])) {

                                                $notice .= "Error while uploading file: <b>".$file['name']."</b><br>";

                                            }
                                        }

                                    }

                                }

                                if ($firstAssigned != $reassigned) {
                                    // reassign todo
                                    $notifications->newTodoNotify(array($users->getUserEmail($reassigned)), $aid, $title, $description, $utilities->formatRemainingDate($expire, SHORT_DATE_FORMAT), $users->getUserEmail($session->get("userid")));
                                    $todoChanged = true;
                                }

                                if ($firstStatus != $status) { 
                                    $todoChanged = true;
                                }

                                if ($firstDate != $expire) { 
                                    $todoChanged = true;
                                }

                                $notice .= "Loose task successfully updated.";


                                $todoRedirection = true;

                                $utilities->redirect("todo.php?aid=".$aid, 3);

                            } else {

                                $success = false;
                                $notice = "Error while updating loose task";
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
                // todo not specified
                $utilities->redirect("error.php?code=10");
            }

            $allUsers = $users->listAllUsers($session->get("account"));
            $todo = $todos->getTodo($aid);

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
<title><?php echo APPLICATION_TITLE ?> - Edit <?php echo $todo[0]["title"]; ?> Loose Task</title>

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

        $("#radioA").buttonset();

        $("#radioB").buttonset();

        $("#priority").buttonset();

        $("#todo-date").slideUp();

        $("#datepicker").datepicker({
            altField: "#todo-date", 
            altFormat: "d. MM yy",
            defaultDate: "<?php if($todo[0]['expire'] == 1924902000){ echo date('m').'/'.(date('d')).'/'.date('Y'); } else { echo date('m/d/Y', $todo[0]['expire']);} ?>",
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
                $("#todo-date").fadeIn();
            } else {
                $("#datepicker").datepicker().slideUp();
                $("#todo-date").slideUp();
            } 
        });

        $("#todo-date").focus(function() {
            $("#datepicker").datepicker().slideDown();
        });

        $("#todo-descriptions").autosize();

        $("#edit-todo").click(function(event) {
            event.preventDefault();
            var todoName = $("#todo-name").val();
            var todoDescriptions = $("#todo-descriptions").val();
            var dueDate = $("#radioA :radio:checked").val();
            var status = $("#radioB :radio:checked").val();
            var assigned = $("#selected-user").val();

            if(todoName == "" || todoDescriptions == "" || dueDate == undefined || status == undefined || assigned == "") {
                $.achtung({message: 'Please enter all required information', timeout: 7});
            } else {
                if(window.file_selected){
                    $("#upload-message").dialog({
                        modal: true,
                    });
                }
                document['edit-todo-form'].submit();
            }

        }); 

        <?php
        $utilities->notify($notice, 7);
        if($todoRedirection) {
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
                <a class="tip" href="all-users.php" title="All Users"><img src="images/all-users.png"></a>
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

                    <form name="edit-todo-form" action="edit-todo.php?aid=<?php echo $todo[0]["id"]; ?>" method="post" enctype="multipart/form-data">
                        
                        <fieldset>
                            <label class="large-label">Edit loose task: <?php echo $todo[0]["title"]; ?></label>
                            <input name="todo-name" type="text" id="todo-name" class="text-input rounded" value="<?php echo $todo[0]["title"]; ?>" placeholder="Loose task name" required>
                            <script> 
                                var todoName = new LiveValidation("todo-name", {onlyOnSubmit: false, validMessage: "OK" });
                                todoName.add(Validate.Presence);
                                todoName.add(Validate.Length, {minimum: 3});
                            </script>
                            <textarea name="todo-descriptions" id="todo-descriptions" class="text-area rounded animated" placeholder="Describe the loose task..."><?php echo $todo[0]["description"]; ?></textarea>
                            <script> 
                                var todoDescriptions = new LiveValidation("todo-descriptions", {onlyOnSubmit: false, validMessage: "OK" });
                                todoDescriptions.add(Validate.Presence);
                                todoDescriptions.add(Validate.Length, {minimum: 10});
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
                            <label class="small-label">Task Due by</label>
                            <div id="radioA" style="position: relative;">
                                    <label for="radioA1">TODAY</label>
                                    <input type="radio" id="radioA1" value="<?php echo date("m")."/".date("d")."/".date("Y");?>" name="radioA" <?php if($utilities->isToday($todo[0]["expire"])){ echo "checked"; } ?>>
                                    <label for="radioA2"><img src="images/calendar-ico.png" alt="Select date"></label>
                                    <input type="radio" id="radioA2" value="<?php echo date("m/d/Y", $todo[0]["expire"]);?>" name="radioA" <?php if(!$utilities->isToday($todo[0]["expire"]) && $todo[0]["expire"] != 1924902000){ echo "checked"; } ?>>
                                    <label for="radioA3">WHENEVER</label>
                                    <input type="radio" id="radioA3" value="-1" name="radioA" <?php if($todo[0]["expire"] == 1924902000){ echo "checked"; } ?>>

                                    <div id="datepicker"></div> 
                                </div>
                            <input name="todo-date" type="text" value="<?php if($todo[0]["expire"] == 1924902000){ echo date("m")."/".(date("d"))."/".date("Y"); } ?>" id="todo-date" class="text-input rounded" style='float: right; display: <?php if(!$utilities->isToday($todo[0]["expire"]) && $todo[0]["expire"] < 1924902000){ echo "block"; } else { echo "none";} ?>;'>
                        </fieldset>

                        <div class="spacer"></div>

                        <!-- todo status -->
                        <fieldset>
                            <label class="small-label" style="width: 104px;">Status</label>
                            <div id="radioB">
                                <label for="radioB1">OPEN</label>
                                <input type="radio" id="radioB1" value="1" name="radioB" <?php if ($todo[0]["status"]==1) { echo "checked";} ?>>
                                <label for="radioB2">RESOLVED</label>
                                <input type="radio" id="radioB2" value="2" name="radioB" <?php if ($todo[0]["status"]==2) { echo "checked";} ?>>
                                <label for="radioB3">COMPLETE</label>
                                <input type="radio" id="radioB3" value="3" name="radioB" <?php if ($todo[0]["status"]==3) { echo "checked";} ?>>
                            </div>
                            <input name="user-role" type="hidden" id="user-role" class="text-input rounded" value="">         
                        </fieldset>
                        <!-- /todo status -->

                        <div class="spacer"></div>

                        <fieldset>
                            <label class="small-label" for="selected-user">Assign to</label>
                            <select name="selected-user" id="selected-user">
                                <option value="">Select user...</option>
                                <?php
                                for ($i=0; $i < count($allUsers); $i++) {
                                ?>
                                <option value="<?php echo $allUsers[$i]["id"]; ?>" <?php if ($todo[0]["assigned"] == $allUsers[$i]["id"]) { echo "selected";} ?>><?php echo $users->getFullUserName($allUsers[$i]["id"]); ?></option>
                                <?php } ?>
                            </select>
                        </fieldset>

                        <div class="spacer"></div>
                        

                        <fieldset>
                            <label class="small-label" style="width: 250px;" for="priority">Mark as High Priority</label>
                            <div id="priority">
                                <label for="priority1">YES</label>
                                <input type="radio" id="priority1" value="1" name="priority" <?php if ($todos->isTodoHasPriority($todo[0]["id"])) { echo 'checked'; } ?>>
                                <label for="priority2">NO</label>
                                <input type="radio" id="priority2" value="0" name="priority" <?php if (!$todos->isTodoHasPriority($todo[0]["id"])) { echo 'checked'; } ?>>
                            </div>
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <a class="orange-button default-button tip" id="edit-todo" role="button" href="#" title="Update Loose task">UPDATE</a>
                            <a class="link-button tip" id="cancel-todo" role="link" href="todo.php?aid=<?php echo $todo[0]["id"]; ?>" title="Cancel and return to Loose task page">Cancel</a>
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