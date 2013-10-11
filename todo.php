<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if (!empty($_GET["aid"])) {

            $aid = $utilities->filter($_GET["aid"]);

            $todo = $todos->getTodo($aid);

            $firstAssigned = $todos->getAssignedTodoUser($aid);
            $firstStatus = $todos->getTodoStatus($aid);
            $firstPriority = $todos->getTodoPriority($aid);

            $todoChanged = false;
            $todoRedirection = false;

            /* todo update */
            if ($utilities->isPost()) {

                $author = $session->get("userid");
                $assigned = $todo[0]["assigned"];
                $created = $utilities->getDate(); 

                if (!empty($_POST["todo-update-descriptions"])) {
                    $description = $utilities->filter($_POST["todo-update-descriptions"]);
                }              
                
                $expire = $utilities->filter($_POST["radioA"]);
                $status = $utilities->filter($_POST["radioB"]);
                $reassigned = $utilities->filter($_POST["selected-user"]);
                
                $expire = $utilities->setExpirationTime($expire);
                $completed = $utilities->getDate();

                $priority = $utilities->filter($_POST["priority"]);

                $fileTempName = $_FILES["file"]["tmp_name"][0];

                $allFiles = $utilities->reArrayFiles($_FILES['file']);

                $managerEmail = $users->getUserEmail($todo[0]["author"]);

                // update text and file
                if (!empty($description) || ($allFiles[0]['tmp_name'] != "")) {

                    if (empty($description)) {
                        $description = "<p></p>";
                    }

                    $update = $updates->createUpdate($session->get("account"), $description, 0, 0, $aid, $author, $assigned, $created, 1);

                    if (is_numeric($update)) {

                        // add file
                        if (!empty($allFiles[0]['tmp_name'])) {

                            $uploadedFiles = array();

                            foreach ($allFiles as $file) {

                                $fileIdentifier = $utilities->createFileName($utilities->getDate()."_".$file['name']);
                                $filePath = UPLOAD."todo/".$aid."/".$fileIdentifier;

                                $uploadStatus = $uploads->uploadTodoFile($aid, $fileIdentifier, $file['tmp_name']);

                                if ($uploadStatus) {

                                    $uid = $update;

                                    $uploadUpdate = $uploads->createUpload($session->get("account"), $file['name'], $fileIdentifier, $file['size'], $file['type'], $filePath, 0, 0, $aid, $uid, $author, $created, 1);

                                    $notice .= "File <b>".$file['name']."</b> was successfully uploaded <br>";

                                    array_push($uploadedFiles, $filePath);
                                
                                } else {

                                    if(!empty($file['tmp_name'])) {

                                        $notice .= "Error while uploading file: <b>".$file['name']."</b><br>";

                                    }

                                }

                            }
                        }

                        // text update notification
                        if($description != "<p></p>") {
                            
                            /* notifications */
                            $notifications->todoUpdateNotify(array($users->getUserEmail($assigned)), $aid, $todo[0]["title"], $description, $session->get("firstname"), $users->getUserEmail($author));

                            $notice .= "Loose task successfully updated <br>";
                        }

                        if(!empty($fileTempName)) {

                            // todo file notify
                            $notifications->todoFileNotify(array($users->getUserEmail($assigned)), $aid, $todo[0]["title"], $uploadedFiles, $session->get("firstname"), $users->getUserEmail($author));

                        }

                    }

                    // redirect after update
                    $todoRedirection = true;

                }

                // update todo settings 
                if (!empty($reassigned) && !empty($expire) && !empty($status)) {

                    $todoUpdate = $todos->updateTodo($aid, $reassigned, $expire, $priority, $status);

                    if ($todoUpdate) {

                        // update todo status
                        if ($status == 2 || $status == 3) {

                            $todoComplete = $todos->completeTodo($aid, $session->get("userid"), $completed);

                            $todoChanged = true;
                            $todoRedirection = true;

                            if ($firstStatus == "OPEN") { 

                                $todoChanged = true;
                            }

                        }

                        if ($firstPriority != $priority) { 

                            $todoChanged = true;
                        }

                        // todo reassign notification
                        if ($firstAssigned != $reassigned) {
                            
                            $notifications->newTodoNotify(array($users->getUserEmail($reassigned)), $aid, $todo[0]["title"], $todo[0]["description"], $utilities->formatRemainingDate($expire, SHORT_DATE_FORMAT), $users->getUserEmail($session->get("userid")));
                                $todoChanged = true;

                            $todoChanged = true;

                        }

                        // todo changes notification
                        if($todoChanged){
                            
                            $notifications->todoChangeNotify(array($managerEmail), $aid, $todo[0]["title"], $users->getUserFirstName($session->get("userid")), $users->getUserEmail($author));

                            $notice .= "Loose task successfully changed <br>";
                        }

                        

                    } else {

                        $notice .= "Error while changing loose task <br>";

                    }
                }
                
            }

            /* assignment update delete */

            // delete update 
            if ($utilities->isGet() && !empty($_GET["action"]) && !empty($_GET["context"]) && !empty($_GET["uid"])) {
                
                $uid = $utilities->filter($_GET["uid"]);
                $action = $utilities->filter($_GET["action"]);
                $context = $utilities->filter($_GET["context"]);

                if ($updates->isUpdateAuthor($uid, $session->get("userid")) && $action == "delete" && $context == "update-file") {

                    $updates->deleteUpdate($uid);

                    $updateFiles = $uploads->getUpdateUploads(0, 0, $uid);

                    if ($updateFiles) {

                        for ($i=0; $i<count($updateFiles); $i++) {

                        $updateFile = $uploads->getUpload($updateFiles[$i]["id"]);

                        @unlink($updateFile[0]["path"]);

                        }

                        $uploads->deleteUploads(0, 0, $uid);

                    }

                    $notice .= "Loose task update deleted <br>";

                } else {

                    $notice .= "You cannot delete this update <br>";

                }
            }

            /* todo file delete */

            // delete todo file
            if ($utilities->isGet() && !empty($_GET["action"]) && !empty($_GET["context"]) && !empty($_GET["fid"])) {
                
                $fid = $utilities->filter($_GET["fid"]);
                $action = $utilities->filter($_GET["action"]);
                $context = $utilities->filter($_GET["context"]);

                if ($users->isAdmin($session->get("userid")) && $action == "delete" && $context == "todo-file") {

                    $updateFile = $uploads->getUpload($fid);

                    @unlink($updateFile[0]["path"]);

                    $uploads->deleteUpload($updateFile[0]["id"]);


                    $notice .= "Loose task file deleted <br>";

                } else {

                    $notice .= "You cannot delete this file <br>";

                }
            }

            /* todo information */
            $todo = $todos->getTodo($aid);
            $allUsers = $users->listAllUsers($session->get("account"));
            $user = $users->getUser($todo[0]["assigned"]);

            if (isset($todo)) {

                $todoUpdates = $updates->listAllTodoUpdates($aid);

            } else {
                // todo not exist
                $utilities->redirect("error.php?code=7");
            }

        } else {
            // todo not specified
            $utilities->redirect("error.php?code=4");
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
<title><?php echo APPLICATION_TITLE ?> - <?php echo $todo[0]["title"]; ?></title>

<!-- styles -->

<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/fonts.css" />
<link rel="stylesheet" href="css/jquery.ui.custom.css" />
<link rel="stylesheet" href="css/jquery.ui.selectmenu.css" />
<link rel="stylesheet" href="css/jquery.ui.achtung.css" />
<link rel="stylesheet" href="css/jquery.fileinput.css" />
<link rel="stylesheet" href="css/jquery.colorbox.css" />
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
<script src="js/jquery.fileinput.js"></script>
<script src="js/jquery.colorbox.js"></script>
<script src="js/jquery.arbitrary-anchor.js"></script>
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
            onChange: function () {
                window.file_selected = true;
                $("#more-files").show();
            }
        });

        $("#file-upload-more").customFileInput({
            onChange: function () {
                window.file_selected = true;
                $("#even-more-files").show();
            }
        });

        $("#file-upload-even-more").customFileInput({
            onChange: function () {
                window.file_selected = true;
            }
        });

        $("#radioA").buttonset();

        $("#radioB").buttonset();

        $("#priority").buttonset();

        $("#datepicker").datepicker({
            altField: "#todo-update-date", 
            altFormat: "d. MM yy",
            defaultDate: "<?php if($todo[0]['expire'] == 1924902000){ echo date('m').'/'.(date('d')).'/'.date('Y'); } else { echo date('m/d/Y', $todo[0]['expire']);} ?>",
            minDate: "-0",
            onSelect: function(dateText) {
                $("#datepicker").datepicker().slideUp();
                $("#radioA2").val(dateText);
            }
        }).hide();

        $("#radioA :radio").click(function() {
            if($("#radioA :radio:checked").attr("id") == "radioA2") {
                $("#datepicker").datepicker().slideDown();
                $("#todo-update-date").fadeIn();
            } else {
                $("#datepicker").datepicker().slideUp();
                $("#todo-update-date").slideUp();
            }
        });

        $("#update-task").click(function() {
            event.preventDefault();
            if(window.file_selected){
                $("#upload-message").dialog({
                    modal: true,
                });
            }
            return true;
        });

        $(".delete-file").click(function(event) {
            event.preventDefault();
            var link = $(this).attr("href");
            $("#delete-message").dialog({
                modal: true,
                buttons: {
                    Yes: function() {
                        $(this).dialog("close");
                        window.location.href=link;
                    },
                    No: function() {
                        $(this).dialog("close");
                    }
                }
            });
        });

        $("#todo-update-date").focus(function() {
            $("#datepicker").datepicker().slideDown();
        });

        $("#todo-update-descriptions").autosize();

        $(".update-description a").each(function() {
            var currentlink = $(this);
            var linkloc = currentlink.attr("href");
            var httpcheck = linkloc.substr(0,7);
            
            if(httpcheck != "http://") {
                if(httpcheck != "https:/") {
                    currentlink.attr('href','http://'+linkloc);
                }
                
            }

        });

        <?php if (defined("AUTOSCROLL") && AUTOSCROLL) { ?>
        window.location.href = "##last-update";
        <?php } ?>
        
        <?php
        $utilities->notify($notice, 7);
        ?>

        $(".update-file").delegate("a[rel='lightbox']", "click", function (event) {
            event.preventDefault();
            $.colorbox({href: $(this).attr("href"),
                overlayClose: true,
                iframe: false,
                opacity: 0.3,
                photo: true,
                maxWidth: "100%",
                maxHeight: "100%"
            });
        });

        $("#search-field").autocomplete({
            source: "lib/api/get-my-tasks.php",
            minLength: 3,
            select: function(event, ui) {
                window.location.href = ui.item.id;
            }
        });

        <?php

        if ($todoRedirection) {

            $utilities->notify("Redirecting...", 7);

            $redirectionUrl = $session->get("redirection");

            ?>   
                window.setTimeout(function() { window.location.href='my-todos.php'; }, 3000);
            <?php
        }
        ?>
        
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
                <a class="tip" href="my-todos.php" title="View all Loose Tasks"><img src="images/all-todos.png"></a>
                <?php
                if ($users->isAdmin($session->get("userid"))) {
                ?>
                <a id="edit-task" class="tip" href="edit-todo.php?aid=<?php echo $todo[0]["id"]; ?>" title="Edit Loose Task: <?php echo $todo[0]["title"]; ?>"><img src="images/edit-project.png"></a>
                <?php 
                }
                ?>
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
                <h1>LOOSE TASK: <span class="<?php if($todos->isTodoExpired($todo[0]["id"])){ echo 'striked';} ?>"><?php echo $todo[0]["title"]; ?></span></h1>
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
                <!-- todo text -->
                <article id="project-description">

                    <!-- todo meta -->
                    <div id="project-meta" class="rounded">
                        <!-- todo author -->
                        <div id="project-author">
                            <p>Assigned to: 
                            <strong>
                                <?php 
                                if (isset($todo[0]["assigned"])) {
                                    echo $users->getShortUserName($todo[0]["assigned"]);
                                }
                                ?>
                            </strong>
                            </p>
                            <p>Created by: 
                                <strong>
                                <?php 
                                if (isset($todo[0]["author"])) {
                                    echo $users->getShortUserName($todo[0]["author"]);
                                }
                                ?>
                                </strong>
                            </p>

                        </div>
                        <!-- /todo author -->
                        <!-- todo due -->
                        <div id="project-timing">
                            <p>Due: <strong><a class="tip underlined" title="<?php echo $utilities->formatDateTime($todo[0]["expire"], LONG_DATE_FORMAT, TIME_FORMAT); ?>" href="#"><?php echo $utilities->formatRemainingDate($todo[0]["expire"], SHORT_DATE_FORMAT); ?></a></strong></p>
                            <p>Status: <strong><?php echo $todos->getTodoStatus($aid)?></strong></p>
                            <p>Created: <strong><a class="tip underlined" title="<?php echo $utilities->formatDateTime($todo[0]["created"], LONG_DATE_FORMAT, TIME_FORMAT); ?>" href="#"><?php echo $utilities->elapsedTime($todo[0]["created"])?></a></strong></p>
                        </div>
                        <!-- /todo due -->
                    </div>
                    <!-- /todo meta -->

                    <!-- todo description -->
                    <div class="default-text">
                        <p><?php echo $utilities->createLinks($utilities->parseSmileys(nl2br($todo[0]["description"]))); ?></p>
                    </div>
                    <!-- /todo description -->

                    <?php
                    $todoFiles = $uploads->getTodoUploads($aid);
                    //var_dump($todoFiles);
                    if ($todoFiles) {
                    ?>

                    <!-- todo files -->
                    <div class="default-text">

                        <!-- todo files label -->
                        <p class="attachment-label">Loose task files:</p>
                        <!-- /todo files label -->

                        <?php
                        for ($n=0; $n < count($todoFiles); $n++) {
                            $todoFile = $uploads->getUpload($todoFiles[$n]["id"]);
                        ?>

                        <!-- todo file -->
                        <div class="update-file rounded">
                            <!-- todo file icon -->
                            <p><img src="images/file-ico.png"><a class="tip" href="#" role="link" title="File Size: <?php echo $uploads->getFileSize($todoFile[0]["id"]); ?><br>File Type: <?php echo $uploads->getFileType($todoFile[0]["id"]); ?><br>File Extension: <?php echo $uploads->getFileExtension($todoFile[0]["id"]); ?>"><?php echo $todoFile[0]["name"]; ?></a></p>
                            <!-- /todo file icon -->
                            <br>
                            <!-- todo file meta -->
                            <p class="smaller-text">File Size: <strong><?php echo $uploads->getFileSize($todoFile[0]["id"]); ?></strong></p>
                            <p class="smaller-text">File Type: <strong><?php echo $uploads->getFileType($todoFile[0]["id"]); ?></strong></p>
                            <!-- /todo file meta -->
                            <br>
                            <!-- todo file buttons -->
                            <p>
                                <a class="download-link" href="file-download.php?pid=0&tid=0&uid=0&fid=<?php echo $todoFile[0]["id"]; ?>">Download</a>
                                <?php if($uploads->getFileType($todoFile[0]["id"]) == "image") { ?>
                                <a rel="lightbox" class="download-link" href="<?php echo $todoFile[0]["path"]; ?>">View</a>
                                <?php } ?>
                                <?php if ($users->isAdmin($session->get("userid"))) { ?>
                                <a class="download-link delete-file" href="todo.php?aid=<?php echo $aid; ?>&fid=<?php echo $todoFile[0]["id"]; ?>&action=delete&context=todo-file">Delete</a>
                                <?php } ?>
                            </p>
                            <!-- /todo file buttons -->

                            <?php if($uploads->getFileType($todoFile[0]["id"]) == "image") { ?>
                            <!-- todo file preview -->
                            <div class="image-preview">
                                <a rel="lightbox" href="<?php echo $todoFile[0]["path"]; ?>">
                                <img class="rounded hidden" src="lib/classes/timthumb/timthumb.php?src=<?php echo PATH."/".$todoFile[0]["path"]; ?>&h=95&zc=1" alt=""/>
                                </a>
                            </div>
                            <!-- /todo file preview -->
                            <?php } ?>
                        </div>
                        <!-- /todo file -->
                        <?php
                        }
                        ?>
                    </div>
                    <!-- /task files -->
                    <?php
                    }
                    ?>

                </article>
                <!-- /todo text -->

                <!-- task updates -->
                <article id="project-updates">

                    <!-- task header -->
                    <div class="task-header">
                        <div class="task-title">
                            <p>Updates</p>
                        </div>
                    </div>
                    <!-- /task header -->

                    <?php 
                    if($todos->isTodoEmpty($aid)) {  
                    ?>
                    <!-- todo has no updates -->
                    <div id="add-first-task" style="margin-bottom: 20px;">
                        <a class="blue-button default-button shadow tip" role="button" href="#" title="This loose task has no updates at this moment." onClick="return false;">There are no updates for this loose task</a>
                    </div>
                    <!-- /task has no updates -->
                    <?php 
                    } 
                    ?>

                    <?php

                    $index = 1;
                    $totalUpdates = count($todoUpdates);

                    for ($i=0; $i < $totalUpdates; $i++) {
                    ?>

                    <?php
                    if($index == $totalUpdates) {
                    ?>
                    <a id="last-update" style="position: relative; display: block; float: left; width: 900px; margin-top: -100px;"></a>
                    <?php
                    }
                    ?>

                    <!-- update -->
                    <div class="updates <?php if ($roles->isTodoAssigned($aid, $todoUpdates[$i]["author"])) { echo 'updates-bg2'; } else { echo 'updates-bg1';} ?>">
                        
                        <!-- update content -->
                        <div class="updates-text">
                            <?php 
                            if ($updates->isUpdateAuthor($todoUpdates[$i]["id"], $session->get("userid"))) {
                            ?>
                            <!-- update remove button -->
                            <a class="remove-update tip delete-file" title="Remove this update" href="todo.php?aid=<?php echo $aid; ?>&uid=<?php echo $todoUpdates[$i]["id"]; ?>&action=delete&context=update-file"><img src="images/remove.png"></a>
                            <!-- /update remove button -->
                            <?php } ?>

                            <!-- update text and attachment -->
                            <div class="default-text">
                                <!-- update text -->
                                <p class="update-description"><?php echo $utilities->createLinks($utilities->parseSmileys(nl2br($todoUpdates[$i]["description"]))); ?></p>
                                <!-- /update text -->

                                <?php
                                $updateFiles = $uploads->getUpdateUploads(0, 0, $todoUpdates[$i]["id"]);
                                if ($updateFiles) {
                                ?>

                                <!-- update attachment label -->
                                <p class="attachment-label">Attachment:</p>
                                <!-- /update attachment label -->

                                <?php
                                for ($n=0; $n < count($updateFiles); $n++) {
                                    $updateFile = $uploads->getUpload($updateFiles[$n]["id"]);
                                ?>
                                    <!-- update attachment -->
                                    <div class="update-file rounded">
                                        <!-- update attachment icon -->
                                        <p><img src="images/file-ico.png"><a class="tip" href="#" role="link" title="File Size: <?php echo $uploads->getFileSize($updateFile[0]["id"]); ?><br>File Type: <?php echo $uploads->getFileType($updateFile[0]["id"]); ?><br>File Extension: <?php echo $uploads->getFileExtension($updateFile[0]["id"]); ?>"><?php echo $updateFile[0]["name"]; ?></a></p>
                                        <!-- /update attachment icon -->
                                        <br>
                                        <!-- update attachment meta -->
                                        <p class="smaller-text">File Size: <strong><?php echo $uploads->getFileSize($updateFile[0]["id"]); ?></strong></p>
                                        <p class="smaller-text">File Type: <strong><?php echo $uploads->getFileType($updateFile[0]["id"]); ?></strong></p>
                                        <!-- /update attachment meta -->
                                        <br>
                                        <!-- update attachment buttons -->
                                        <p>
                                            <a class="download-link" href="file-download.php?pid=0&tid=0&uid=<?php echo $taskUpdates[$i]["id"]; ?>&fid=<?php echo $updateFile[0]["id"]; ?>">Download</a>
                                            <?php if($uploads->getFileType($updateFile[0]["id"]) == "image") { ?>
                                            <a rel="lightbox" class="download-link" href="<?php echo $updateFile[0]["path"]; ?>">View</a>
                                            
                                            <?php } ?>
                                        </p>
                                        <!-- /update attachment buttons -->

                                        <?php if($uploads->getFileType($updateFile[0]["id"]) == "image") { ?>
                                        <!-- update attachment preview -->
                                        <div class="image-preview">
                                            <a rel="lightbox" href="<?php echo $updateFile[0]["path"]; ?>">
                                            <img class="rounded hidden" src="lib/classes/timthumb/timthumb.php?src=<?php echo PATH."/".$updateFile[0]["path"]; ?>&h=95&zc=1" alt=""/>
                                            </a>
                                        </div>
                                        <!-- /update attachment preview -->
                                        <?php } ?>
                                    </div>
                                    <!-- /update attachment -->
                                <?php
                                }
                                }
                                ?>
                                
                            </div>
                            <!-- /update text and attacments -->
                        </div>
                        <!-- update content -->
                        <!-- update meta -->
                        <div class="updates-meta">
                            <p>
                            <strong>
                            <img src="images/author.png">
                            <?php 
                            if (isset($todoUpdates[$i]["author"])) {
                                echo $users->getShortUserName($todoUpdates[$i]["author"]);
                            }
                            ?>
                            </strong>
                            on <img src="images/date.png">                               
                            <?php echo $utilities->formatDateTime($todoUpdates[$i]["created"], LONG_DATE_FORMAT, TIME_FORMAT); ?>
                            Assigned <img src="images/assigned.png">
                            <strong>
                            <?php 
                            if (isset($todoUpdates[$i]["assigned"])) {
                                echo $users->getShortUserName($todoUpdates[$i]["assigned"]);
                            }
                            ?>
                            </strong>
                            </p>
                        </div>
                        <!-- /update meta -->
                    </div>
                    <!-- /update  -->

                    <?php
                        $index++;
                    } 
                    ?>
                               
                    <!-- complete  -->
                    <?php 
                    if (!empty($todo[0]["finished"]) && $todos->getTodoStatus($aid) != "OPEN") {
                    ?>
                    <div class="complete">
                        <p>
                        <strong><?php echo $todos->getTodoStatus($aid); ?></strong> 
                        by:                  
                        <strong><?php echo $users->getShortUserName($todo[0]["finished"]); ?></strong>     
                    
                        <?php 
                        if (!empty($todo[0]["completed"])) { 
                        ?> 
                        on 
                        <?php echo $utilities->formatDateTime($todo[0]["completed"], LONG_DATE_FORMAT, TIME_FORMAT); ?>
                        </p>
                    </div>
                    <?php 
                        }
                    } 
                    ?> 
                    <!-- /complete  -->

                </article>
                <!-- task updates -->

            </div>             
            <!-- /main content -->
        </section>
        <!-- /main wrapper -->

        <!-- todo update -->
        <section id="task-update-section">

            <div id="task-update-content" class="project-tasks clearer">

                <article id="project-comment">

                    <form name="update-todo-form" action="todo.php?aid=<?php echo $aid; ?>" method="post" enctype="multipart/form-data">
                        
                        <!-- todo comment -->
                        <fieldset>
                            <label class="small-label">Update</label>
                            <textarea name="todo-update-descriptions" id="todo-update-descriptions" class="text-area rounded animated" placeholder="Leave a comment..." tabindex="1"></textarea>
                            <script> 
                                var todoUpdateDescriptions = new LiveValidation("todo-update-descriptions", {onlyOnSubmit: false, validMessage: "OK" });
                                todoUpdateDescriptions.add(Validate.Presence);
                                todoUpdateDescriptions.add(Validate.Length, {minimum: 10});
                            </script>
                        </fieldset>
                        <!-- todo comment -->

                        <div class="spacer"></div>

                        <!-- task files -->
                        <fieldset>
                            <label class="small-label" for="file">Add files <a class="tip" href="#" title="Your server configuration allow maximum file size of <?php echo $utilities->getMaximumUpload(); ?> MB.<br>To select multiple files hold <b>Ctrl</b> key."><img src="images/help.png"></a></label>
                            <input type="file" name="file[]" id="file-upload" multiple="multiple">
                        </fieldset>
                        <!-- /task files -->

                        <!-- task files -->
                        <fieldset id="more-files" style="display: none; margin-top: 20px;">
                            <label class="small-label" for="file">Add more files</label>
                            <input type="file" name="file[]" id="file-upload-more" multiple="multiple">
                        </fieldset>
                        <!-- /task files -->

                        <!-- task files -->
                        <fieldset id="even-more-files" style="display: none; margin-top: 20px;">
                            <label class="small-label" for="file">Add even more files</label>
                            <input type="file" name="file[]" id="file-upload-even-more" multiple="multiple">
                        </fieldset>
                        <!-- /task files -->

                        <div class="spacer"></div>

                        <div style="display: <?php if($users->isAdmin($session->get("userid"))) { echo 'block'; } else { echo 'none'; } ?>">  
                            <!-- todo date -->
                            <fieldset>
                                <label class="small-label">Change date to</label>
                                <div id="radioA" style="position: relative;">
                                    <label for="radioA1">TODAY</label>
                                    <input type="radio" id="radioA1" value="<?php echo date("m")."/".date("d")."/".date("Y");?>" name="radioA" <?php if($utilities->isToday($todo[0]["expire"])){ echo "checked"; } ?>>
                                    <label for="radioA2"><img src="images/calendar-ico.png" alt="Select date"></label>
                                    <input type="radio" id="radioA2" value="<?php echo date("m/d/Y", $todo[0]["expire"]);?>" name="radioA" <?php if(!$utilities->isToday($todo[0]["expire"]) && $todo[0]["expire"] != 1924902000){ echo "checked"; } ?>>
                                    <label for="radioA3">WHENEVER</label>
                                    <input type="radio" id="radioA3" value="-1" name="radioA" <?php if($todo[0]["expire"] == 1924902000){ echo "checked"; } ?>>

                                    <div id="datepicker" style="top: 0px; left: 630px;"></div> 
                                </div>
                                <input name="todo-update-date" type="text" value="<?php if($todo[0]["expire"] == 1924902000){ echo date("m")."/".(date("d"))."/".date("Y"); } ?>" id="todo-update-date" class="text-input rounded" style='display: <?php if(!$utilities->isToday($todo[0]["expire"]) && $todo[0]["expire"] < 1924902000){ echo "block"; } else { echo "none";} ?>; margin-left: -8px;'>         
                            </fieldset>
                            <!-- /todo date -->

                        </div>

                        <div class="spacer"></div>

                        <!-- todo status -->
                        <fieldset>
                            <label class="small-label">Change status to</label>
                            <div id="radioB">
                                <label for="radioB1">OPEN</label>
                                <input type="radio" id="radioB1" value="1" name="radioB" <?php if ($todo[0]["status"]==1) { echo "checked";} ?>>
                                <label for="radioB2">RESOLVED</label>
                                <input type="radio" id="radioB2" value="2" name="radioB" <?php if ($todo[0]["status"]==2) { echo "checked";} ?>>
                                <label for="radioB3">COMPLETE</label>
                                <input type="radio" id="radioB3" value="3" name="radioB" <?php if ($todo[0]["status"]==3) { echo "checked";} ?> <?php if(!$users->isAdmin($session->get("userid"))) { echo 'disabled'; } ?>>
                            </div>
                            <input name="user-role" type="hidden" id="user-role" class="text-input rounded" value="">         
                        </fieldset>
                        <!-- /todo status -->

                        <div style="display: <?php if($users->isAdmin($session->get("userid"))) { echo 'block'; } else { echo 'none'; } ?>">

                            <div class="spacer"></div>

                            <!-- todo reassignment -->
                            <fieldset>
                                <label class="small-label" for="selected-user">Re-assign to</label>
                                <select name="selected-user" id="selected-user">
                                    <?php
                                    for ($i=0; $i < count($allUsers); $i++) {
                                    ?>
                                    <option value="<?php echo $allUsers[$i]["id"]; ?>" <?php if ($todo[0]["assigned"] == $allUsers[$i]["id"]) { echo "selected";} ?>><?php echo $users->getFullUserName($allUsers[$i]["id"]) ?></option>
                                    <?php } ?>
                                </select>
                            </fieldset>
                            <!-- /todo reassignment -->

                            <div class="spacer"></div>

                            <fieldset>
                                <label class="small-label" for="priority">High Priority</label>
                                <div id="priority">
                                    <label for="priority1">YES</label>
                                    <input type="radio" id="priority1" value="1" name="priority" <?php if ($todos->isTodoHasPriority($todo[0]["id"])) { echo 'checked'; } ?>>
                                    <label for="priority2">NO</label>
                                    <input type="radio" id="priority2" value="0" name="priority" <?php if (!$todos->isTodoHasPriority($todo[0]["id"])) { echo 'checked'; } ?>>
                                </div>
                            </fieldset>

                        </div>

                        <div class="spacer"></div>

                        <!-- submit -->
                        <fieldset>
                            <a class="orange-button default-button tip" id="update-task" role="button" href="#" onClick="document['update-todo-form'].submit(); return false;" title="Update loose task" tabindex="2">UPDATE</a>

                            <a class="link-button tip" id="cancel-todo-update" role="link" href="my-todos.php" title="Cancel and return to My Loose Tasks">Cancel</a>
                        </fieldset>
                        <!-- /submit -->

                    </form>

                </article>

            </div>

        </section>
        <!-- /todo update -->

        <div id="upload-message" title="Upload in progress..." style="display: none;">
            <div style="width: 32px; height: 32px; margin: 30px auto 0 auto;"><img src="css/images/loading.gif"></div>
        </div>

        <div id="delete-message" title="Delete update" style="display: none;">
            <p>Do you really want to delete this update?</p>
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