<?php
    require("lib/bootstrap.php");

    if ($utilities->isPost()) {

        if (!empty($_POST["user-email"]) && !empty($_POST["forgot-what"])) {

            $userEmail = $utilities->filter($_POST["user-email"]);
            $forgotWhat = $utilities->filter($_POST["forgot-what"]);

            $user = $users->getUserByEmail($userEmail);

            if(isset($user)) {

                if($forgotWhat == 1 && !empty($_POST["user-username"])) {

                    $userUsername = $utilities->filter($_POST["user-username"]);

                    if($userUsername == $user[0]["username"]) {
                        $tempPassword = $auth->randomPassword();
                        $newPassword = $auth->createHash("md5", $tempPassword, HASH_PASSWORD_KEY);
                        $reset = $auth->resetPassword($user[0]["id"], $newPassword);

                        $send = $notifications->sentNewPassword(array($user[0]["email"]), $tempPassword);

                        $notice = "Your new password is sent to your email: <b>".$user[0]["email"]."</b>";
                        $utilities->redirect("index.php", 6);

                    } else {
                        $notice = "You enter wrong username";
                    }
                    
                } else {
                    $notice = "You must enter your username";
                }

                if($forgotWhat == 2) {

                    $send = $notifications->sentUsername(array($user[0]["email"]), $user[0]["username"]);

                    $notice = "Your <b>username</b> is sent to your email address. Check your email and continue reseting your password";
                }

            } else {
                $notice = "<b>User does not exist</b>. Check your email address or contact system administrator";
            }

        } else {
            $notice = "Enter all required information"; 
        }
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?> - Forgot Login Information</title>

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
        $("select#forgot-what").selectmenu({
            width: 310,
            select: function(event, options) {
                if (options.value == 1) { 
                    $("#your-username").fadeIn();
                    $("#user-username").removeAttr("readonly");
                } else {
                    $("#your-username").fadeOut();
                    $("#user-username").attr("readonly","readonly");
                }
            }
        });

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
                <a class="tip" href="index.php" title="Login"><img src="images/login.png"></a>
            </div>
            <!-- /breadcrumbs -->
            <?php } ?>

            <!-- welcome message -->
            <div id="welcome-message">
                <p>Welcome to <?php echo APPLICATION_TITLE ?></p>
            </div>
            <!-- /welcome message -->
            
        </header>
        <!-- /header -->
        
        <!-- main wrapper -->
        <section id="main-wrapper" class="no-bg">
            <!-- main content -->
            <div id="main-content">
                <!-- page content -->
                <article id="select-project">

                    <form name="login-reset" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                    
                        <fieldset>
                            <label class="large-label">Your Email Address</label>
                            <input name="user-email" type="text" id="user-email" class="text-input rounded" placeholder="Email Address" required>
                            <script> 
                                var userEmail = new LiveValidation("user-email", {onlyOnSubmit: false, validMessage: "OK" });
                                userEmail.add(Validate.Email);
                            </script>
                        </fieldset>

                        <fieldset>
                            <label class="large-label" for="forgot-what">Do you know username?</label>
                            <select name="forgot-what" id="forgot-what">
                                <option value="0">...</option>
                                <option value="1">I know my username</option>
                                <option value="2">I forgot my username</option>
                            </select>
                        </fieldset>

                        <fieldset id="your-username" style="display: none; margin-top: 10px; ">
                            <label class="large-label" for="user-username">Your Username</label>
                            <input name="user-username" type="text" id="user-username" class="text-input rounded" placeholder="Your Username" readonly="readonly">
                        </fieldset>

                        <div class="spacer"></div>
                        
                        <fieldset>
                            <a class="orange-button default-button tip" id="login" role="button" href="#" onClick="document['login-reset'].submit(); return false;" title="Continue resetting your password">NEXT</a>
                            <a class="link-button tip" id="cancel-reset" role="link" href="index.php" title="Cancel and return to login page">Cancel</a>
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