<?php
	header('Content-Type: text/html; charset=UTF-8');
	include("connect_db.php");
?>

<html>
	<head>
		<title>Gosseek - Achieve your goals</title>
		<meta name="author" content="Benno">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="styles/stylesheet.css" />
	</head>
	<body>
		<div class="boxmain">
			<div class="boxright">
				<div class="rightinnerboxtop">
					<br><center>
					<b>Login:</b><br><br></center>
					<form action="login.php?action=login" method="post" >
					E-Mail:
					<input name="email" size="12" maxlength="40">
					<br><br>Passwort:
					<input type="password" name="password" size="12">
					<br><br>
					<input type="submit" value="Einloggen"></input>
					</form>
					<br>
					<a href="mainpage.php?action=forgotpw">Passwort vergessen</a>
				</div>
				<div class="rightinnerboxbottom">
					bbbbb
				</div>
			</div>
			<div class="boxtop">
				Hallo
			</div>
			<div class="boxfeed">
				<p>
					<?php
						if(empty($_GET{'action'})){
							 echo "Herzlich Willkommen auf Gosseek. Registriere dich jetzt und beginne damit, deine eigene Story niederzuschreiben oder nutze die Erfahrungen anderer um deine eigenen ähnlichen Ziele zu erreichen.";
						}else{
							switch($_GET['action']) {
								
								case 'registering':
								if(empty($_POST['name']) OR empty($_POST['surname']) OR empty($_POST['email']) OR empty($_POST['password'])){
									echo "<center>Bitte alle Felder ausfüllen.</center>";
								}else{
									$name = mysqli_real_escape_string($mysql_connection, $_POST['name']);
									$surname = mysqli_real_escape_string($mysql_connection, $_POST['surname']);
									$email = mysqli_real_escape_string($mysql_connection, $_POST['email']);
									$password = mysqli_real_escape_string($mysql_connection, $_POST['password']);
									$check_email_query = mysqli_query($mysql_connection, "SELECT id FROM users WHERE email = '$email' LIMIT 1");
									if (mysqli_num_rows($check_email_query)){
										echo "<center>Die angegebene Email-Adresse befindet sich bereits in unserer Datenbank.</center>";
									}else{
										$activation_code = md5(mysqli_real_escape_string($mysql_connection, $_POST['email']));
										$password_encrypted = md5($password);
										$text = "Hallo ".$name." ".$surname.",\r\nvielen Dank für deine Anmeldung bei Gosseek.\r\nHier sind deine Anmeldedaten:\r\nE-Mail: ".$email."\r\nPasswort: ".$password."\r\nUm deinen Account zu aktivieren, bitte klicke auf folgenden Link: http://127.0.0.1/mainpage.php?action=activate_account&code=".$activation_code."\r\nMit freundlichen Grüssen\r\nDas Gosseek Team.";
										$header = 'From: benno.staub@hotmail.com' . "\r\n" .
												 'Content-type: text/plain; charset=\"utf-8\"' . "\r\n";
										mail($email, 'Registrierung bei Gosseek', $text, $header);
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
								
								case 'forgotpw':
								echo "Wenn du dein Passwort vergessen hast, gib bitte deine E-Mail ein. Wir schicken dir dann eine E-Mail mit deinem neuen Passwort.";
								echo "<center><form action=\"mainpage.php?action=sendnewpw\" method=\"post\">";
								echo "E-Mail <input name=\"email\" size=\"18\"></input>";
								echo "<input type=\"submit\" value=\"E-Mail versenden\"></input></form></center>";
								break;
								
								case 'sendnewpw':
								$email = mysqli_real_escape_string($mysql_connection, $_POST['email']);
								$userdata_query = mysqli_query($mysql_connection, "SELECT id,name,surname FROM users WHERE email = '$email' LIMIT 1");
								if(mysqli_num_rows($userdata_query)){
										 $new_password = mt_rand(123456,999999);
										 $new_password_encrypted = md5($new_password);
										 $userdata = mysqli_fetch_array($userdata_query);
										 $text = "Hallo ".$userdata['name']." ".$userdata['surname'].",\r\nDu hast dein Passwort vergessen, daher schicken wir dir mit dieser E-Mail dein neues Passwort.\r\n\r\nDein neues Passwort lautet: ".$new_password."\r\n\r\nDu solltest dein Passwort nach dem ersten Login ändern, nicht dass du es wieder vergisst.\r\n\r\n\r\nMit freundlichen Grüssen,\r\nDas Gosseek-Team";
										 $header = 'From: benno.staub@hotmail.com' . "\r\n" .
												 'Content-type: text/plain; charset=\"utf-8\"' . "\r\n";
										 mail($email, 'Passwort vergessen bei Gosseek', $text, $header);
										 mysqli_query($mysql_connection, "UPDATE users SET password = '$new_password_encrypted' WHERE id = ".$userdata['id']."");
										 echo "Du erhälst in Kürze eine E-Mail.";
								}else{
										 echo "Die angegebene E-Mail befindet sich nicht in unserer Datenbank.";
								}
								break;
								
								case 'failedlogin':
									switch($_GET['reason']) {
										
										case 'wrongmail':
										echo "Die angegebene E-Mail befindet sich nicht in unserer Datenbank.";
										break;
										
										case 'wrongpassword':
										echo "Falsches Passwort.";
										break;
										
										case 'notactivated':
										echo "Dein Account wurde noch nicht aktiviert.";
										break;
									}								
								break;
							}
						}
					?>
				</p>
			</div>
			<div class="boxleft">
				<?php
					echo "<div class=\"leftinnerboxtop\">Registriere dich hier: <br><br><form action=\"mainpage.php?action=registering\" method=\"post\" accept-charset=\"utf-8\">";
                    echo "Vorname: <br><input name=\"name\" size=\"30\" maxlength=\"30\"><br>";
					echo "Nachname: <br><input name=\"surname\" size=\"30\" maxlength=\"30\"><br>";
					echo "E-Mail: <br><input name=\"email\" size=\"30\" maxlength=\"100\" type=\"email\"><br>";
					echo "Passwort: <br><input name=\"password\" size=\"30\" maxlength=\"30\" type=\"password\"><br>";
					echo "<center><input type=\"submit\" value=\"Anmelden\"></center></form></div>";
				?>
			</div>
		</div>
	</body>
</html>