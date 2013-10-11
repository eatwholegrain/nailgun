<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if (!empty($_GET["uid"])) {

            $uid = $utilities->filter($_GET["uid"]);

            $userRedirection = false;

            if ($users->isAdmin($session->get("userid"))) {
                // should be at the end
                $user = $users->getUser($uid);

                if (isset($user)) {

                      $allProjects = $projects->listAllProjects($session->get("account"));

                } else {
                    // user not exist
                    $utilities->redirect("error.php?code=8");
                }

            } else {
                // permission problem
                $utilities->redirect("error.php?code=5");
            }

            // update user info and permissions
            if ($utilities->isPost() && $users->isOwner($session->get("userid"))) {

                if (!empty($_POST["user-firstname"]) && !empty($_POST["user-lastname"]) && !empty($_POST["user-email"]) && !empty($_POST["user-username"]) && !empty($_POST["radio"]) && !empty($_POST["user-id"])) {

                    $userid = $utilities->filter($_POST["user-id"]);
                    $firstname = $utilities->filter($_POST["user-firstname"]);
                    $lastname = $utilities->filter($_POST["user-lastname"]);
                    $email = $utilities->filter($_POST["user-email"]);
                    $username = $utilities->filter($_POST["user-username"]);
                    $role = $utilities->filter($_POST["radio"]);

                    $update = $users->updateUser($userid, $firstname, $lastname, $email, $username, $role);
                    
                    if ($update) {
                        
                        for ($i=0; $i < count($allProjects); $i++) {

                            if (!empty($_POST["radio-".$allProjects[$i]["id"]])) {

                                if ($roles->isRoleSet($allProjects[$i]["id"], $userid)) {

                                    $role = $roles->updateRole($allProjects[$i]["id"], $userid, $_POST["radio-".$allProjects[$i]["id"]]);

                                } else {

                                    $role = $roles->createRole($allProjects[$i]["id"], $userid, $_POST["radio-".$allProjects[$i]["id"]]);

                                }

                            } 
                        }
                        
                        $notifications->userUpdateNotify(array($email), $users->getUserEmail($session->get("userid")));

                        $notice = "User <strong>".$firstname." ".$lastname."</strong> successfully updated.";

                        $userRedirection = true;

                        $utilities->redirect("all-users.php", 3);

                    } else {

                        $notice = "Error while updating user";

                    }

                } else {

                    $notice = "Enter all required information";

                }
                
            }

            // delete user
            if ($utilities->isGet() && !empty($_GET["rid"]) && !empty($_GET["action"]) && $users->isOwner($session->get("userid"))) {  

                $rid = $utilities->filter($_GET["rid"]);
                $action = $utilities->filter($_GET["action"]);

                if ($action == "delete") {

                    $role = $roles->deleteRole($rid);

                }
            } 

            // delete user roles
            if ($utilities->isGet() && !empty($_GET["action"]) && $users->isOwner($session->get("userid"))) { 

                $action = $utilities->filter($_GET["action"]);

                if ($action == "remove") {

                    $role = $roles->deleteAllUserRole($uid);
                    $user = $users->deleteUser($uid);
                    $utilities->redirect("all-users.php");

                }
            }

        } else {
            // user not specified
            $utilities->redirect("error.php?code=3");
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
<title><?php echo APPLICATION_TITLE ?> - <?php echo $user[0]["firstname"]." ".$user[0]["lastname"]; ?></title>

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
<script src="js/script.js"></script>

<!-- remove for production -->
<script src="js/cssrefresh.js"></script>


<!-- javascript -->

<script>
    $(document).ready(function() {

        $("#radio").buttonset();

        $("#user-role").hide();

        $("#radio :radio").click(function() {
            $("#user-role").val($("#radio :radio:checked").attr("value"));
        });

        <?php
        $utilities->notify($notice, 7);

        if($userRedirection) {
            $utilities->notify("Redirecting...", 7);
        }
        ?>
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
            <?php if (defined("SHORTCUTS") && SHORTCUTS) { ?>
            <!-- breadcrumbs -->
            <div class="breadcrumbs">
                <a class="tip" href="home.php" title="Home: Select project"><img src="images/home.png"></a>
                <a class="separator"><img src="images/separator.png"></a>
                <?php 
                if ($users->isAdmin($session->get("userid"))) {
                ?>
                <a class="tip" href="all-users.php" title="Users"><img src="images/all-users.png"></a>
                <?php } ?>
                <?php 
                if ($users->isAdmin($session->get("userid"))) {
                ?>
                <a class="tip" href="user-todos.php?uid=<?php echo $user[0]["id"]; ?>" title="<?php echo $user[0]["firstname"]; ?> assignments"><img src="images/all-todos.png"></a>
                <a class="tip" href="statistic-users.php?uid=<?php echo $user[0]["id"]; ?>" title="<?php echo $user[0]["firstname"]; ?> statistics"><img src="images/statistics.png"></a>
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

                <?php 
                if ($users->isAdmin($session->get("userid"))) {
                ?>
                <!-- add assignment -->
                <div class="add">
                    <a id="add-project-button" class="tip" href="add-todo.php?uid=<?php echo $user[0]["id"]; ?>" role="link" title="Add new lose task to <?php echo $user[0]["firstname"]; ?>">Add lose task to <?php echo $user[0]["firstname"]; ?></a>
                </div>
                <!-- /add assignment -->
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
                <article id="add-user">

                    <form name="update-user-form" action="user.php?uid=<?php echo $uid; ?>" method="post">
                        
                        <fieldset class="split-fieldset">
                            <label class="large-label">User information</label>
                            <input name="user-firstname" type="text" id="user-firstname" class="text-input rounded" value="<?php echo $user[0]["firstname"]; ?>">
                            <input name="user-lastname" type="text" id="user-lastname" class="text-input rounded" value="<?php echo $user[0]["lastname"]; ?>">
                        </fieldset>


                        <fieldset>
                            <label for="user-email">Email</label>
                            <input name="user-email" type="text" id="user-email" class="text-input rounded" value="<?php echo $user[0]["email"]; ?>">
                        </fieldset>

                        <fieldset class="split-fieldset">
                            <label for="user-username">Username</label>
                            <input name="user-username" type="text" id="user-username" class="text-input rounded" value="<?php echo $user[0]["username"]; ?>">
                            <input name="user-id" type="hidden" id="user-id" class="text-input rounded" value="<?php echo $user[0]["id"]; ?>" readonly="readonly">
                        </fieldset>

                        <br>

                        <fieldset>
                            <label class="small-label">Role</label>
                            <div id="radio">
                                <label for="radio1">OWNER</label>
                                <input type="radio" id="radio1" value="1" name="radio" <?php if($users->getUserStatus($user[0]["id"]) == "Owner"){ echo 'checked'; }?>>
                                <label for="radio2">ADMIN</label>
                                <input type="radio" id="radio2" value="2" name="radio" <?php if($users->getUserStatus($user[0]["id"]) == "Admin"){ echo 'checked'; }?>>
                                <label for="radio3">USER</label>
                                <input type="radio" id="radio3" value="3" name="radio" <?php if($users->getUserStatus($user[0]["id"]) == "User"){ echo 'checked'; }?>>
                            </div>
                            <input name="user-role" type="text" id="user-role" class="text-input rounded" value="" style="float: right;">         
                        </fieldset>

                        <br>

                        <fieldset>

                            <div class="check-table">
                                <!-- user role header -->
                                <div class="check-table-header">
                                    <div class="check-table-col1">
                                        <p>Assigned to projects as</p>
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
                                for ($i=0; $i < count($allProjects); $i++) {
                                ?>

                                <div class="check-table-row">
                                    <div class="check-table-col1">
                                        <p><?php echo $allProjects[$i]["title"]; ?></p>
                                    </div>
                                    <div class="check-table-col2">
                                        <p><input type="radio" id="radio-user-<?php echo $i; ?>" value="2" name="radio-<?php echo $allProjects[$i]["id"]; ?>" <?php if($roles->isProjectUser($allProjects[$i]["id"], $user[0]["id"])){ echo 'checked';} ?>></p>
                                    </div>
                                    <div class="check-table-col3">
                                        <p><input type="radio" id="radio-manager-<?php echo $i; ?>" value="1" name="radio-<?php echo $allProjects[$i]["id"]; ?>" <?php if($roles->isProjectManager($allProjects[$i]["id"], $user[0]["id"])){ echo 'checked';} ?>></p>
                                    </div>
                                    <?php 
                                    if ($users->isOwner($session->get("userid"))) {
                                    ?>
                                    <div class="check-table-col4">
                                        <a class="remove-update tip" title="Remove this role" href="user.php?pid=<?php echo $allProjects[$i]["id"]; ?>&uid=<?php echo $uid; ?>&rid=<?php echo $roles->getRoleID($allProjects[$i]["id"], $uid); ?>&action=delete"><img src="images/delete.png"></a>
                                    </div>
                                    <?php } ?>
                                </div>

                                <?php } ?>

                                <?php
                                if (count($allProjects) == 0) {
                                    ?>
                                    <div class="info-bar rounded">
                                        <p>There are no projects</p>
                                    </div>

                                    <?php if($users->isOwner($session->get("userid"))) { ?>
                                    <div class="spacer-small"></div>

                                    <a class="orange-button default-button tip" role="button" href="add-project.php" title="Create your first project">Create project</a>
                                    <?php
                                    }
                                }
                                ?>
                                
                            </div>
                        </fieldset>


                        <!-- explanations -->
                        <div id="explanations">

                            <!-- registered -->
                            <div class="check-table-row info-col">
                                <div class="check-table-col-full">
                                    <p><img src="images/registered.png">&nbsp;&nbsp;Registered on <?php echo $utilities->formatDateTime($user[0]["created"], SHORT_DATE_FORMAT, TIME_FORMAT); ?></a></p>
                                </div>
                            </div>
                            <!-- /registered -->
                            
                            <div class="spacer"></div>
                            
                            <!-- permissions -->
                            <?php
                            if($users->getUserStatus($session->get("userid")) == "Owner") {    
                            ?>
                        
                            <div class="check-table-row note-col">
                                <div class="check-table-col-full">
                                    <p>Owner can update all users information<a class="tip info-icon" title="<b>Owner</b> - can add new user or can edit all user information including global or project specific roles. You can change your personal information from My Account page" href="#"><img src="images/help.png"></a></p>
                                </div>
                            </div>
                            <?php } else if($users->getUserStatus($session->get("userid")) == "Admin"){
                            ?>
                            <div class="check-table-row note-col">
                                <div class="check-table-col-full">
                                    <p>Admin cannot update other users information<a class="tip info-icon" title="<b>Admin</b> - cannot add new user or edit any user information including global or project specific roles. You can change your personal information from My Account page." href="#"><img src="images/help.png"></a></p>
                                </div>
                            </div>
                            <?php } else if($users->getUserStatus($session->get("userid")) == "User"){
                            ?>

                            <div class="check-table-row note-col">
                                <div class="check-table-col-full">
                                    <p>User cannot update other users information<a class="tip info-icon" title="<b>User</b> - cannot add new user or edit any user information including global or project specific roles. You can change your personal information from My Account page" href="#"><img src="images/help.png"></a></p>
                                </div>
                            </div>

                            <?php } ?>
                            <!-- /permissions -->  

                            <div class="spacer"></div>
                            

                            <?php 
                            if ($users->isOwner($session->get("userid"))) {
                            ?>
                            <!-- update user -->
                            <div id="update-user-information">
                                <fieldset>
                                    <a class="orange-button default-button tip" id="update-user" role="button" href="#" onClick="document['update-user-form'].submit(); return false;" title="Update user account">UPDATE</a>
                                    <a class="orange-button default-button tip" id="delete-user" role="button" href="user.php?uid=<?php echo $uid; ?>&action=remove" title="Delete user account">DELETE</a>
                                    <a class="link-button tip" id="cancel-user" role="link" href="all-users.php" title="Cancel and return to users">Cancel</a>
                                </fieldset>
                            </div>
                            <!-- /update user -->
                            <?php } ?>

                        </div>
                        <!-- /explanations -->

                    </form>

                    <div class="spacer"></div>

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