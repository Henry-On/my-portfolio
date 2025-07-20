<?php	

if($_SERVER['REQUEST_METHOD'] !== "POST") print_r(json_encode(["message"=>"Invalid request method"]));

USE PHPMailer\PHPMailer\PHPMailer;
USE PHPMailer\PHPMailer\Exception;

require "libraries\PHPMailer/src/PHPMailer.php";
require "libraries\PHPMailer/src/Exception.php";
require "libraries\PHPMailer/src/SMTP.php";
require "form-validation.php";

$mail = new form_validation();
$mail->validate_name($_POST['name'], [
    'required'=>true,
    'error'=>'name'
]);

$mail->validate_email($_POST['email'], [
    'required'=>true,
    'error'=>'email'
]);

$mail->validate_string($_POST['subject'], [
    'required'=>true,
    'error'=>'subject'
]);

$mail->validate_string($_POST['message-body'], [
    'required'=>true,
    'error'=>'body',
    'min_length'=>12
]);

if(count($mail->form_errors) > 0) {
    print_r(json_encode(['errors'=>$mail->form_errors]));
    exit();
}

    
$send_mail = new PHPMailer(true);

$send_mail -> isSMTP();
$send_mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

$send_mail -> Host = "smtp.gmail.com";
$send_mail -> SMTPAuth = true;
$send_mail -> Username = "orionu360@gmail.com";
$send_mail -> Password = "lkablhbhjaqndqdz";
$send_mail -> SMTPSecure = "ssl";
$send_mail -> Port = 465;

$send_mail -> setFrom("orionu360@gmail.com", "[WebForm] {$_POST['name']}", 0);
$send_mail -> addAddress("orionu360@gmail.com","henryorionu@gmail.com");
$send_mail -> AddReplyTo($_POST['email']);
$send_mail -> isHTML(true);

$send_mail -> Subject = "[Portfolio] WebForm Message";

//get full email body content
$body = file_get_contents("user-email.php");
$body = str_replace("__email_subject", "{$_POST['subject']}", $body);
$body = str_replace("user_email_address", "{$_POST['email']}", $body);
$body = str_replace("__email_text_content", "{$_POST['message-body']}", $body);
$send_mail -> Body = $body;

$success = false;

try {

    if($send_mail -> send()) $success = true;

}
catch(Exception $e) {
    $mail->set_error("body", "Message not sent. Please try again...");
    $err = "[Generated]=>".date("d:m:Y h:i:s")." [Name]=>".get_class($e)." [Message]=>{$e->getMessage()} [file]=>{$e->getFile()} [line]=>{$e->getLine()}\n";
    error_log($err, 3, "php_errors.log");
}
finally {

    if(count($mail->form_errors) < 1) {
        print_r(json_encode(['success' => $success]));
        return;
    };

    print_r(json_encode([
        'errors' => $mail->form_errors,
        'success' => $success
    ]));
}    
