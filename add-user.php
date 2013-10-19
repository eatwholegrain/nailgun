<?php
    require("lib/bootstrap.php");

    if ($auth->isLogedIn() && $users->isUser($session->get("userid"))) {

        if ($users->isOwner($session->get("userid"))) {

            $allProjects = $projects->listAllProjects($session->get("account"));

            $userRedirection = false;

            if ($utilities->isPost()) {

                if (!empty($_POST["user-firstname"]) && !empty($_POST["user-lastname"]) && !empty($_POST["user-email"]) && !empty($_POST["user-username"]) && !empty($_POST["user-password"]) && !empty($_POST["radio"])) {

                    $firstname = $utilities->filter($_POST["user-firstname"]);
                    $lastname = $utilities->filter($_POST["user-lastname"]);
                    $email = $utilities->filter($_POST["user-email"]);
                    $username = $utilities->filter($_POST["user-username"]);
                    $password = $utilities->filter($_POST["user-password"]);
                    $password = $auth->createHash("md5", $password, HASH_PASSWORD_KEY);
                    $role = $utilities->filter($_POST["radio"]);
                    $created = $utilities->getDate();

                    $user = $users->createUser($firstname, $lastname, $email, $username, $password, $session->get("account"), $created, $role, 1);
                    
                    if (is_numeric($user)) {

                        for ($i=0; $i < count($allProjects); $i++) {

                            if(!empty($_POST["radio-".$allProjects[$i]["id"]])) {

                                $role = $roles->createRole($allProjects[$i]["id"], $user, $_POST["radio-".$allProjects[$i]["id"]]);

                            }
                        }

                        $notifications->newUserNotify(array($email), $firstname, $username, $_POST["user-password"], $users->getUserEmail($session->get("userid")));

                        $notice = "User <strong>".$firstname." ".$lastname."</strong> successfully created.";

                        $userRedirection = true;

                        $utilities->redirect("all-users.php", 3);

                    } else {
                        $notice = "Error while creating user";
                    }

                } else {
                    $notice = "Enter all required information";
                }
                
            }

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
<title><?php echo APPLICATION_TITLE ?> - Add User</title>

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
            var role = $("#radio :radio:checked").val();

            if(userFirstname == "" || userLastname == "" || !isValidEmailAddress(userEmail) || userPassword == "" || role == undefined) {
                $.achtung({message: 'Please enter all required information', timeout: 7});
            } else {
                document['add-user-form'].submit();
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
                <a class="separator"><img src="images/separator.png"></a>
                <?php 
                if ($users->isAdmin($session->get("userid"))) {
                ?>
                <a class="tip" href="all-users.php" title="Users"><img src="images/all-users.png"></a>
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
                <article id="add-user">

                    <form name="add-user-form" action="add-user.php" method="post">
                        
                        <fieldset class="split-fieldset">
                            <label class="large-label">Create a new user</label>
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

                        <fieldset>
                            <label class="small-label">Role</label>
                            <div id="radio">
                                <label for="radio1">OWNER</label>
                                <input type="radio" id="radio1" value="1" name="radio">
                                <label for="radio2">ADMIN</label>
                                <input type="radio" id="radio2" value="2" name="radio">
                                <label for="radio3">USER</label>
                                <input type="radio" id="radio3" value="3" name="radio">
                            </div>
                            <input name="user-role" type="text" id="user-role" class="text-input rounded" value="" style="float: right;">         
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <div class="check-table">
                                <!-- user role header -->
                                <div class="check-table-header">
                                    <div class="check-table-col1">
                                        <p>Add to projects as</p>
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

                                <div class="check-table-row">
                                    <div class="check-table-col1">
                                        <p><?php echo $allProjects[$i]["title"]; ?></p>
                                    </div>
                                    <div class="check-table-col2">
                                        <p><input type="radio" id="project-user-<?php echo $i; ?>" value="3" name="radio-<?php echo $allProjects[$i]["id"]; ?>"></p>
                                    </div>
                                    <div class="check-table-col2">
                                        <p><input type="radio" id="project-user-<?php echo $i; ?>" value="2" name="radio-<?php echo $allProjects[$i]["id"]; ?>"></p>
                                    </div>
                                    <div class="check-table-col2">
                                        <p><input type="radio" id="project-manager-<?php echo $i; ?>" value="1" name="radio-<?php echo $allProjects[$i]["id"]; ?>"></input></p>
                                    </div>
                                    <div class="check-table-col4">
                                        <a class="remove-update tip" title="Remove this role" href="#" onClick="$('#project-user-<?php echo $i; ?>, #project-manager-<?php echo $i; ?>').prop('checked', false); return false;"><img src="images/delete.png"></a>
                                    </div>
                                </div>

                                <?php } ?>
                                
                            </div>
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset>
                            <a class="orange-button default-button tip" id="create-user" role="button" href="#" title="Create new user">CREATE</a>
                            <a class="link-button tip" id="cancel-user" role="link" href="all-users.php" title="Cancel and return">Cancel</a>
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