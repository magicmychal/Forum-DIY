<?php


require_once('PHPMailer.php');
require_once('SMTP.php');
//require_once ('Exception.php');
class mailer{

     public  static  function sent_mail($sent_mail_to, $token , $UserID , $username , $location=0){
         date_default_timezone_set('Etc/UTC');
         //require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';

//Create a new PHPMailer instance
         $mail = new PHPMailer;
//Tell PHPMailer to use SMTP
         $mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
         $mail->SMTPDebug = 0;
//Ask for HTML-friendly debug output
         $mail->Debugoutput = 'html';
//Set the hostname of the mail server
         $mail->Host = 'smtp.gmail.com';
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
         $mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
         $mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
         $mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
         $mail->Username = "mortmusaker@gmail.com";
//Password to use for SMTP authentication
         $mail->Password = "(5m;DbD%Pe";
//Set who the message is to be sent from
         $mail->setFrom('mortmusaker@gmail.com', 'First Last');
//Set an alternative reply-to address
         $mail->addReplyTo('mortmusaker@gmail.com', 'First Last');
//Set who the message is to be sent to
         $mail->addAddress($sent_mail_to, $username);
//Set the subject line
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->isHTML(true);


// output: localhost
$hostName = $_SERVER['HTTP_HOST'];
if($location === 0){
            $mail->Subject = 'Activate user account on Forum-diy';
            $url = "165.22.78.2/verify?token=$token&id=$UserID";
            // Set email format to HTML
            $template= file_get_contents("includes/templates/mail-activate.html");
            $template = str_replace('sUsername', $username, $template);
            $template = str_replace('HOSTURL', $url, $template);
        }else{
            $mail->Subject = 'Login attempts on Forum-diy';
            $url = "165.22.78.2/reactivate?token=$token&id=$UserID";
            $reportURL = "165.22.78.2/report";
            $template= file_get_contents("includes/templates/mail-reactivate.html");
            $template = str_replace('sUsername', $username, $template);
            $template = str_replace('LocationIP', $location, $template);
            $template = str_replace('HOSTURL', $url, $template);
            $template = str_replace('ReportURL', $reportURL, $template);
        }

         $mail->Body    = $template;


         // $mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
//Replace the plain text body with one created manually
         $mail->AltBody = 'This is a plain-text message body';
//Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
         if (!$mail->send()) {
             echo "Mailer Error: " . $mail->ErrorInfo;
         }
     }
}