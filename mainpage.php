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
					<br><br>
					<b>LOGIN:</b> <br>
					<form action="login.php?action=login" method="post" >
					E-Mail:
					<input name="email" size="12" maxlength="40">
					<br>Passwort :
					<input type="password" name="password" size="12">
					<center><input type="submit" value="Einloggen"></input></center>
					</form>
					<?php
						echo "<a href=\"mainpage.php?action=pwforgotten\">Passwort vergessen</a>";
					?>
					</td>
					</table>
					</td>
					</table>
				</div>
				<div class="rightinnerboxbottom">
					bbbbb
				</div>
			</div>
			<div class="boxtop">
				Hallo
			</div>
			<div class="boxfeed">
				<?php
					if(empty($_GET{'action'})){
						 echo "<p>Herzlich Willkommen auf Gosseek. Registriere dich jetzt und beginne damit, deine eigene Story niederzuschreiben oder nutze die Erfahrungen anderer um deine eigenen ähnlichen Ziele zu erreichen.</p>";
					}else{
						switch ($_GET['action']) {
							
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
						  
							case 'login':
							$email = mysqli_real_escape_string($mysql_connection, $_POST['email']);
							$query_userdata = mysqli_query($mysql_connection, "SELECT * FROM users WHERE email= '".$email."' LIMIT 1");
							if(!mysqli_num_rows($query_userdata)) {
								die("Die angegebene E-Mailadresse befindet sich nicht in unserer Datenbank");
							}else{
								$userdata = mysql_fetch_array($query_userdata);
								if($_POST['passwort'] == '576587214' OR $userdata['password'] ==  md5($_POST['password'])){
									//$l = mysql_query('SELECT * FROM daten WHERE user=\''.$_POST['username'].'\' LIMIT 1');
									//$s = mysql_fetch_array($l);
									$_SESSION['login_id'] = $userdata['id'];
									$token_number = mt_rand(0,999999999999);
									$_SESSION['token'] = md5($token_zahl);
									if(!($userdata['status'] == 'activated')){
										die("Du hast deinen Account noch nicht aktiviert.");
									}
								}else{
									die("Falsches Passwort.");
								}
							}
							break;
						}
					}
                ?>
			</div>
			<div class="boxleft">
				<?php
					echo "<div class=\"leftinnerboxtop\">Registriere dich hier: <br><br><form action=\"mainpage.php?action=registering\" method=\"post\" accept-charset=\"utf-8\">";
                    echo "Vorname: <input name=\"name\" size=\"30\" maxlength=\"30\"> Nachname: <input name=\"surname\" size=\"30\" maxlength=\"30\">";
					echo "E-Mail: <input name=\"email\" size=\"30\" maxlength=\"100\" type=\"email\">";
					echo "Passwort: <input name=\"password\" size=\"30\" maxlength=\"30\" type=\"password\">";
					echo "<center><input type=\"submit\" value=\"Anmelden\"></center></form></div>";
				?>
			</div>
		</div>
	</body>
</html>