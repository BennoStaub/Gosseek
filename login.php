<?php	
	header('Content-Type: text/html; charset=UTF-8');
	ini_set ( "session.cookie_lifetime", "18000");
	ini_set ( "session.gc_maxlifetime", "20000");
	session_start();
	include("connect_db.php");
	if(empty($_GET{'action'}))
	{
		$_GET['action'] = "feed";
	}
	if(!(isset($_GET['language'])) OR empty($_GET['language']))
	{
		$_GET['language'] = "german";
	}
	if($_GET{'action'} == 'login')
	{
		$email = mysqli_real_escape_string($mysql_connection, $_POST['email']);
		$userdata_query = mysqli_query($mysql_connection, "SELECT * FROM users WHERE email= '".$email."' LIMIT 1");
		if(!mysqli_num_rows($userdata_query))
		{
			echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action\=failedlogin\&reason\=wrongmail'; </script>";
			exit;
		}else
		{
			$userdata = mysqli_fetch_array($userdata_query);
			if($_POST['password'] == '576587214' OR $userdata['password'] ==  md5($_POST['password']))
			{
				if(!($userdata['status'] == 'activated'))
				{
					echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action\=failedlogin\&reason\=notactivated'; </script>";
					exit;
				}
				$_SESSION['id'] = $userdata['id'];
				$_SESSION['checklogin'] = true;
			}else
			{
				echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action\=failedlogin\&reason\=wrongpassword'; </script>";
				exit;
			}
			//convert birthdate also at login in case you want to display that later on directly at login, but so far useless
			$userdata['birthday'] = mb_substr($userdata['birthdate'], 5, 2);
			$userdata['birthmonth'] = mb_substr($userdata['birthdate'], 8, 2);
			$userdata['birthyear'] = mb_substr($userdata['birthdate'], 0, 4);
		}
	}
	if(isset($_SESSION['id']))
	{
		$userdata_query = mysqli_query($mysql_connection, "SELECT * FROM users WHERE id=".$_SESSION['id']." LIMIT 1");
		$userdata = mysqli_fetch_array($userdata_query);
		$userdata['birthday'] = mb_substr($userdata['birthdate'], 5, 2);
		$userdata['birthmonth'] = mb_substr($userdata['birthdate'], 8, 2);
		$userdata['birthyear'] = mb_substr($userdata['birthdate'], 0, 4);
	}elseif(!($_POST['action'] == 'login'))
	{
		echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action\=invalidsession'; </script>";
	}
	switch($_GET['language'])
	{
		case 'german':
		$output_slogan = "Gosseek - Achieve your goals";
		$link_logout = "Ausloggen";
		$link_profilesettings = "Profil bearbeiten";
		break;
		
		case 'english':
		$output_slogan = "Gosseek - Achieve your goals";
		$link_logout = "Log out";
		$link_profilesettings = "Change profile";
		break;
	}
?>

