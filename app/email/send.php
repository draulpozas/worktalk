<?php
require_once __DIR__."./../../config/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;

function sendEmail($to, $subject, $message){
	$mail = new PHPMailer;

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->SMTPDebug = 0;
	$mail->Username = 'worktalkteam@gmail.com';                 // SMTP username
	$mail->Password = 'worktalk2019';                           // SMTP password
	$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
	$mail->Port = 587;
	$mail->From = 'worktalkteam@gmail.com';
	$mail->FromName = 'WorkTalk Team';
	$mail->addAddress($to, 'WorkTalk');     // Add a recipient
	$mail->addReplyTo('worktalkteam@gmail.com', 'Information');

	$mail->Subject = $subject;
	/*$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';*/

	$mail->MsgHTML($message);

	if(!$mail->send()) {
	    echo 'Message could not be sent.';
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
	    // echo 'Message has been sent. <a href="./">Go back</a>';
	}
}

 ?>