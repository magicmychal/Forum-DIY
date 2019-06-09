<?php


require_once('PHPMailer.php');
require_once('SMTP.php');
//require_once ('Exception.php');
class mailer{

     public  static  function sent_mail($sent_mail_to, $token , $UserID , $username){
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
         $mail->Subject = 'Activate user account on Forum-diy';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
         $mail->isHTML(true);


         // output: localhost
         $hostName = $_SERVER['HTTP_HOST'];

         $url = "165.22.78.2/verify?token=$token&id=$UserID";
         // Set email format to HTML
         $template= ' <!DOCTYPE html>
      <html lang="en">
        <head>
          <title>Making Accessible Emails</title>
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <meta http-equiv="X-UA-Compatible" content="IE=edge" />
          <style type="text/css">
              /* CLIENT-SPECIFIC STYLES */
              body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
              table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
              img { -ms-interpolation-mode: bicubic; }

              /* RESET STYLES */
              img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
              table { border-collapse: collapse !important; }
              body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
          </style>
        </head>
        <body style="background-color:#00897B; margin: 0 !important; padding: 60px 0 60px 0 !important;">
          <table border="0" cellspacing="0" cellpadding="0" role="presentation" width="100%">
            <tr>
                <td bgcolor="#00897B" style="font-size: 0;">&​nbsp;</td>
                <td bgcolor="white" width="600" style="border-radius: 4px; color: grey; font-family: sans-serif; font-size: 18px; line-height: 28px; padding: 40px 40px;">
                  <h1 style="color: black; font-size: 32px; font-weight: bold; line-height: 36px; margin: 0 0 30px 0;">Hi '.$username.' thank you for registering at Forum.diy.</h1>
                  <p style="margin: 0 0 30px 0;"> <em style="color: black;"> You can activate your account at <a role="button" href='.$url.' style="border-radius: 5px; text-decoration: none; background-color: #2c3e50; color:white; padding: .4em;">THIS</a> link </em> </p>
                </td>
                <td bgcolor="#00897B" style="font-size: 0;">&​nbsp;</td>
            </tr>
          </table>
        </body>
      </html>
';
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