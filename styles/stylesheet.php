<?php
header("Content-type: text/css; charset: UTF-8");
session_start();
include("../version.php");
include("../connect_db.php");
if($color_query = mysqli_query($mysql_connection, "SELECT color_frame, color_box, color_background FROM users WHERE id = ".$_SESSION['id']." LIMIT 1"))
{
	$color = mysqli_fetch_array($color_query);
}
//green: 008000
//nearly white: fdfdfd
//black: 000000
if(empty($color['color_frame']))
{
	$color_frame = "#3e2a09";
}else
{
	$color_frame = $color['color_frame'];
}
if(empty($color['color_box']))
{
	$color_box = "#f1ebdd";
}else
{
	$color_box = $color['color_box'];
}
if(empty($color['color_background']))
{
	$color_background = "#ded3b9";
}else
{
	$color_background = $color['color_background'];
}
?>

body { width: 100%; background-color: <?php echo $color_background; ?>; }
p { padding: 10px; }

.boxmain { width: 99%; height: 100%; background: <?php echo $color_background ?>; }

.boxright { float: right; width: 18%; height: 98%; border-radius: 5px; margin: 5px; background: <?php echo $color_frame; ?>; color: #000000; }
	
.rightinnerboxtop { width: 90%; height: 28%; line-height: 100%; border-radius: 5px; background: <?php echo $color_box; ?>; color:#000000; margin: 0 auto; margin-top: 5%;}
.rightinnerboxtop input { float: right; }
.rightinnerboxtop p a { float: right; margin-right: 10px;}
.rightinnerboxtoplinks { margin-left: 5px; margin-top: 5px; height: 92%; }
.rightinnerboxtop .rightinnerboxtoplinks a { width: 60%; float: left; }
.profilepic { float: right; margin: 3px; width: 48px; height: 64px; }
.profilepic img { border-radius: 5px; width: 48px; }
	
.rightinnerboxbottom { width: 90%; height: 65%; border-radius: 5px; background: <?php echo $color_box; ?>; color: #000000; margin: 0 auto; margin-top: 5%; padding-top: 5%; }
.rightinnerboxbottom a { margin-left: 5px; margin-bottom: 5px; width: 80%; float: left; }
	
.boxtop { float: right; width: 79%; height: 5%; border-radius: 5px; margin: 5px; background: <?php echo $color_frame; ?>; color: #000000; display: flex; align-items: center; }
	
.boxtopinner { width: 98%; height: 80%; border-radius: 5px; background: <?php echo $color_box; ?>; color: #000000; margin: 0 auto; }
.boxtopinner p { display: inline-block; vertical-align: middle; line-height: 40%; height: 40%; width: 30%; float: right; text-align: right; font-size: 115%; margin: auto; }
.boxtopinner a { display: inline-block; line-height: 170%; text-align: left; margin: auto 0; margin-left: 5px; }
	
.boxleft { float: right; width: 19%; height: 92%; border-radius: 5px; margin: 5px; background: <?php echo $color_frame; ?>; color: #000000; text-align: center; }

.offlineleftinnerbox { width: 90%; height: 96%; border-radius: 5px; background: <?php echo $color_box; ?>; color: #000000; margin: 0 auto; margin-top: 5%; text-align: left; padding-left: 5px; }
.offlineleftinnerbox form { margin-left: 5px; }
.offlineleftinnerbox input { width: 95%; }

.onlineleftinnerboxtop { width: 90%; height: 24%; border-radius: 5px; background: <?php echo $color_box; ?>; color: #000000; margin: 0 auto; margin-top: 5%; text-align: left; padding-left: 5px; }
.onlineleftinnerboxbottom { width: 90%; height: 70.5%; border-radius: 5px; background: <?php echo $color_box; ?>; color: #000000; margin: 0 auto; margin-top: 5%; text-align: left; padding-left: 5px; }
.onlineleftinnerboxbottom a { display: block; }

	
.boxfeed { float: right; width: 58%; height: 87.5%; border-radius: 5px; margin: 5px; padding: 5px; padding-top: 25px; background: <?php echo $color_box; ?>; color: #000000; overflow:auto; }
.boxfeed form { width: 80% ; margin: 0 auto; }	
.boxfeed label { float: left; width: 318px; }
.boxfeed select { width: 120px; }
.boxfeed span select { margin-left: 144px; width: 60px; }
.boxfeed p input { width: 200px; margin-left: 195px; }
.boxfeed .clear {clear: both;}


.feedpost { width: 95%; border-radius: 5px; margin: auto; background: <?php echo $color_frame; ?>; padding: 5px; }
.feedheader { background: <?php echo $color_box; ?>; color: #000000; border-radius: 5px; margin: auto; margin-bottom: 5px; padding-left: 10px; padding-right: 10px; }
.feedtime { width: 20%; background: <?php echo $color_box; ?>; color: #000000; border-radius: 5px; margin: auto; margin-left: 0px; padding-left: 10px; padding-right: 10px; float: right }
.feedtitle { font-weight: bold; background: <?php echo $color_box; ?>; color: #000000; border-radius: 5px; margin: auto; margin-bottom: 5px; padding-left: 10px; padding-right: 10px; }
.feedcontent { background: <?php echo $color_box; ?>; color: #000000; border-radius: 5px; margin: auto; margin-bottom: 5px; padding-left: 10px; padding-right: 10px; padding-top: 5px; }
.feedcontent img { max-width: 18%; margin-right: 5px; }
.feedlinks_owner { display: flex; font-weight: bold; background: <?php echo $color_box; ?>; color: #000000; border-radius: 5px; margin: auto; padding-left: 10px; padding-right: 10px; margin-bottom: 5px;}
.feedlinks_owner a { padding-right: 10px; }
.feedlinks { display: flex; font-weight: bold; background: <?php echo $color_box; ?>; color: #000000; border-radius: 5px; margin: auto; padding-left: 10px; padding-right: 10px;}
.feedlinks a { padding-right: 10px; }


.feedlinksleft { width: 50%; }
.feedlinksright { text-align: right; width: 50%; }

.profile { width: 95% height: 70%; border-radius: 5px; margin: auto; backround: <?php echo $color_frame; ?>; padding: 5px; }
.profile span img { float: right; margin-right: 50px; width: 180px;}
.profile h { display: block; width: 90%; margin-left: 20px; }
.profile p { display: inline-block; width: 30%; margin: 0 0; padding: 2px; }

.inbox td {
	max-width: 300px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.goalpictures { height: 50px; width: 100%; display: flex;}
.goalpictures img { max-height: 50px; margin-right: 5px; }
	