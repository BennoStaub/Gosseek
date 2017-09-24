<?php
header('Content-Type: text/html; charset=UTF-8');
include("connect_db.php");
?>

<html>
<head>
<title>Gosseek - Achieve your goals</title>
<meta name="author" content="Benno">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body text="#000000" bgcolor="#FFFFFF" link="#FF0000" alink="#FF0000" vlink="#FF0000">

<table style="width:100%" border="3" height="100%">
         <tr align="center">
                 <td align="center" colspan="3">Gosseek</td>
         </tr>
         <tr height="100%">
                 <td width="10%" valign="top"><a href="mainpage.php?action=register">Registrieren</a></td>
                 <td>
                 <?php
                 if(empty($_GET{'action'})){
                         echo "Herzlich Willkommen auf Gosseek. Registriere dich jetzt und beginne damit, deine eigene Story niederzuschreiben oder nutze die Erfahrungen anderer um deine eigenen ähnlichen Ziele zu erreichen.";
                 }else{
                      switch ($_GET['action']) {


                          case 'register':
                          echo "<form action=\"mainpage.php?action=registering\" method=\"post\" accept-charset=\"utf-8\">";
                          echo "<table border=\"1\" bgcolor=\"E5E5E5\" width=\"60%\" align=\"center\">";
                          echo "<tr><td>Vorname: <input name=\"name\" size=\"30\" maxlength=\"30\"> Nachname: <input name=\"surname\" size=\"30\" maxlength=\"30\"></td></tr>";
                          echo "<tr><td>E-Mail: <input name=\"email\" size=\"89\" maxlength=\"100\" type=\"email\"></td></tr>";
                          echo "<tr><td>Passwort: <input name=\"password\" size=\"30\" maxlength=\"30\" type=\"password\"></td></tr>";
                          echo "<tr><td><center><input type=\"submit\" value=\"Anmelden\"></center></td></tr></form></table>";
                          break;


                          case 'registering':
                          if(empty($_POST['name']) OR empty($_POST['surname']) OR empty($_POST['email']) OR empty($_POST['password'])){
                                   echo "<center>Bitte alle Felder ausfüllen.</center>";
                          }else{
                                   $name = mysqli_real_escape_string($mysql_connection, $_POST['name']);
                                   $surname = mysqli_real_escape_string($mysql_connection, $_POST['surname']);
                                   $email = mysqli_real_escape_string($mysql_connection, $_POST['email']);
                                   $password = mysqli_real_escape_string($mysql_connection, $_POST['password']);
                                   $r = mysqli_query($mysql_connection, "SELECT id FROM users WHERE email = '$email' LIMIT 1");
                                   $activation_code = md5(mysqli_real_escape_string($mysql_connection, $_POST['email']));
                                   $password_encrypted = md5($password);
                                   if (mysqli_num_rows($r)){
                                           echo "<center>Die angegebene Email-Adresse befindet sich bereits in unserer Datenbank.</center>";
                                   }else{
                                           $text = "Hallo ".$name." ".$surname.",\r\nvielen Dank für deine Anmeldung bei Gosseek.\r\nHier sind deine Anmeldedaten:\r\nE-Mail: ".$email."\r\nPasswort: ".$password."\r\nUm deinen Account zu aktivieren, bitte klicke auf folgenden Link: http://127.0.0.1/mainpage.php?action=activate_account&code=".$activation_code."\r\nMit freundlichen Grüssen\r\nDas Gosseek Team.";
                                           $header = 'From: benno.staub@hotmail.com' . "\r\n" .
													 'Content-type: text/plain; charset=\"utf-8\"' . "\r\n";
										   mail($email, 'Registrierung bei Gosseek', $text,$header);
                                           mysqli_query($mysql_connection, "INSERT INTO users (activation_code, email,status, password, name, surname, birthdate, timeaction) VALUES ('$activation_code','$email','registered','$password_encrypted', '$name', '$surname', '1994-10-03', '100000')");
                                           echo "<center><br><br>Du erhälst in Kürze eine E-Mail.</center>";

                                   }
                          }
                          break;
						  
						  case 'activate_account':
						  if(!(empty($_GET['code']))) {
							  $activation_code = mysqli_real_escape_string($mysql_connection, $_GET['code']);
							  mysqli_query($mysql_connection, "UPDATE users SET status = 'activated' WHERE activation_code = '$activation_code'");
							  echo "<center>Account erfolgreich aktiviert.</center>";
						  }
						  break;
                      }
                 }
                 ?>
                 </td>


















                 <td width="10%" valign="top">Updates</td>
         </tr>




</table>











</body>
</html>