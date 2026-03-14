<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;
require_once 'vendor/autoload.php';

class Mailers {
    function sendMail($receiver,$receivers_name){
        $mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = 'smtpout.secureserver.net';
		$mail->Port = 465;
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

		$mail->SMTPAuth = true;
		$mail->AuthType = 'XOAUTH2';

		$email = 'aldrinlobis1@gmail.com'; // the email used to register google app
		$clientId = '957746875312-ggvgsvovj8gm6ut1ktfiqnhkqf0navgq.apps.googleusercontent.com';
		$clientSecret = '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';
		$refreshToken ='1//0gZlha7mrCVPvCgYIARAAGBASNwF-L9IrP4YCEut7LDML8pr5gT34wfWpxAhuyzWFHUYiH-jcH91IHGYVbIaDnoQ3PlbkhNoHvP4';
		//Create a new OAuth2 provider instance
		$provider = new Google(
			[
				'clientId' => $clientId,
				'clientSecret' => $clientSecret,
			]
		);

		//Pass the OAuth provider instance to PHPMailer
		$mail->setOAuth(
			new OAuth(
				[
					'provider' => $provider,
					'clientId' => $clientId,
					'clientSecret' => $clientSecret,
					'refreshToken' => $refreshToken,
					'userName' => $email,
				]
			)
		);
		$content="<p>Dear Aldrin,</p>

		<p>We hope this email finds you well. At Technos, we strive to provide valuable content and updates that matter to you.</p>

		<h2>In this week's newsletter:</h2>
		<ol>
			<li><strong>Exclusive Tips:</strong> Discover expert tips on improving productivity.</li>
			<li><strong>Featured Article:</strong> Our latest article on [relevant topic].</li>
			<li><strong>Upcoming Events:</strong> Don't miss out on our upcoming webinars or events.</li>
		</ol>

		<p>[Optional Personalized Content or Recommendations]</p>

		<p>We value your time and privacy. You can manage your email preferences or unsubscribe at any time using the links below.</p>

		<p>Thank you for being a part of our community!</p>

		<p>Best Regards,<br>
			Aldrin<br>
			Junior Web<br>
			091253648372
		</p>";
		$mail->setFrom($email, 'Technos Systems');
		$mail->addAddress($receiver, $receivers_name);
		$mail->isHTML(true);
		$mail->Subject = 'Your Weekly Newsletter from Technos';
		$mail->Body = $content;

		//send the message, check for errors
		if (!$mail->send()) {
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			echo 'Message sent!';
		}
    }
}