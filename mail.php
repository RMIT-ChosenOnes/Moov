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
	$mail->setFrom('moov.adm@outlook.com', 'Moov Admin');
	$mail->addAddress($mail_email, $mail_name);
	$mail->addReplyTo('moov.adm@outlook.com', 'Moov Admin');

	// Mail
	$mail->isHTML(true);
	$mail->Subject  = $mail_subject;
	$mail->Body		= $mail_body;

	$mail->send();

} catch (Exception $e) {
	echo $mail->ErrorInfo;

}
?>