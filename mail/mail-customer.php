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
	$mail->setFrom('moov.adm@outlook.com', 'Moov');
	$mail->addAddress($mail_email, $mail_name);
	$mail->addReplyTo('moov.adm@outlook.com', 'Moov');

	// Mail
	$mail->isHTML(true);
	$mail->Subject  = $mail_subject;
	$mail->Body		= '<link rel="stylesheet" type="text/css" href="http://121.200.18.218:8080/moov/assets/style/bootstrap.css"><link rel="stylesheet" type="text/css" href="http://121.200.18.218:8080/moov/assets/style/style.css"><body class="d-flex m-4 p-0"><div class="container mx-auto text-center"><img src="http://121.200.18.218:8080/moov/mail/assets/logo/moov_mail_logo_400x200.png" class="mx-auto">' . $mail_body . '</div></body>';

	$mail->send();

} catch (Exception $e) {
	echo $mail->ErrorInfo;

}
?>