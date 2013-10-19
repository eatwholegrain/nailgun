<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        $allProjects = $projects->listAllProjects($session->get("account"));

        if ($users->isAccountUser($session->get("userid"), $session->get("account"))) {

            if ($utilities->isPost()){

                if (!empty($_POST["user-firstname"]) && !empty($_POST["user-lastname"]) && !empty($_POST["user-email"]) && !empty($_POST["user-username"]) && !empty($_POST["user-id"])){

                    $firstname = $utilities->filter($_POST["user-firstname"]);
                    $lastname = $utilities->filter($_POST["user-lastname"]);
                    $email = $utilities->filter($_POST["user-email"]);
                    $username = $utilities->filter($_POST["user-username"]);

                    $edit = $users->editUser($session->get("userid"), $firstname, $lastname, $email, $username);

                    if ($edit) {
                        $notice = "Your account has successfully updated";
                    }

                } else {
                    $notice = "Enter all required information";
                }
            }

        } else {
            // account permission problem
            $utilities->redirect("error.php?code=5");
        }

        $user = $users->getUser($session->get("userid")); 
        
    } else {
        // user not loged
        $utilities->redirect("index.php?redirection=my-account.php");
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?> - <?php echo $user[0]["firstname"]; ?></title>

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
<script src="js/livevalidation.js"></script>
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

        $(":radio:not(:checked)").attr("disabled", true);

        <?php
        $utilities->notify($notice, 7);
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
                <?php 
                if ($users->isAdmin($session->get("userid"))) {
                ?>
                <a class="separator"><img src="images/separator.png"></a>
                <a class="tip" href="all-users.php" title="Users"><img src="images/all-users.png"></a>
                <?php } ?>
            </div>
            <!-- /breadcrumbs -->
            <?php } ?>

            <!-- welcome message -->
            <div id="welcome-message">
                <p>Welcome to <?php echo APPLICATION_TITLE ?> <span class="orange"><?php echo $user[0]["firstname"]; ?></span></p>
            </div>
            <!-- /welcome message -->

            <!-- top panel -->
            <div id="top-panel">

                <?php 
                if ($users->isOwner($session->get("userid"))) {
                ?>
                <!-- add user -->
                <div class="add">
                    <a id="add-user-button" class="tip" href="add-user.php" role="link" title="Add new user">Add user</a>
                </div>
                <!-- /add user -->
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

                    <form name="edit-user-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        
                        <fieldset class="split-fieldset">
                            <label class="large-label">User information</label>
                            <input name="user-firstname" type="text" id="user-firstname" class="text-input rounded" value="<?php echo $user[0]["firstname"]; ?>">
                            <script> 
                                var userFirstname = new LiveValidation("user-firstname", {onlyOnSubmit: false, validMessage: "OK" });
                                userFirstname.add(Validate.Presence);
                                userFirstname.add(Validate.Length, {minimum: 3});
                            </script>
                            <input name="user-lastname" type="text" id="user-lastname" class="text-input rounded" value="<?php echo $user[0]["lastname"]; ?>">
                            <script> 
                                var userLastname = new LiveValidation("user-lastname", {onlyOnSubmit: false, validMessage: "OK" });
                                userLastname.add(Validate.Presence);
                                userLastname.add(Validate.Length, {minimum: 3});
                            </script>
                        </fieldset>

                        <fieldset>
                            <label for="user-email">Email</label>
                            <input name="user-email" type="text" id="user-email" class="text-input rounded" value="<?php echo $user[0]["email"]; ?>">
                            <script> 
                                var userEmail = new LiveValidation("user-email", {onlyOnSubmit: false, validMessage: "OK" });
                                userEmail.add(Validate.Email);
                            </script>
                        </fieldset>

                        <fieldset class="split-fieldset">
                            <label for="user-email">Username</label>
                            <input name="user-username" type="text" id="user-username" class="text-input rounded" value="<?php echo $user[0]["username"]; ?>">
                            <script> 
                                var userUsername = new LiveValidation("user-username", {onlyOnSubmit: false, validMessage: "OK" });
                                userUsername.add(Validate.Presence);
                                userUsername.add(Validate.Length, {minimum: 3});
                            </script>
                            <a class="orange-button default-button tip" id="password-reset" role="button" href="reset-password.php" title="Reset current password and get new one">RESET PASSWORD</a>
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <label class="small-label smaller-label">Role</label>
                            <div id="user-global-role" class="rounded">
                                <p><?php echo $users->getUserStatus($user[0]["id"]); ?><?php if($users->isAdmin($user[0]["id"])) { ?><a class="tip info-icon" style="margin-left: 20px;" title="You can manage all projects and tasks" href="#"><img src="images/help.png"></a><?php } ?></p>
                            </div>

                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <div class="check-table">
                                <!-- user role header -->
                                <div class="check-table-header">
                                    <div class="check-table-col1">
                                        <p>Assigned to projects as</p>
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
                                for ($i=0; $i < count($allProjects); $i++) {
                                ?>

                                    <?php
                                    if($roles->isProjectClient($allProjects[$i]["id"], $user[0]["id"]) || $roles->isProjectUser($allProjects[$i]["id"], $user[0]["id"]) || $roles->isProjectManager($allProjects[$i]["id"], $user[0]["id"])) {
                                    ?>
                                    <div class="check-table-row">
                                        <div class="check-table-col1">
                                            <p><?php echo $allProjects[$i]["title"]; ?></p>
                                        </div>
                                        <div class="check-table-col2">
                                            <p><?php if ($roles->isProjectClient($allProjects[$i]["id"], $user[0]["id"])) { ?><img src="images/check.png"><?php } ?></p>
                                        </div>
                                        <div class="check-table-col2">
                                            <p><?php if ($roles->isProjectUser($allProjects[$i]["id"], $user[0]["id"])) { ?><img src="images/check.png"><?php } ?></p>
                                        </div>
                                        <div class="check-table-col2">
                                            <p><?php if ($roles->isProjectManager($allProjects[$i]["id"], $user[0]["id"])) { ?><img src="images/check.png"><?php } ?></p>
                                        </div>
                                    </div>

                                    <?php } ?>

                                <?php } ?>

                                <?php
                                if (count($allProjects) == 0) {
                                    ?>
                                    <div class="info-bar rounded">
                                        <p>There are no projects</p>
                                    </div>

                                    <?php if($users->isOwner($user[0]["id"])) { ?>
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
                            
                            <!-- permissions -->
                            <?php
                            if ($users->getUserStatus($session->get("userid")) == "Owner") {    
                            ?>
                        
                            <div class="check-table-row note-col">
                                <div class="check-table-col-full">
                                    <p>You have owner privileges at <?php echo $accounts->getAccountName($session->get("account")); ?><a class="tip info-icon" title="<b>Owner</b> - you can change personal information such is first name, last name, email address, username or create new password. You can change global role or project specific role for any user from Users page." href="#"><img src="images/help.png"></a></p>
                                </div>
                            </div>
                            <?php } else if ($users->getUserStatus($session->get("userid")) == "Admin") {
                            ?>
                            <div class="check-table-row note-col">
                                <div class="check-table-col-full">
                                    <p>You have admin privileges at <?php echo $accounts->getAccountName($session->get("account")); ?><a class="tip info-icon" title="<b>Admin</b> - you can change personal information such is first name, last name, email address, username or create new password. You cannot change your global role or project specific role." href="#"><img src="images/help.png"></p>
                                </div>
                            </div>
                            <?php } else if ($users->getUserStatus($session->get("userid")) == "User") {
                            ?>

                            <div class="check-table-row note-col">
                                <div class="check-table-col-full">
                                    <p>You have user privileges at <?php echo $accounts->getAccountName($session->get("account")); ?><a class="tip info-icon" title="<b>User</b> - you can change personal information such is first name, last name, email address, username or create new password. You cannot change your global role or project specific role." href="#"><img src="images/help.png"></a></p>
                                </div>
                            </div>

                            <?php } ?>
                            <!-- /permissions -->  

                            <div class="spacer"></div>

                            <?php 
                            if ($users->isUser($session->get("userid"))) {
                            ?>
                            <!-- update user -->
                            <div id="update-user-information">
                                <fieldset>
                                    <a class="orange-button default-button tip" id="edit-user" role="button" href="#" onClick="document['edit-user-form'].submit(); return false;" title="Edit your account">UPDATE</a>
                                </fieldset>
                            </div>
                            <!-- /update user -->
                            <?php } ?>
                            

                        </div>
                        <!-- /explanations -->

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