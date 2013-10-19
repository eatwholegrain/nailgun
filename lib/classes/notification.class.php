<?php
/**
 * Notification
 * @package notification
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2012, Milan Trajkovic
 * @access public
 */

class Notification extends PHPMailer {

    public $mailer;
    public $subject;
    public $message;
    public $receivers;    
    public $sender;
    public $body;
    public $notifyStatus;

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function sendNotification() {
        $mailer = new PHPMailer(true);
        $mailer->IsSMTP();
        $mailer->SMTPAuth = false;
        $mailer->Port = 25;
        $mailer->Host = "localhost";
        $mailer->Username = "nailguna";
        $mailer->Password = "28Nail$";

        $recipients = $this->receivers;

        try {

            foreach ($recipients as &$recipient) {
                if($recipient != $this->sender) {
                    $mailer->AddAddress($recipient);
                    //Utils::log("---".time()."---\n".$recipient."\n");
                }
            }

            $body  = "<html>";
            $body  .= "<body style='font-family: Arial; font-size: 14px; color: #4C4C4C;';>";
            $body  .= "".$this->message."";
            $body  .= "<hr/>";
            $body  .= "<img src='".ROOT."images/logo.png'>";
            $body  .= "</body>";
            $body  .= "</html>";

            $mailer->From = REPLY_EMAIL;
            $mailer->FromName = APPLICATION_TITLE;
            $mailer->AddReplyTo(REPLY_EMAIL, APPLICATION_TITLE);
            $mailer->Subject = $this->subject;
            $mailer->AltBody = "To view the message, please use an HTML compatible email viewer!";
            $mailer->WordWrap = 80;
            $mailer->MsgHTML($body);
            $mailer->IsHTML(true);
            $mailer->Send();
            return true;

        } catch (phpmailerException $e) {
            return $e->errorMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function newProjectNotify($receivers, $pid, $projectTitle, $sender) {
        $this->receivers = $receivers;
        $this->sender = $sender;
        $this->subject = "New Project: ".$projectTitle;
        $this->message = "<p>Project <b>".$projectTitle."</b> is assigned to you.</p><p>View:<a style='color: #16709B; text-decoration: none;' href='".ROOT."project.php?pid=".$pid."'>".$projectTitle."</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function newTaskNotify($receivers, $pid, $tid, $projectTitle, $taskTitle, $taskDescriptions, $dueDate, $sender) {
        $this->receivers = $receivers;
        $this->sender = $sender;
        $this->subject = $projectTitle.":".$taskTitle;
        $this->message = "<p>Task <b>".$taskTitle."</b> is assigned to you.</p><p>Task description:</p> <p>".nl2br($taskDescriptions)."</p><p>Due: <b>".$dueDate."</b></p><p>View: <a style='color: #16709B; text-decoration: none;' href='".ROOT."task.php?pid=".$pid."&tid=".$tid."'>".$taskTitle."</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
    * -.
    * @param string $string String desc.
    * @param number $number Number desc.
    * @param boolean $boolean Boolean desc.
    * @return mixed description
    */
    public function taskUpdateNotify($receivers, $projectTitle, $taskTitle, $taskDescription, $taskAssignee, $pid, $tid, $sender) {
        $this->receivers = $receivers;
        $this->sender = $sender;
        $this->subject = $projectTitle.":".$taskTitle;
        $this->message = "<p>Task: <b>".$taskTitle."</b> is updated by <b>".$taskAssignee."</b> with following text:</p><p>".nl2br($taskDescription)."</p><p>View: <a style='color: #16709B; text-decoration: none;' href='".ROOT."task.php?pid=".$pid."&tid=".$tid."'>".$taskTitle."</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
    * -.
    * @param string $string String desc.
    * @param number $number Number desc.
    * @param boolean $boolean Boolean desc.
    * @return mixed description
    */
    public function taskFileNotify($receivers, $projectTitle, $taskTitle, $uploadedFiles, $taskAssignee, $pid, $tid, $sender) {
        $fileList = "<ul>";
        foreach ($uploadedFiles as &$file) {
            $fileList .= "<li>".ROOT.$file."</li>";
        }
        $fileList .= "</ul>";
        $this->receivers = $receivers;
        $this->sender = $sender;
        $this->subject = $projectTitle.":".$taskTitle;
        $this->message = "<p>Task: <b>".$taskTitle."</b> is updated by <b>".$taskAssignee."</b> with following file(s):</p><p>".$fileList."</p><p>View: <a style='color: #16709B; text-decoration: none;' href='".ROOT."task.php?pid=".$pid."&tid=".$tid."'>".$taskTitle."</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
    * -.
    * @param string $string String desc.
    * @param number $number Number desc.
    * @param boolean $boolean Boolean desc.
    * @return mixed description
    */
    public function taskChangeNotify($receivers, $projectTitle, $taskTitle, $taskAssignee, $pid, $tid, $sender) {
        $this->receivers = $receivers;
        $this->sender = $sender;
        $this->subject = $projectTitle.":".$taskTitle;
        $this->message = "<p>Task: <b>".$taskTitle."</b> is marked as resolved by <b>".$taskAssignee.".</p></b><p>View: <a style='color: #16709B; text-decoration: none;' href='".ROOT."task.php?pid=".$pid."&tid=".$tid."'>".$taskTitle."</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
     * -.
     * @param string $string String desc.
     * @param number $number Number desc.
     * @param boolean $boolean Boolean desc.
     * @return mixed description
     */
    public function newTodoNotify($receivers, $aid, $todoTitle, $todoDescriptions, $dueDate, $sender) {
        $this->receivers = $receivers;
        $this->sender = $sender;
        $this->subject = $todoTitle;
        $this->message = "<p><b>".$todoTitle."</b> is assigned to you.</p><p>Assignment description:</p><p>".nl2br($todoDescriptions)."</p><p>Due: <b>".$dueDate."</b></p><p>View: <a style='color: #16709B; text-decoration: none;' href='".ROOT."todo.php?aid=".$aid."'>".$todoTitle."</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
    * -.
    * @param string $string String desc.
    * @param number $number Number desc.
    * @param boolean $boolean Boolean desc.
    * @return mixed description
    */
    public function todoUpdateNotify($receivers, $aid, $todoTitle, $todoDescription, $todoAssignee, $sender) {
        $this->receivers = $receivers;
        $this->sender = $sender;
        $this->subject = $todoTitle;
        $this->message = "<p><b>".$todoTitle."</b> is updated by <b>".$todoAssignee."</b> with following text: </p><p>".nl2br($todoDescription)."</p><p>View: <a style='color: #16709B; text-decoration: none;' href='".ROOT."todo.php?aid=".$aid."'>".$todoTitle."</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
    * -.
    * @param string $string String desc.
    * @param number $number Number desc.
    * @param boolean $boolean Boolean desc.
    * @return mixed description
    */
    public function todoFileNotify($receivers, $aid, $todoTitle, $uploadedFiles, $todoAssignee, $sender) {
        $fileList = "<ul>";
        foreach ($uploadedFiles as &$file) {
            $fileList .= "<li>".ROOT.$file."</li>";
        }
        $fileList .= "</ul>";
        $this->receivers = $receivers;
        $this->sender = $sender;
        $this->subject = $todoTitle;
        $this->message = "<p><b>".$todoTitle."</b> is updated by <b>".$todoAssignee."</b> with following file(s):</p><p>".$fileList."</p><p>View: <a style='color: #16709B; text-decoration: none;' href='".ROOT."todo.php?aid=".$aid."'>".$todoTitle."</a></p>";   
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
    * -.
    * @param string $string String desc.
    * @param number $number Number desc.
    * @param boolean $boolean Boolean desc.
    * @return mixed description
    */
    public function todoChangeNotify($receivers, $aid, $todoTitle, $todoAssignee, $sender) {
        $this->receivers = $receivers;
        $this->sender = $sender;
        $this->subject = $todoTitle;
        $this->message = "<p><b>".$todoTitle."</b> is marked as resolved by <b>".$todoAssignee.".</p></b><p>View: <a style='color: #16709B; text-decoration: none;' href='".ROOT."todo.php?aid=".$aid."'>".$todoTitle."</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
    * -.
    * @param string $string String desc.
    * @param number $number Number desc.
    * @param boolean $boolean Boolean desc.
    * @return mixed description
    */
    public function newUserNotify($receivers, $name, $username, $password, $sender) {
        $this->receivers = $receivers;
        $this->sender = $sender;
        $this->subject = "Your account for ".APPLICATION_TITLE;
        $this->message = "<p>Hi <b>".$name."</b>,</p><p>New account is created for you.</p><p>Your login information:</p><p><i>username</i>: <b>".$username."</b></p><p><i>password</i>: <b>".$password."</b></p><p>Check your roles for projects.</p><p><a style='color: #16709B; text-decoration: none;' href='".ROOT."'>Click here to login</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
    * -.
    * @param string $string String desc.
    * @param number $number Number desc.
    * @param boolean $boolean Boolean desc.
    * @return mixed description
    */
    public function userUpdateNotify($receivers, $sender) {
        $this->receivers = $receivers;
        $this->sender = $sender;
        $this->subject = "Account update";
        $this->message = "<p>Your account is updated.</p><p>Check your new roles for projects.</p><p><a style='color: #16709B; text-decoration: none;' href='".ROOT."my-account.php'>Click here to view your account</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
    * -.
    * @param string $string String desc.
    * @param number $number Number desc.
    * @param boolean $boolean Boolean desc.
    * @return mixed description
    */
    public function userPasswordReset($receivers, $password) {
        $this->receivers = $receivers;
        $this->sender = REPLY_EMAIL;
        $this->subject = "Password reset";
        $this->message = "<p>Your password is changed successfully.</p><p>New password is: <b>".$password."</b></p><p><a style='color: #16709B; text-decoration: none;' href='".ROOT."my-account.php'>Click here to view your account</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
    * -.
    * @param string $string String desc.
    * @param number $number Number desc.
    * @param boolean $boolean Boolean desc.
    * @return mixed description
    */
    public function sentUsername($receivers, $username) {
        $this->receivers = $receivers;
        $this->sender = REPLY_EMAIL;
        $this->subject = "Your username reminder";
        $this->message = "<p>You recently requested to send your username.</p><p>Your username is: <b>".$username."</b></p><p>Now you can continue reseting your password. From dropdown select <b>I know my username</b> and enter following username: <b>".$username."</b>.</p><p>If you did not request username please ignore this message.</p><p><a style='color: #16709B; text-decoration: none;' href='".ROOT."forgot-login.php'>Click here to continue reseting your password</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }

    /**
    * -.
    * @param string $string String desc.
    * @param number $number Number desc.
    * @param boolean $boolean Boolean desc.
    * @return mixed description
    */
    public function sentNewPassword($receivers, $password) {
        $this->receivers = $receivers;
        $this->sender = REPLY_EMAIL;
        $this->subject = "Your new password";
        $this->message = "<p>You recently requested to reset your password.</p><p>Your new password is: <b>".$password."</b></p><p>You can choose new password from My Account page by clicking on <b>Reset Password</b></p><p>If you did not request username or new password please ignore this message.</p><p><a style='color: #16709B; text-decoration: none;' href='".ROOT."index.php'>Click here to login</a></p>";    
        $this->notifyStatus = $this->sendNotification();
        return $this->notifyStatus;
    }
	
}