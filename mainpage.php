<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	require 'PHPMailer/Exception.php';
	require 'PHPMailer/PHPMailer.php';
	require 'PHPMailer/SMTP.php';
	header('Content-Type: text/html; charset=UTF-8');
	include("version.php");
	include("connect_db.php");
	if(empty($_GET{'action'}))
	{
		$_GET['action'] = "motivation";
	}
	if(!(isset($_GET['language'])) OR empty($_GET['language']))
	{
		$_GET['language'] = "german";
	}
echo "<html>";
	echo "<head>";
		echo "<title>";
			switch($_GET['language'])
			{
				case 'german':
				$output_slogan = "Gosseek - Achieve your Goals and Share your Experience";
				break;
				
				case 'english':
				$output_slogan = "Gosseek - Achieve your Goals and Share your Experience";
				break;
			}
			echo $output_slogan;
		echo "</title>";
		echo "<meta name=\"author\" content=\"Benno Staub\">";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles/stylesheet.css\" />";
		echo "<meta http-equiv=\"cache-control\" content=\"no-cache\" />";
	echo "</head>";
	echo "<body>";
		echo "<div class=\"settingsbar\">";
			echo "<a href=\"mainpage.php?language=german&action=".$_GET['action']."\">DE</a>";
			echo "<a href=\"mainpage.php?language=english&action=".$_GET['action']."\">EN</a>";
		echo "</div>";
		echo "<div class=\"bodypage\">";
			echo "<div class=\"leftmenu\">";
				echo "<div class=\"logo\">";;
				echo "</div>";
				echo "<div class=\"navigation\">";
					switch($_GET['language'])
					{
						case 'german':
						$a_motivation = "Motivation";
						$a_register = "Registrieren";
						break;
						
						case 'english':
						$a_motivation = "Motivation";
						$a_register = "Register";
						break;
					}
					echo "<a href=\"mainpage.php?language=".$_GET['language']."&action=register\">".$a_register."</a>";
					echo "<a href=\"mainpage.php?language=".$_GET['language']."&action=motivation\">".$a_motivation."</a>";
				echo "</div>";
			echo "</div>";
			echo "<div class=\"page\">";
				echo "<div class=\"subpage\">";
					echo "<div class=\"rightmenu\">";
						echo "<div class=\"fixed\">";
							echo "<div class=\"login\">";
								switch($_GET['language'])
								{
									case 'german':
									$output_email = "E-Mail:";
									$output_password = "Passwort:";
									$input_submit = "Einloggen";
									$a_forgotpassword = "Passwort vergessen";
									$a_motivation = "Motivation";
									break;
									
									case 'english':
									$output_email = "E-mail:";
									$output_password = "Password:";
									$input_submit = "Log in";
									$a_forgotpassword = "Forgot password";
									$a_motivation = "Motivation";
									break;
								}
										echo "<form action=\"login.php?language=".$_GET['language']."&action=login\" method=\"post\" accept-charset=\"utf-8\" >";
											echo "<span>".$output_email."</span>";
											echo "<input name=\"email\" size=\"12\" maxlength=\"40\"></input>";
											echo "<br>";
											echo "<br>";
											echo "<span>".$output_password."</span>";
											echo "<input type=\"password\" name=\"password\" size=\"12\"></input>";
											echo "<br>";
											echo "<br>";
											echo "<span></span><input type=\"submit\" value=\"".$input_submit."\"></input>";
										echo "</form>";
										echo "<a href=\"mainpage.php?language=".$_GET['language']."&action=forgotpassword\">".$a_forgotpassword."</a>";
							echo "</div>";
						echo "</div>";
					echo "</div>";
					echo "<div class=\"borderline\">";
						echo "<div class=\"fixed\">";
						echo "</div>";
					echo "</div>";
					echo "<div class=\"content\">";
					
						switch($_GET['action'])
						{
							case 'motivation':
							switch($_GET['language'])
							{
								case 'german':
								$output = "Hallo Besucher,<br><br>Gosseek ist ein Soziales Netzwerk, bei dem nicht die Person, sondern das Erreichen persönlicher Ziele im Vordergrund steht. Jeder Mensch hat Dutzende persönliche Ziele in allen möglichen Bereichen. Von den meisten träumen wir und nur wenige werden aktiv verfolgt. Motivation ist selten das Problem, oft weiss man einfach nicht wo/wie starten, auf das Ziel hinzuarbeiten. Gosseek ändert das jetzt. Registrierte Nutzer haben folgende zwei Möglichkeiten:<br><br><b>1)  Definiere dein eigenes Ziel und fange an zu dokumentieren:</b> Poste über deinen Tagesablauf, deine aufgebrachten Stunden, Schlüsselmomente und jede Erfahrung die du in Verbindung mit dem Erreichen deines Zieles gemacht hast. Abschluss der Dokumentation ist das Veröffentlichen deiner - mit dem dokumentierten Aufwand - erreichten Resultate. <b>Nutzen: </b>Durch das Dokumentieren kannst du deinen Aufwand besser reflektieren, erkennst eigene Fehler oder sehr vorteilhafte Entscheidungen. So kannst du effizienter auf dein Ziel hinarbeiten und deine Methoden laufend verbessern. Ein weiterer Punkt ist erhöhte Motivation: Durch das Dokumentieren bleibst du am Ball und dein Ziel wird konkret. Andere Nutzer können dein Ziel mitverfolgen und laufend Tipps und weiteren Input geben um dir zu helfen.<br><br><b>2)  Suche nach ähnlichen Zielen wie dein Eigenes, schau dir die erreichten Resultate anderer Nutzer an und folge jener Dokumentation, welche deine gewünschten Resultate erreicht hat. </b>Damit erhälst du stets die geposteten Beiträge des gewünschten Zieles. Diese kannst du als konkrete Anleitung nutzen und damit jederzeit abschätzen, ob du auf dem richtigen Kurs bist oder gewisse Anpassungen zu deinem Alltag vornehmen musst, um die gewünschten Resultate zu erreichen. Du kannst zudem aus den Erfahrungen und Fehlern des Dokumentierers lernen, um noch effizienter zu arbeiten und bessere Resultate zu erreichen.<br><br>Zusammenfassend unterstützt Gosseek folgendes:<br><b>1.Jeder Mensch hat Ziele, welche er verfolgen will.<br>2.Jeder Mensch hat Wissen, von welchem andere profitieren können.<br>3.Jeder Mensch kann aus den Erfahrungen anderer lernen und zu seinem Vorteil nutzen.</b><br><br><b>Gosseek bietet die Platform dafür! Registriere dich jetzt und starte damit, deine Ziele zu Errungenschaften zu machen.</b>";
								break;
								
								case 'english':
								$output = "Dear visitor,<br><br>Gosseek is a social network that sets achieving personal goals in focus, not people. Everyone has dozens of personal goals in all different areas. People dream about the most of them while only a few goals are seeked actively. Motivation is rarely a problem, most people are just missing a guideline on how to start working for their goals. Gosseek will change this now. Registered users have the following two options:<br><br><b>1) Define your own goal and start documenting:</b> Post about your daily schedule, the amount of hours worked for your goal, key moments and any experience you made in connection with achieving your goal. The end of the documentation will be a publication of your - with the documented effort - achieved results. <b>Advantage:</b> While documenting everything, you can better reflect your effort, notice mistakes but also benefitful decisions. With this, you can work more efficiently towards your goal and improve your methods continuously. Another point is increased motivation: While documenting, you stay on track and your goal becomes concrete. Other users can follow your goal and provide input and tips to help you.<br><br><b>2)  Search for goals similar to yours, check the achieved results by other users, and follow the documentation that achieved your desired results.</b> By that you get all the posts from that goal. You can use them as a concrete guideline and constantly estimate whether you are on track or you need to adapt your daily schedule in order to achieve the desired results. You can also learn from the mistakes and experiences from the author and therefore work more efficiently and achieve better results.<br><br>Summarized, Gossek supports the following:<b><br>1.Every person has goals he/she wants to achieve.<br>2.Every person has knowledge of which others can benefit.<br>3.Every person can learn from the experience of others and use it for his/her advantage.</b><br><br><b>Gossek provides a platform for that! Register now and start transforming your goals into achievements.</b>";
								break;
							}
							echo "<div class=\"block\">";
								echo "<div class=\"innerblock\">";
									echo $output;
								echo "</div>";
							echo "</div>";
							break;
							
							case 'register':
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
							echo "<div class=\"block\">";
								echo "<div class=\"innerblock\">";
									echo "<form action=\"mainpage.php?language=".$_GET['language']."&action=registering\" method=\"post\" accept-charset=\"utf-8\">";
										echo "<label>".$output_name."</label>";
										echo "<input name=\"name\" maxlength=\"30\"></input>";
											echo "<div class=\"clear\"></div>";
										echo "<label>".$output_surname."</label>";
										echo "<input name=\"surname\" maxlength=\"30\"></input>";
											echo "<div class=\"clear\"></div>";
										echo "<label>".$output_email."</label>";
										echo "<input name=\"email\" maxlength=\"100\" type=\"email\"></input>";
											echo "<div class=\"clear\"></div>";
										echo "<label>".$output_password."</label>";
										echo "<input name=\"password\" maxlength=\"30\" type=\"password\"></input>";
											echo "<div class=\"clear\"></div>";
										echo "<span>";
											echo "<input type=\"submit\" value=\"".$input_submit."\">";
										echo "</span>";
									echo "</form>";
								echo "</div>";
							echo "</div>";
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
								$full_name = $name." ".$surname;
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
										$body = "Hallo ".$name." ".$surname.",<br><br>Vielen Dank für deine Anmeldung bei Gosseek.<br>Hier sind deine Anmeldedaten:<br>E-Mail: ".$email."<br>Passwort: ".$password."<br><br>Um deinen Account zu aktivieren, bitte klicke auf folgenden Link: <a href=\"http://www.gosseek.com/mainpage.php?language=".$_GET['language']."&action=activate_account&code=".$activation_code."\">Registrierung bestätigen</a><br><br>Mit freundlichen Grüssen<br>Das Gosseek Team.";
										$subject = "Registrierung bei Gosseek";
										$altbody = "Hallo ".$name." ".$surname.",\r\n\r\nVielen Dank für deine Anmeldung bei Gosseek.\r\nHier sind deine Anmeldedaten:\r\nE-Mail: ".$email."\r\nPasswort: ".$password."\r\n\r\nUm deinen Account zu aktivieren, bitte klicke auf folgenden Link: http://www.gosseek.com/mainpage.php?language=".$_GET['language']."&action=activate_account&code=".$activation_code."\r\n\r\nMit freundlichen Grüssen\r\nDas Gosseek Team.";
										$subject = "Registrierung bei Gosseek";
										break;
										
										case 'english':
										$body = "Dear ".$name." ".$surname.",<br><br>Thank you for your registration on Gosseek.<br>Here are your credentials:<br>E-mail: ".$email."<br>Password: ".$password."<br><br>To activate your account, please click on the following link: <a href=\"http://www.gosseek.com/mainpage.php?language=".$_GET['language']."&action=activate_account&code=".$activation_code."\">Confirm registration</a><br><br>Best regards<br>The Gosseek Team.";
										$subject = "Registration on Gosseek";
										$altbody = "Dear ".$name." ".$surname.",\r\n\r\nThank you for your registration on Gosseek.\r\nHere are your credentials:\r\nE-mail: ".$email."\r\nPassword: ".$password."\r\n\r\nTo activate your account, please click on the following link: http://www.gosseek.com/mainpage.php?language=".$_GET['language']."&action=activate_account&code=".$activation_code."\r\n\r\nBest regards\r\nThe Gosseek Team.";
										$subject = "Registration on Gosseek";
										break;
									}
									
									$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
									//Server settings
									$mail->CharSet = "UTF-8";
									$mail->SMTPDebug = 0;                                 // Enable verbose debug output
									$mail->isSMTP();                                      // Set mailer to use SMTP
									$mail->Host = 'smtp.live.com';  // Specify main and backup SMTP servers
									$mail->SMTPAuth = true;                               // Enable SMTP authentication
									$mail->Username = 'gosseek@hotmail.com';                 // SMTP username
									$mail->Password = $gosseek_hotmail_password;                           // SMTP password
									$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
									$mail->Port = 587;                                    // TCP port to connect to
									//Recipients
									$mail->setFrom('gosseek@hotmail.com', 'Gosseek');
									$mail->addAddress($email, $full_name);     // Add a recipient
									$mail->addReplyTo('gosseek@hotmail.com', 'Gosseek');
									//Content
									$mail->isHTML(true);                                  // Set email format to HTML
									$mail->Subject = $subject;
									$mail->Body    = $body;
									$mail->AltBody = $altbody;
									$mail->send();
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
							echo "<div class=\"block\">";
								echo "<div class=\"innerblock\">";
									echo $output."<br><br>";
									echo "<form action=\"mainpage.php?language=".$_GET['language']."&action=sendnewpassword\" method=\"post\" accept-charset=\"utf-8\">";
									echo "<label>".$output_label."</label>";
									echo "<input name=\"email\" size=\"18\"></input>";
									echo "<div class=\"clear\"></div>";
									echo "<span>";
										echo "<input type=\"submit\" value=\"".$input_submit."\"></input>";
									echo "</span>";
									echo "</form>";
								echo "</div>";
							echo "</div>";
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
										$body = "Hallo ".$userdata['name']." ".$userdata['surname'].",<br><br>Du hast dein Passwort vergessen, daher schicken wir dir mit dieser E-Mail dein neues Passwort.<br>Dein neues Passwort lautet: ".$new_password."<br>Du solltest dein Passwort nach dem ersten Login ändern, nicht dass du es wieder vergisst.<br><br>Mit freundlichen Grüssen,<br>Das Gosseek-Team";
										$altbody = "Hallo ".$userdata['name']." ".$userdata['surname'].",\r\n\r\nDu hast dein Passwort vergessen, daher schicken wir dir mit dieser E-Mail dein neues Passwort.\r\nDein neues Passwort lautet: ".$new_password."\r\nDu solltest dein Passwort nach dem ersten Login ändern, nicht dass du es wieder vergisst.\r\n\r\nMit freundlichen Grüssen,\r\nDas Gosseek-Team";
										$subject = "Neues Passwort bei Gosseek";
										break;
										
										case 'english':
										$body = "Dear ".$userdata['name']." ".$userdata['surname'].",<br><br>You forgot your password, hence we are sending you a new one with this e-mail.<br>Your new password: ".$new_password."<br>You should change your password after the first login, such that you don't forget it again.<br><br>Best regards<br>The Gosseek-Team";
										$altbody = "Dear ".$userdata['name']." ".$userdata['surname'].",\r\n\r\nYou forgot your password, hence we are sending you a new one with this e-mail.\r\nYour new password: ".$new_password."\r\nYou should change your password after the first login, such that you don't forget it again.\r\n\r\nBest regards\r\nThe Gosseek-Team";
										$subject = "New password for Gosseek";
										break;
									}
									$full_name = $userdata['name']." ".$userdata['surname'];
									$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
									//Server settings
									$mail->CharSet = "UTF-8";
									$mail->SMTPDebug = 0;                                 // Enable verbose debug output
									$mail->isSMTP();                                      // Set mailer to use SMTP
									$mail->Host = 'smtp.live.com';  // Specify main and backup SMTP servers
									$mail->SMTPAuth = true;                               // Enable SMTP authentication
									$mail->Username = 'gosseek@hotmail.com';                 // SMTP username
									$mail->Password = $gosseek_hotmail_password;                           // SMTP password
									$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
									$mail->Port = 587;                                    // TCP port to connect to
									//Recipients
									$mail->setFrom('gosseek@hotmail.com', 'Gosseek');
									$mail->addAddress($email, $full_name);     // Add a recipient
									$mail->addReplyTo('gosseek@hotmail.com', 'Gosseek');
									//Content
									$mail->isHTML(true);                                  // Set email format to HTML
									$mail->Subject = $subject;
									$mail->Body    = $body;
									$mail->AltBody = $altbody;
									$mail->send();
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
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
		echo "<div class=\"notebar\">";
			switch($_GET['language'])
			{
				case 'german':
				$output_note = "Diese Seite befindet sich im aktiven Aufbau. Probleme, Kritik und Vorschläge können via Mail an gosseek@hotmail.com gesendet werden. Vielen Dank!";
				break;
				
				case 'english':
				$output_note = "This page is under active development. Problems, criticisms and suggestions can be sent via mail to gosseek@hotmail.com Thank you!";
				break;
			}
			echo $output_note;
		echo "</div>";
	echo "</body>";
echo "</html>";
?>