<?php
include("version.php");
?>
<html>
<head>
<title>Gosseek - Achieve your goals</title>
<meta name="author" content="Benno">
<?php
switch($version)
{
	case 0:
	echo "<meta http-equiv=\"refresh\" content=\"0; URL=http://www.gosseek.com/mainpage.php\">";
	break;
	
	case 1:
	echo "<meta http-equiv=\"refresh\" content=\"0; URL=http://127.0.0.1/mainpage.php\">";
	break;
}
?>
</head>
<body text="#000000" bgcolor="#FFFFFF" link="#FF0000" alink="#FF0000" vlink="#FF0000">
</body>
</html>