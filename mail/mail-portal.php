<?php
// PHP Mailer Library
use PHPMailer\PHPMailer\PHPMailer;
require '/var/lib/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
	// Server
	$mail->isSMTP();
	$mail->Host         = 'smtp.gmail.com';
	$mail->SMTPAuth     = true;
	$mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS;
	$mail->Port         = 587;
	$mail->Username     = 'moov.chosenones@gmail.com';
	$mail->Password     = 'qUbcyx-zetqe1-zubdyj';

	// Recipients
	$mail->setFrom('moov.chosenones@gmail.com', 'Moov Portal Admin');
	$mail->addAddress($mail_email, $mail_name);
	$mail->addReplyTo('moov.chosenones@gmail.com', 'Moov Portal Admin');

	// Mail
	$mail->isHTML(true);
	$mail->Subject  = $mail_subject;
	$mail->Body		= '<link rel="stylesheet" type="text/css" href="http://121.200.18.218:8080/moov/portal/assets/style/bootstrap.css"><link rel="stylesheet" type="text/css" href="http://121.200.18.218:8080/moov/portal/assets/style/style.css"><body class="d-flex m-4 p-0"><div class="container mx-auto text-center"><img src="http://121.200.18.218:8080/moov/mail/assets/logo/moov_mail_logo_400x200.png" class="mx-auto">' . $mail_body . '</div></body>';

	$mail->send();

} catch (Exception $e) {
	$$error_message = $mail->ErrorInfo;

}
?>