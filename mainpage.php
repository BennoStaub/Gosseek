<?php
	header('Content-Type: text/html; charset=UTF-8');
	include("connect_db.php");
	if(empty($_GET{'action'}))
	{
		$_GET['action'] = "welcome";
	}
	if(!(isset($_GET['language'])) OR empty($_GET['language']))
	{
		$_GET['language'] = "german";
	}
?>
<html>
	<head>
		<title>
			<?php
				switch($_GET['language'])
				{
					case 'german':
					$output_slogan = "Gosseek - Achieve your goals";
					break;
					
					case 'english':
					$output_slogan = "Gosseek - Achieve your goals";
					break;
				}
				echo $output_slogan;
			?>
		</title>
		<meta name="author" content="Benno Staub">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="styles/stylesheet.css" />
	</head>
	<body>
		<div class="boxmain">
			<div class="boxright">
				<div class="rightinnerboxtop">
					<br>
					<center>
						<b>
							<?php
								switch($_GET['language'])
								{
									case 'german':
									$output_login = "Login:";
									$output_email = "E-Mail:";
									$output_password = "Passwort:";
									$input_submit = "Einloggen";
									$link_forgotpassword = "Passwort vergessen";
									break;
									
									case 'english':
									$output_login = "Login:";
									$output_email = "E-mail:";
									$output_password = "Password:";
									$input_submit = "Log in";
									$link_forgotpassword = "Forgot password";
									break;
								}
								echo $output_login;
							?>
						</b>
						<br>
						<br>
					</center>
					<?php
						echo "<form action=\"login.php?language=".$_GET['language']."&action=login\" method=\"post\" >";
							echo $output_email;
							echo "<input name=\"email\" size=\"12\" maxlength=\"40\"></input>";
							echo "<br>";
							echo "<br>";
							echo $output_password;
							echo "<input type=\"password\" name=\"password\" size=\"12\"></input>";
							echo "<br>";
							echo "<br>";
							echo "<input type=\"submit\" value=\"".$input_submit."\"></input>";
						echo "</form>";
						echo "<br>";
						echo "<p>";
							echo "<a href=\"mainpage.php?language=".$_GET['language']."&action=forgotpassword\">".$link_forgotpassword."</a>";
						echo "</p>";
					?>
				</div>
				<div class="rightinnerboxbottom">
				</div>
			</div>
			<div class="boxtop">
				<div class="boxtopinner">
					<?php
						echo "<a href=\"mainpage.php?language=german&action=welcome\">Ger</a>";
						echo "<a href=\"mainpage.php?language=english&action=welcome\">En</a>";
						echo "<p>".$output_slogan."</p>";
					?>
				</div>
			</div>
			<div class="boxfeed">
				<p>
					<?php
						switch($_GET['action'])
						{
							case 'welcome':
							switch($_GET['language'])
							{
								case 'german':
								$output = "Herzlich Willkommen auf Gosseek. Registriere dich jetzt und beginne damit, deine eigene Erfolgsgeschichte niederzuschreiben oder nutze die Erfahrungen anderer, um deine eigenen, ähnlichen Ziele zu erreichen.";
								break;
								
								case 'english':
								$output = "Welcome on Gosseek. Register now and start writing your own success story or use the experiences from others to achieve your own similar goals.";
								break;
							}
							echo $output;
							break;
							
							case 'registering':
							switch($_GET['language'])
							{
								case 'german':
								$output_noinput = "Bitte alle Eingabefelder ausfüllen.";
								$output_wrongmail = "Die angegebene Email-Adresse befindet sich bereits in unserer Datenbank.";
								$output_done = "Du erhälst in Kürze eine E-Mail.";
								break;
								
								case 'english':
								$output_noinput = "Please fill in all input fields.";
								$output_wrongmail = "There is already an entry in our database for this e-mail.";
								$output_done = "You will receive an e-mail from us shortly.";
								break;
							}
							if(empty($_POST['name']) OR empty($_POST['surname']) OR empty($_POST['email']) OR empty($_POST['password']))
							{
								echo $output_noinput;
							}else
							{
								$name = mysqli_real_escape_string($mysql_connection, $_POST['name']);
								$surname = mysqli_real_escape_string($mysql_connection, $_POST['surname']);
								$email = mysqli_real_escape_string($mysql_connection, $_POST['email']);
								$password = mysqli_real_escape_string($mysql_connection, $_POST['password']);
								$check_email_query = mysqli_query($mysql_connection, "SELECT id FROM users WHERE email = '$email' LIMIT 1");
								if (mysqli_num_rows($check_email_query))
								{
									echo $output_wrongmail;
								}else
								{
									$activation_code = md5(mysqli_real_escape_string($mysql_connection, $_POST['email']));
									$password_encrypted = md5($password);
									switch($_GET['language'])
									{
										case 'german':
										$text = "Hallo ".$name." ".$surname.",\r\n\r\nVielen Dank für deine Anmeldung bei Gosseek.\r\nHier sind deine Anmeldedaten:\r\nE-Mail: ".$email."\r\nPasswort: ".$password."\r\n\r\nUm deinen Account zu aktivieren, bitte klicke auf folgenden Link: http://127.0.0.1/mainpage.php?language=".$_GET['language']."&action=activate_account&code=".$activation_code."\r\n\r\nMit freundlichen Grüssen\r\nDas Gosseek Team.";
										$subject = "Registrierung bei Gosseek";
										break;
										
										case 'english':
										$text = "Dear ".$name." ".$surname.",\r\n\r\nThank you for your registration on Gosseek.\r\nHere are your credentials:\r\nE-mail: ".$email."\r\nPassword: ".$password."\r\n\r\nTo activate your account, please click on the following link: http://127.0.0.1/mainpage.php?language=".$_GET['language']."&action=activate_account&code=".$activation_code."\r\n\r\nBest regards\r\nThe Gosseek Team.";
										$subject = "Registration on Gosseek";
										break;
									}
									$header = "From: benno.staub@hotmail.com" . "\r\n" .
											 "Content-type: text/plain; charset=\"utf-8\"" . "\r\n";
									mail($email, $subject, $text, $header);
									mysqli_query($mysql_connection, "INSERT INTO users (activation_code, email, status, password, name, surname) VALUES ('$activation_code','$email','registered','$password_encrypted', '$name', '$surname')");
									echo $output_done;
								}
							}
							break;
						  
							case 'activate_account':
							if(!(empty($_GET['code'])))
							{
								$activation_code = mysqli_real_escape_string($mysql_connection, $_GET['code']);
								mysqli_query($mysql_connection, "UPDATE users SET status = 'activated' WHERE activation_code = '$activation_code'");
								switch($_GET['language'])
								{
									case 'german':
									$output = "Account erfolgreich aktiviert.";
									break;
									
									case 'english':
									$output = "Account successfully activated.";
									break;
								}
								echo $output;
							}
							break;
							
							case 'forgotpassword':
							switch($_GET['language'])
							{
								case 'german':
								$output = "Wenn du dein Passwort vergessen hast, gib bitte deine E-Mail ein. Wir schicken dir dann eine E-Mail mit deinem neuen Passwort.";
								$output_label = "E-Mail:";
								$input_submit = "E-Mail versenden";
								break;
								
								case 'english':
								$output = "If you have forgotten your password, please fill in your e-mail address. We will send you an e-mail with your new password.";
								$output_label = "E-mail:";
								$input_submit = "Send e-mail";
								break;
							}
							echo $output;
							echo "<form action=\"mainpage.php?language=".$_GET['language']."&action=sendnewpassword\" method=\"post\">";
							echo $output_label;
							echo "<input name=\"email\" size=\"18\"></input>";
							echo "<input type=\"submit\" value=\"".$input_submit."\"></input>";
							echo "</form>";
							break;
							
							case 'sendnewpassword':
							switch($_GET['language'])
							{
								case 'german':
								$output_done = "Du erhälst in Kürze eine E-Mail.";
								$output_wrongmail = "Die angegebene E-Mail befindet sich nicht in unserer Datenbank.";
								break;
								
								case 'english':
								$output_done = "You will receive an e-mail from us shortly.";
								$output_wrongmail = "There is no entry in our database for the e-mail you entered.";
								break;
							}
							$email = mysqli_real_escape_string($mysql_connection, $_POST['email']);
							$userdata_query = mysqli_query($mysql_connection, "SELECT id,name,surname FROM users WHERE email = '$email' LIMIT 1");
							if(mysqli_num_rows($userdata_query))
							{
									$new_password = mt_rand(100000,999999);
									$new_password_encrypted = md5($new_password);
									$userdata = mysqli_fetch_array($userdata_query);
									switch($_GET['language'])
									{
										case 'german':
										$text = "Hallo ".$userdata['name']." ".$userdata['surname'].",\r\n\r\nDu hast dein Passwort vergessen, daher schicken wir dir mit dieser E-Mail dein neues Passwort.\r\nDein neues Passwort lautet: ".$new_password."\r\nDu solltest dein Passwort nach dem ersten Login ändern, nicht dass du es wieder vergisst.\r\n\r\nMit freundlichen Grüssen,\r\nDas Gosseek-Team";
										$subejct = "Neues Passwort bei Gosseek";
										break;
										
										case 'english':
										$text = "Dear ".$userdata['name']." ".$userdata['surname'].",\r\n\r\nYou forgot your password, hence we are sending you a new one with this e-mail.\r\nYour new password: ".$new_password."\r\nYou should change your password after the first login, such that you don't forget it again.\r\n\r\nBest regards\r\nThe Gosseek-Team";
										$subject = "New password for Gosseek";
										break;
									}
									$header = 'From: benno.staub@hotmail.com' . "\r\n" . 'Content-type: text/plain; charset=\"utf-8\"' . "\r\n";
									mail($email, $subject, $text, $header);
									mysqli_query($mysql_connection, "UPDATE users SET password = '$new_password_encrypted' WHERE id = ".$userdata['id']);
									echo $output_done;
							}else
							{
								echo $output_wrongmail;
							}
							break;
							
							case 'failedlogin':
							switch($_GET['language'])
							{
								case 'german':
								$output_wrongmail = "Die angegebene E-Mail befindet sich nicht in unserer Datenbank.";
								$output_wrongpassword = "Falsches Passwort.";
								$output_notactivated = "Dein Account wurde noch nicht aktiviert.";
								break;
								
								case 'english':
								$output_wrongmail = "There is no entry in our database for the e-mail you entered.";
								$output_wrongpassword = "Wrong password.";
								$output_notactivated = "Your account has not been activated yet.";
								break;
							}
								switch($_GET['reason'])
								{
									
									case 'wrongmail':
									echo $output_wrongmail;
									break;
									
									case 'wrongpassword':
									echo $output_wrongpassword;
									break;
									
									case 'notactivated':
									echo $output_notactivated;
									break;
								}								
							break;
							
							case 'invalidsession':
							switch($_GET['language'])
							{
								case 'german':
								$output = "Ungültige Sitzung.";
								break;
								
								case 'english':
								$output = "Invalid session.";
								break;
							}
							echo $output;
							break;
							
							case 'logout':
							session_start();
							$_SESSION = array();
							session_destroy();
							switch($_GET['language'])
							{
								case 'german':
								$output = "Erfolgreich ausgeloggt.";
								break;
								
								case 'english':
								$output = "Successfully logged out.";
								break;
							}
							echo $output;
							break;
						}
					?>
				</p>
			</div>
			<div class="boxleft">
					<div class="leftinnerbox">
						<br>
						<?php
							switch($_GET['language'])
							{
								case 'german':
								$output = "Registriere dich hier: ";
								$output_name = "Vorname: ";
								$output_surname = "Nachname: ";
								$output_email = "E-Mail: ";
								$output_password = "Passwort: ";
								$input_submit = "Registrieren";
								break;
								
								case 'english':
								$output = "Register here: ";
								$output_name = "Name: ";
								$output_surname = "Surname: ";
								$output_email = "E-mail: ";
								$output_password = "Password: ";
								$input_submit = "Register";
								break;
							}
							echo "<form action=\"mainpage.php?language=".$_GET['language']."&action=registering\" method=\"post\" accept-charset=\"utf-8\">";
								echo $output;
								echo "<br>";
								echo "<br>";
								echo $output_name;
								echo "<br>";
								echo "<input name=\"name\" size=\"30\" maxlength=\"30\"></input>";
								echo "<br>";
								echo $output_surname;
								echo "<br>";
								echo "<input name=\"surname\" size=\"30\" maxlength=\"30\"></input>";
								echo "<br>";
								echo $output_email;
								echo "<br>";
								echo "<input name=\"email\" size=\"30\" maxlength=\"100\" type=\"email\"></input>";
								echo "<br>";
								echo $output_password;
								echo "<br>";
								echo "<input name=\"password\" size=\"30\" maxlength=\"30\" type=\"password\"></input>";
								echo "<br>";
								echo "<center>";
									echo "<input type=\"submit\" value=\"".$input_submit."\">";
								echo "</center>";
							echo "</form>";
						?>
					</div>
			</div>
		</div>
	</body>
</html>