<?php
// PHP Mailer Library
use PHPMailer\PHPMailer\PHPMailer;
require '/var/lib/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
	// Server
	$mail->isSMTP();
	$mail->Host         = 'smtp.office365.com';
	$mail->SMTPAuth     = true;
	$mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS;
	$mail->Port         = 587;
	$mail->Username     = 'moov.adm@outlook.com';
	$mail->Password     = 'cegbor-9qeqwa-Dirjef';

	// Recipients
	$mail->setFrom('moov.adm@outlook.com', $mail_sender);
	$mail->addAddress($mail_email, $mail_name);
	$mail->addReplyTo('moov.adm@outlook.com', $mail_sender);

	// Mail
	$mail->isHTML(true);
	$mail->Subject  = $mail_subject;
	//$mail->Body     = 'Dear ' . $reset_name . ',<br/><br/>You are receiving this email because we received a password reset request for your account.<br/><br/><a href="' . $reset_url . '">Click to Reset Passowrd</a><br/><br/>This password reset link will expire in 15 minutes.<br/><br/>If you did not request a password reset, no further action is required.<br/><br/>Kind Regards,<br/>Snack Masters<br/><br/><hr/>If you\'re having trouble clicking the Reset Password button, copy and paste the URL below into your web browser: <a href="' . $reset_url . '">' . $reset_url . '</a>';
	$mail->Body		= $mail_body;

	$mail->send();

} catch (Exception $e) {
	echo $mail->ErrorInfo;

}
?>