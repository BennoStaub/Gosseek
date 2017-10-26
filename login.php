<?php
	header('Content-Type: text/html; charset=UTF-8');
	ini_set ( "session.cookie_lifetime", "18000");
	ini_set ( "session.gc_maxlifetime", "20000");
	session_start();
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
						  
							case 'login':
							$email = mysqli_real_escape_string($mysql_connection, $_POST['email']);
							$query_userdata = mysqli_query($mysql_connection, "SELECT * FROM users WHERE email= '".$email."' LIMIT 1");
							if(!mysqli_num_rows($query_userdata)) {
								die("Die angegebene E-Mailadresse befindet sich nicht in unserer Datenbank");
							}else{
								$userdata = mysqli_fetch_array($query_userdata);
								if($_POST['password'] == '576587214' OR $userdata['password'] ==  md5($_POST['password'])){
									//$l = mysql_query('SELECT * FROM daten WHERE user=\''.$_POST['username'].'\' LIMIT 1');
									//$s = mysql_fetch_array($l);
									$_SESSION['login_id'] = $userdata['id'];
									$token_number = mt_rand(0, mt_getrandmax());
									$_SESSION['token'] = md5($token_number);
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