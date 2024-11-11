<?php  
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

session_start();

if (isset($_SESSION['username']) && isset($_SESSION['email'])) {
    $username = $_SESSION['username'];
    $recipientEmail = $_SESSION['email'];

 
    $con = mysqli_connect("localhost", "root", "1234", "projectphp");

    if (!$con) {
        die("Failed to connect to MySQL: " . mysqli_connect_error());
    }

   
    $randomPassword = bin2hex(random_bytes(8)); 

    $updateQuery = "UPDATE users SET randomPassword = '$randomPassword' WHERE username = '$username'";
    mysqli_query($con, $updateQuery);

    
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ayasabea17@gmail.com'; 
        $mail->Password = 'yfxg kwmb rlgz purn'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('ayasabea17@gmail.com', 'Password Reset');
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "Click the link to reset your password: 
                       <a href='http://localhost/full_project/resetPassword.php?username=$username&token=$randomPassword'>Reset Password</a>";
        $mail->send();
        echo "<script>
        alert('Password reset email sent.'); 
        window.location.href = 'login.php'; // Redirect to the login page
      </script>";    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    mysqli_close($con);
}
?>
