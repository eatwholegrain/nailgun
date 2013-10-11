<?php
    require("lib/bootstrap.php");

    $userRedirection = false;

    if ($utilities->isPost()) {

        if (!empty($_POST["user-firstname"]) && !empty($_POST["user-lastname"]) && !empty($_POST["user-email"]) && !empty($_POST["user-username"]) && !empty($_POST["user-password"]) && !empty($_POST["account-title"])) {

            $firstname = $utilities->filter($_POST["user-firstname"]);
            $lastname = $utilities->filter($_POST["user-lastname"]);
            $email = $utilities->filter($_POST["user-email"]);
            $username = $utilities->filter($_POST["user-username"]);
            $password = $utilities->filter($_POST["user-password"]);
            $password = $auth->createHash("md5", $password, HASH_PASSWORD_KEY);
            $created = $utilities->getDate();

            $accountTitle = $utilities->filter($_POST["account-title"]);

            $account = $accounts->createAccount($accountTitle, $created, 1);

            if (is_numeric($account)) {

                $user = $users->createUser($firstname, $lastname, $email, $username, $password, $account, $created, 1, 1);
                
                if (is_numeric($user)) {

                    $notice = "Account <strong>".$accountTitle."</strong> successfully created <br><br><hr><br>Account owner <strong>".$firstname." ".$lastname."</strong> successfully added.";

                    $session->set("userid", intval($user));
                    $session->set("firstname", $firstname);
                    $session->set("lastname", $lastname);
                    $session->set("username", $username);
                    $session->set("email", $email);
                    $session->set("account", intval($account));
                    $session->set("role", 1);
                    $session->set("active", 1);
                    $session->set("start", time());
                    $session->set("activity", time());

                    $session->regenerateSessionId();

                    $userRedirection = true;

                    $utilities->redirect("home.php", 3);

                } else {
                    $notice = "Error while creating account owner";
                }

            } else {
                $notice = "Error while creating account";
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
<title><?php echo APPLICATION_TITLE ?> - Create Account</title>

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
<script src="js/jquery.autosize.js"></script>
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

        $("#create-user").click(function(event) {
            event.preventDefault();
            var userFirstname = $("#user-firstname").val();
            var userLastname = $("#user-lastname").val();
            var userEmail = $("#user-email").val();
            var userPassword = $("#user-password").val();
            var accountTitle = $("#account-title").val();

            if(userFirstname == "" || userLastname == "" || !isValidEmailAddress(userEmail) || userPassword == "" || accountTitle == "") {
                $.achtung({message: 'Please enter all required information', timeout: 7});
            } else {
                document['add-account-form'].submit();
            }
        });

        function isValidEmailAddress(emailAddress) {
            var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
            return pattern.test(emailAddress);
        };

        <?php
        $utilities->notify($notice, 7);

        if($userRedirection) {
            $utilities->notify("Redirecting...", 7);
        }
        ?>
    })   
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
            </div>
            <!-- /breadcrumbs -->
            <?php } ?>

            <!-- welcome message -->
            <div id="welcome-message">
                <p>Welcome to <?php echo APPLICATION_TITLE ?> <span class="orange"><?php echo $session->get("firstname"); ?></span></p>
            </div>
            <!-- /welcome message -->

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

                    <form name="add-account-form" action="add-account.php" method="post">
                        
                        <fieldset class="split-fieldset">
                            <label class="large-label">Create a new account</label>

                            <div class="spacer"></div>
                            <h4>Personal Information</h4>
                            <input name="user-firstname" type="text" id="user-firstname" class="text-input rounded" placeholder="First name" required>
                            <script> 
                                var userFirstname = new LiveValidation("user-firstname", {onlyOnSubmit: false, validMessage: "OK" });
                                userFirstname.add(Validate.Presence);
                                userFirstname.add(Validate.Length, {minimum: 3});
                            </script>
                            <input name="user-lastname" type="text" id="user-lastname" class="text-input rounded" placeholder="Last name" required>
                            <script> 
                                var userLastname = new LiveValidation("user-lastname", {onlyOnSubmit: false, validMessage: "OK" });
                                userLastname.add(Validate.Presence);
                                userLastname.add(Validate.Length, {minimum: 3});
                            </script>
                        </fieldset>


                        <fieldset>
                            <input name="user-email" type="text" id="user-email" class="text-input rounded" placeholder="Email address" required>
                            <script> 
                                var userEmail = new LiveValidation("user-email", {onlyOnSubmit: false, validMessage: "OK" });
                                userEmail.add(Validate.Email);
                            </script>
                        </fieldset>

                        <fieldset class="split-fieldset">
                            <input name="user-username" type="text" id="user-username" class="text-input rounded" placeholder="Username" required>
                            <script> 
                                var userUsername = new LiveValidation("user-username", {onlyOnSubmit: false, validMessage: "OK" });
                                userUsername.add(Validate.Presence);
                                userUsername.add(Validate.Length, {minimum: 3});
                            </script>
                            <input name="user-password" type="password" id="user-password" class="text-input rounded" placeholder="Password" required>
                            <script> 
                                var userPassword = new LiveValidation("user-password", {onlyOnSubmit: false, validMessage: "OK" });
                                userPassword.add(Validate.Presence);
                                userPassword.add(Validate.Length, {minimum: 3});
                            </script>
                        </fieldset>

                        <div class="spacer"></div>

                        <div class="spacer"></div>
                        <h4>Company Information</h4>

                        <fieldset>
                            <input name="account-title" type="text" id="account-title" class="text-input rounded" placeholder="Company name" required>
                            <script> 
                                var accountTitle = new LiveValidation("account-title", {onlyOnSubmit: false, validMessage: "OK" });
                                accountTitle.add(Validate.Presence);
                                accountTitle.add(Validate.Length, {minimum: 2});
                            </script>
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <a class="orange-button default-button tip" id="create-user" role="button" href="#" title="Create new account">CREATE</a>
                            <a class="link-button tip" id="cancel-user" role="link" href="index.php" title="Cancel and return">Cancel</a>
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