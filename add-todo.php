<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if ($users->isAdmin($session->get("userid"))) {

            $uid = isset($_GET["uid"]) ? $utilities->filter($_GET["uid"]) : 0;

            if ($uid > 0 && !$users->isAccountUser($uid, $session->get("account"))) {
                // permission problem
                $utilities->redirect("error.php?code=5");
            }

            $userName = isset($_GET["uid"]) ? $users->getUser($utilities->filter($_GET["uid"])) : null;

            $todoRedirection = false;

            // add todo
            if ($utilities->isPost()){

                if (!empty($_POST["todo-name"]) && !empty($_POST["todo-descriptions"]) && !empty($_POST["radio"])) {

                    $title = $utilities->filter($_POST["todo-name"]);
                    $description = $utilities->filter($_POST["todo-descriptions"]);
                    $author = $session->get("userid");
                    $assigned = $utilities->filter($_POST["selected-user"]);
                    $created = $utilities->getDate();
                    $expire = $utilities->filter($_POST["radio"]);
                    $priority = $utilities->filter($_POST["priority"]);

                    $expire = $utilities->setExpirationTime($expire);

                    $allFiles = $utilities->reArrayFiles($_FILES['file']);

                    $todo = $todos->createTodo($session->get("account"), $title, $description, $author, $assigned, $created, $expire, $priority, 1);

                    if (is_numeric($todo)) {

                        $receiver = $users->getUser($assigned);

                        $notifications->newTodoNotify(array($receiver[0]["email"]), $todo, $title, $description, $utilities->formatRemainingDate($expire, SHORT_DATE_FORMAT), $users->getUserEmail($author));
                        
                        $notice = "Assignment successfully created";
                        
                        $success = true;

                        if (!empty($allFiles[0]['tmp_name'])) {

                                foreach ($allFiles as $file) {

                                    $fileIdentifier = $utilities->createFileName($utilities->getDate()."_".$file['name']);
                                    $filePath = UPLOAD."todo/".$todo."/".$fileIdentifier;

                                    $uploadStatus = $uploads->uploadTodoFile($todo, $fileIdentifier, $file['tmp_name']);

                                    if ($uploadStatus) {

                                        $uid = 0;

                                        $uploadUpdate = $uploads->createUpload($session->get("account"), $file['name'], $fileIdentifier, $file['size'], $file['type'], $filePath, 0, 0, $todo, $uid, $author, $created, 1);

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
                        $notice = "Error while creating loose task";
                    }

                } else {

                    $success = false;
                    $notice = "Enter all required information";
                }
            }

            $allUsers = $users->listAllUsers($session->get("account"));


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
<title><?php echo APPLICATION_TITLE ?> - Create Loose Task<?php if(isset($userName)) { echo ' for '.$userName[0]["firstname"]; }?></title>

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

        $("#todo-date").slideUp();

        $("#datepicker").datepicker({
            altField: "#todo-date", 
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

        $("#create-todo").click(function(event) {
            event.preventDefault();
            var taskName = $("#todo-name").val();
            var taskDescriptions = $("#todo-descriptions").val();
            var dueDate = $("#radio :radio:checked").val();
            //var assigned = $("#selected-user").val();

            if(taskName == "" || taskDescriptions == "" || dueDate == undefined) {
                $.achtung({message: 'Please enter all required information', timeout: 7});
            } else {
                if(window.file_selected){
                    $("#upload-message").dialog({
                        modal: true,
                    });
                }
                document['add-todo-form'].submit();
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
                            window.location.href="add-todo.php";
                        },
                        No: function() {
                            window.location.href="all-users.php";
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

                    <form name="add-todo-form" action="add-todo.php" method="post" enctype="multipart/form-data">
                        
                        <fieldset>
                            <label class="large-label">Create Loose Task<?php if(isset($userName)) { echo ' for '.$userName[0]["firstname"]; }?></label>
                            <input name="todo-name" type="text" id="todo-name" class="text-input rounded" placeholder="Loose task name" required>
                            <script> 
                                var todoName = new LiveValidation("todo-name", {onlyOnSubmit: false, validMessage: "OK" });
                                todoName.add(Validate.Presence);
                                todoName.add(Validate.Length, {minimum: 3});
                            </script>
                            <textarea name="todo-descriptions" id="todo-descriptions" class="text-area rounded animated" placeholder="Describe the loose task..."></textarea>
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
                            <div id="radio">
                                <label for="radio1">TODAY</label>
                                <input type="radio" id="radio1" value="<?php echo date("m")."/".date("d")."/".date("Y");?>" name="radio">
                                <label for="radio2"><img src="images/calendar-ico.png" alt="Select date"></label>
                                <input type="radio" id="radio2" value="" name="radio">
                                <label for="radio3">WHENEVER</label>
                                <input type="radio" id="radio3" value="-1" name="radio">

                                <div id="datepicker"></div> 
                            </div>
                            <input name="todo-date" type="text" value="<?php echo date("m")."/".(date("d"))."/".date("Y");?>" id="todo-date" class="text-input rounded" style="float: right;">         
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <label class="small-label" for="selected-user">Assign to</label>
                            <select name="selected-user" id="selected-user">
                                <option value="">Select user...</option>
                                <?php
                                for ($i=0; $i < count($allUsers); $i++) {
                                ?>
                                <option value="<?php echo $allUsers[$i]["id"]; ?>" <?php if($uid == $allUsers[$i]["id"]) { echo 'selected';} ?>><?php echo $users->getFullUserName($allUsers[$i]["id"]); ?></option>
                                <?php } ?>
                            </select>
                        </fieldset>

                        <div class="spacer"></div>
                        

                        <fieldset>
                            <label class="small-label" style="width: 250px;" for="priority">Mark as High Priority</label>
                            <div id="priority">
                                <label for="priority1">YES</label>
                                <input type="radio" id="priority1" value="1" name="priority">
                                <label for="priority2">NO</label>
                                <input type="radio" id="priority2" value="0" name="priority" checked>
                            </div>
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <a class="orange-button default-button tip" id="create-todo" role="button" href="#" title="Create new Loose task">CREATE</a>
                            <a class="link-button tip" id="cancel-todo" role="link" href="all-users.php" title="Cancel and return to All Users page">Cancel</a>
                        </fieldset>
    

                    </form>

                </article>
                <!-- /page content -->
            </div>             
            <!-- /main content -->
        </section>
        <!-- /main wrapper -->
        
        <div id="dialog-message" title="New Loose Task" style="display: none;">
            <p>Do you want to create another loose task?</p>
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