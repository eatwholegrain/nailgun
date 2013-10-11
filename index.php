<?php
    require("lib/bootstrap.php");

    if ($utilities->isGet()) {

        if (!empty($_GET["action"]) && $_GET["action"] == "logout") {
            $session->destroy();
        }

        if (!empty($_GET["redirection"])) {
            $session->set("redirection", $utilities->replace($_GET["redirection"], "|", "&"));
        }

        if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {
            $utilities->redirect("home.php");
        } else {
            $notice = "<strong>Welcome to ".APPLICATION_TITLE."</strong>. Enter your username and password to start<br><br><hr><br>If you are <strong>new user</strong> you can register your account by clicking on Register button";
        }
    }

    if ($utilities->isPost()) {

        if (!empty($_POST["username"]) && !empty($_POST["password"])) {

            $username = $utilities->filter($_POST["username"]);
            $password = $utilities->filter($_POST["password"]);

            $password = $auth->createHash("md5", $password, HASH_PASSWORD_KEY);

            $users = $auth->login($username, $password);

            if (intval($users[0]["id"]) > 0) {

                $notice = "Welcome to ".APPLICATION_TITLE." <strong>".$users[0]["firstname"]."</strong>";

                $session->set("userid", intval($users[0]["id"]));
                $session->set("firstname", $users[0]["firstname"]);
                $session->set("lastname", $users[0]["lastname"]);
                $session->set("username", $users[0]["username"]);
                $session->set("email", $users[0]["email"]);
                $session->set("account", intval($users[0]["account"]));
                $session->set("role", intval($users[0]["role"]));
                $session->set("active", 1);
                $session->set("start", time());
                $session->set("activity", time());

                $session->regenerateSessionId();

                if (isset($_SESSION["redirection"]) && !empty($_SESSION["redirection"]) && $session->get("redirection") != "") {
                    $utilities->redirect($session->get("redirection"));
                } else {
                    $utilities->redirect("home.php");
                }

            } else {

                $notice = "Wrong username or password";
            }
    
        } else {

            $notice = "Enter your username and password";
        }    
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo APPLICATION_TITLE ?></title>

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
            <!-- breadcrumbs -->
            <div class="breadcrumbs">
                <a class="tip" href="#" title="Login"><img src="images/login.png"></a>
            </div>
            <!-- /breadcrumbs -->

            <!-- welcome message -->
            <div id="welcome-message">
                <p>Welcome to <?php echo APPLICATION_TITLE ?></p>
            </div>
            <!-- /welcome message -->

            <!-- top panel -->
            <div id="top-panel">

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

                    <form name="login" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                    
                        <fieldset class="split-fieldset">
                            <?php
                            if (!$auth->isLogedIn()) {
                            ?>
                            <label class="large-label">Login</label>
                            <input name="username" type="text" id="username" class="text-input rounded" placeholder="Username" tabindex="1" required autofocus>
                            <input name="password" type="password" id="password" class="text-input rounded" placeholder="Password" tabindex="2" required>
                            <div class="spacer"></div>
                            <a class="orange-button default-button tip" id="login" role="button" href="#" title="Login to <?php echo APPLICATION_TITLE ?> application" onClick="document['login'].submit(); return false;">LOGIN</a>
                            <a class="link-button tip" id="forgot" role="link" href="forgot-login.php" title="If you forgot your username or password click here">Forgot password?</a>
                            <input  type="submit" name="update" value=" " style="position: absolute; height: 0px; width: 0px; border: none; padding: 0px;" hidefocus="true" tabindex="-1"/>
                            <?php } else { ?>
                            <label class="large-label">Logout</label>
                            <a class="orange-button default-button tip" id="logout" role="button" href="index.php?action=logout" title="Logout">LOGOUT</a>
                            
                            <?php } ?>
                        </fieldset>

                        <?php
                        if(!$auth->isLogedIn()) {
                        ?>
                        <fieldset class="mt20">
                            <label class="large-label">Or</label>
                            <a class="orange-button default-button tip" id="register" role="button" href="add-account.php" title="Register your account at <?php echo APPLICATION_TITLE ?>">REGISTER</a>
                        </fieldset>
                        <?php } ?>

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