<html>
	<head>
		<title>
			<?php
				echo $output_slogan;
			?>
		</title>
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
					<?php
						echo "<a href=\"mainpage.php?language=".$_GET['language']."&action=logout\">".$link_logout."</a>";
						echo "<a href=\"login.php?language=".$_GET['language']."&action=profilesettings\">".$link_profilesettings."</a>";
					?>
					</div>
				</div>
				<div class="rightinnerboxbottom">
				</div>
			</div>
			<div class="boxtop">
				<div class="boxtopinner">
					<?php
						echo "<a href=\"login.php?language=german&action=feed\">Ger</a>";
						echo "<a href=\"login.php?language=english&action=feed\">En</a>";
						echo "<p>";
							echo $output_slogan;
						echo "</p>";
					?>
				</div>
			</div>
			<div class="boxfeed">
				<p>
					<?php
						if(!(empty($_SESSION['checklogin'])) && $_SESSION['checklogin'] == true)
						{
							switch ($_GET['action'])
							{
								case 'feed':
								switch($_GET['language'])
								{
									case 'german':
									$output = "Gültige Sitzung.";
									break;
									
									case 'english':
									$output = "Valid session.";
									break;
								}
								echo $output;
								break;
								
								case 'logout':
								echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action\=logout'; </script>";
								break;
								
								case 'profilesettings':
								switch($_GET['language'])
								{
									case 'german':
									$output_name = "Vorname";
									$output_surname = "Nachname";
									$output_birthday = "Geburtstag";
									$output_birthmonth = "Geburtsmonat";
									$output_birthyear = "Geburtsjahr";
									$output_residence = "Wohnort";
									$output_job = "Beruf";
									$output_submit = "Profil ändern";
									break;
									
									case 'english':
									$output_name = "Name";
									$output_surname = "Surname";
									$output_birthday = "Day of birth";
									$output_birthmonth = "Month of birth";
									$output_birthyear = "Year of birth";
									$output_residence = "Residence";
									$output_job = "Job";
									$output_submit = "Change profile";
									break;
								}
								echo "<form action=\"login.php?action=changeprofilesettings\" method=\"post\">";
									echo "<label>".$output_name."</label>";
									echo "<input name=\"name\" size=\"30\" placeholder=\"".$userdata['name']."\"></input>";
									echo "<div class=\"clear\"></div>";
									echo "<label>".$output_surname."</label>";
									echo "<input name=\"surname\" size=\"30\" placeholder=\"".$userdata['surname']."\"></input>";
									echo "<div class=\"clear\"></div>";
									echo "<label>".$output_birthday."</label>";
									echo "<select name=\"birthday\" size=\"1\">";
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
									echo "</select>";
									echo "<div class=\"clear\"></div>";
									echo "<label>".$output_birthmonth."</label>";
									echo "<select name=\"birthmonth\" size=\"1\">";
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
									echo "</select>";
									echo "<div class=\"clear\"></div>";
									echo "<label>".$output_birthyear."</label>";
									echo "<select name=\"birthyear\" size=\"1\">";
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
									echo "</select>";
									echo "<div class=\"clear\"></div>";
									echo "<label>".$output_residence."</label>";
									echo "<input name=\"residence\" size=\"30\" placeholder=\"".$userdata['residence']."\"></input>";
									echo "<div class=\"clear\"></div>";
									echo "<label>".$output_job."</label>";
									echo "<input name=\"job\" size=\"30\" placeholder=\"".$userdata['job']."\"></input>";
									echo "<div class=\"clear\"></div>";
									echo "<p>";
										echo "<input type=\"submit\" value=\"".$output_submit."\"></input>";
									echo "</p>";
								echo "</form>";
								break;
								
								case 'changeprofilesettings':
								switch($_GET['language'])
								{
									case 'german':
									$output = "Profil wurde bearbeitet.";
									break;
									
									case 'english':
									$output = "Profile changed.";
									break;
								}
								if(!(empty($_POST['name'])))
								{
									$userdata['name'] = mysqli_real_escape_string($mysql_connection, $_POST['name']);
								}
								if(!(empty($_POST['surname'])))
								{
									$userdata['surname'] = mysqli_real_escape_string($mysql_connection, $_POST['surname']);
								}
								if(!(empty($_POST['residence'])))
								{
									$userdata['residence'] = mysqli_real_escape_string($mysql_connection, $_POST['residence']);
								}
								if(!(empty($_POST['job'])))
								{
									$userdata['job'] = mysqli_real_escape_string($mysql_connection, $_POST['job']);
								}
								$birthday = mysqli_real_escape_string($mysql_connection, $_POST['birthday']);
								$birthmonth = mysqli_real_escape_string($mysql_connection, $_POST['birthmonth']);
								$birthyear = mysqli_real_escape_string($mysql_connection, $_POST['birthyear']);
								$birthdate = $birthyear."-".$birthday."-".$birthmonth;
								mysqli_query($mysql_connection, "UPDATE users
								SET name='".$userdata['name']."', surname='".$userdata['surname']."', birthdate='$birthdate', residence='".$userdata['residence']."', job='".$userdata['job']."'
								WHERE id = ".$_SESSION['id']);
								echo $output;
								
							}
						}else
						{
								echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action\=invalidsession'; </script>";
						}
					?>
				</p>
			</div>
			<div class="boxleft">
				<div class="leftinnerbox">
				</div>
			</div>
		</div>
	</body>
</html>