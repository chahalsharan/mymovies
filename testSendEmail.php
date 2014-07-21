<?php
require_once 'swift/lib/swift_required.php';
require_once 'globals.php';

$emailTitle = "Account confirmation email";
$vars = array(
  '{$emailTitle}' => "Account confirmation email",
  '{$siteUrl}'    => $SITE_URL,
  '{$confLink}'   => "confirmAccount.php?userId=1234&confirmationCode=sdfdsfdsafds223"
);

$message = file_get_contents('email_lite2.php');
foreach ($vars as $key => $value) {
    error_log("DEBUG: replacging :" . $key . " with value: " . $value);
    $message = str_replace($key, $value, $message);  
}

echo $message;

$header = "From: noreply-mymovies@gmail.com\r\n"; 
$header .= "Reply-to: noreply-mymovies@gmail.com\r\n"; 


$transport = Swift_SmtpTransport::newInstance('ssl://smtp.gmail.com', 465)
  ->setUsername('webspheresolutions@gmail.com')
  ->setPassword('DiscoSingh');

$mailer = Swift_Mailer::newInstance($transport);

$message = Swift_Message::newInstance($emailTitle)
  ->setFrom(array('webspheresolutions@gmail.com' => 'MyMovies'))
  ->setTo(array('chahalsharan@gmail.com'))
  ->setBody("This is account confirmation email")
  ->addPart($message, 'text/html');

// Send the message
if ($mailer->send($message)) {
    echo 'Mail sent successfully.';
} else {
    echo 'I am sure, your configuration are not correct. :(';
}

?>