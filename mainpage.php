<?php
header('Content-Type: text/html; charset=ISO-8859-1');
include("connect_db.php");
?>

<html>
<head>
<title>Gosseek - Achieve your goals</title>
<meta name="author" content="Benno">
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
                         echo "Herzlich Willkommen auf Gosseek. Registriere dich jetzt und beginne damit, deine eigene Story niederzuschreiben oder nutze die Erfahrungen anderer um deine eigenen &auml;hnlichen Ziele zu erreichen.";
                 }else{
                      switch ($_GET['action']) {


                          case 'register':
                          echo "<form action=\"mainpage.php?action=registering\" method=\"post\">";
                          echo "<table border=\"1\" bgcolor=\"E5E5E5\" width=\"60%\" align=\"center\">";
                          echo "<tr><td>Vorname: <input name=\"name\" size=\"30\" maxlength=\"30\"> Nachname: <input name=\"surname\" size=\"30\" maxlength=\"30\"></td></tr>";
                          echo "<tr><td>E-Mail: <input name=\"email\" size=\"89\" maxlength=\"100\"></td></tr>";
                          echo "<tr><td>Passwort: <input name=\"password\" size=\"30\" maxlength=\"30\"></td></tr>";
                          echo "<tr><td><center><input type=\"submit\" value=\"Anmelden\"></center></td></tr></form></table>";
                          break;


                          case 'registering':
                          if(empty($_POST['name']) OR empty($_POST['surname']) OR empty($_POST['email']) OR empty($_POST['password'])){
                                   echo "<center>Bitte alle Felder ausf&uuml;llen.</center>";
                          }else{
                                   $name = mysqli_real_escape_string($connection, $_POST['name']);
                                   $surname = mysqli_real_escape_string($connection, $_POST['surname']);
                                   $email = mysqli_real_escape_string($connection, $_POST['email']);
                                   $password = mysqli_real_escape_string($connection, $_POST['password']);
                                   $r = mysqli_query($connection, "SELECT id FROM users WHERE email = '$email' LIMIT 1");
                                   $activation_code = md5(mysqli_real_escape_string($connection, $_POST['email']));
                                   $password_encrypted = md5($password);
                                   if (mysqli_num_rows($r)){
                                           echo "<center>Die angegebene Email-Adresse befindet sich bereits in unserer Datenbank.</center>";
                                   }else{
                                           $text = "Hallo ".$name." ".$surname.",
vielen Dank für deine Anmeldung bei Gosseek.
Hier sind deine Anmeldedaten:
E-Mail: ".$email."
Passwort: ".$password."
Um deinen Account zu aktivieren, bitte klicke auf folgenden Link: http://127.0.0.1/mainpage.php?action=activate_account&code=".$activation_code."
Mit freundlichen Grüssen
Das Gosseek Team.";
                                           mail($email, 'Registrierung bei Gosseek', $text,"from:benno.staub@hotmail.com");
                                           mysqli_query($connection, "INSERT INTO users (email,status, password, name, surname, birthdate, timeaction) VALUES ('$email','registered','$password_encrypted', '$name', '$surname', '1994-10-03', '100000')");
                                           echo "<center><br><br>Du erh&auml;lst in Kürze eine E-Mail.</center>";

                                   }
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