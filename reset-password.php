<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn()) {

        if ($utilities->isPost()) {

            if (!empty($_POST["current-password"]) && !empty($_POST["new-password1"]) && !empty($_POST["new-password1"])) {

                $currentPassword = $utilities->filter($_POST["current-password"]);
                $newPassword1 = $utilities->filter($_POST["new-password1"]);
                $newPassword2 = $utilities->filter($_POST["new-password2"]);

                if($newPassword1 == $newPassword2) {

                    $passwordCheck = $auth->createHash("md5", $currentPassword, HASH_PASSWORD_KEY);
                    $user = $users->getUser($session->get("userid"));

                    if($passwordCheck == $user[0]["password"]){

                        $newPassword = $auth->createHash("md5", $newPassword1, HASH_PASSWORD_KEY);
                        $reset = $auth->resetPassword($user[0]["id"], $newPassword);

                        $notifications->userPasswordReset(array($user[0]["email"]), $newPassword1);

                        $notice = "Your password has changed successfully";
                        $utilities->redirect("my-account.php", 3);  

                    } else {
                        $notice = "Current password is wrong";
                    }

                } else {
                    $notice = "Password does not match";
                }  

            } else {
                $notice = "Enter all required information"; 
            }
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
<title><?php echo APPLICATION_TITLE ?> - Password Reset</title>

<!-- styles -->

<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/fonts.css" />
<link rel="stylesheet" href="css/jquery.ui.custom.css" />
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
<script src="js/jquery.stickypanel.js"></script>
<script src="js/jquery.ui.achtung.js"></script>
<script src="js/jquery.tiptip.js"></script>
<script src="js/script.js"></script>

<!-- remove for production -->
<script src="js/cssrefresh.js"></script>


<!-- javascript -->

<script>
    $(document).ready(function() {

        <?php
        $utilities->notify($notice, 7);
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
                <?php 
                if ($users->isAdmin($session->get("userid"))) {
                ?>
                <a class="separator"><img src="images/separator.png"></a>
                <a class="tip" href="all-users.php" title="Users"><img src="images/all-users.png"></a>
                <?php } ?>
                <a class="tip" href="my-account.php" title="My Account"><img src="images/user.png"></a>
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
            
        </header>
        <!-- /header -->
        
        <!-- main wrapper -->
        <section id="main-wrapper" class="no-bg">
            <!-- main content -->
            <div id="main-content">
                <!-- page content -->
                <article id="select-project">

                    <form name="password-reset" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                    
                        <fieldset>
                            <label class="large-label">Current Password</label>
                            <input name="current-password" type="password" id="current-password" class="text-input rounded" placeholder="Current Password" required>
                        </fieldset>

                        <fieldset class="split-fieldset">
                            <label class="large-label">New Password</label>
                            <input name="new-password1" type="password" id="new-password1" class="text-input rounded" placeholder="New Password" required>
                            <input name="new-password2" type="password" id="new-password2" class="text-input rounded" placeholder="Repeat New Password" required>   
                        </fieldset>

                        <div class="spacer"></div>
                        
                        <fieldset>
                            <a class="orange-button default-button" id="login" role="button" href="#" onClick="document['password-reset'].submit(); return false;" title="Login">RESET</a>
                            <a class="link-button tip" id="cancel-reset" role="link" href="my-account.php" title="Cancel and return to profile">Cancel</a>
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