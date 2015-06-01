<?php
require_once('config.php');
$board = 'talk';
$start = 0;
$limit = 100;
if(!empty($_GET['start']))
{
	$start = $_GET['start'];
}
if(!empty($_GET['limit']))
{
	$limit = $_GET['limit'];
}
if(!empty($_GET['board']))
{
	$board = $_GET['board'];
}
//$date = '2014-01-09';
$mysqli = new mysqli($host, $user, $password, $database);
$sql = "SELECT id,content FROM posts WHERE forum_alias=? AND content LIKE '%imgur%' ORDER BY createdAt DESC LIMIT ?,?";
$mysqli->set_charset('utf8');
$stmt = $mysqli->prepare($sql);

$stmt->bind_param('sss',$board,$start,$limit);

$stmt->execute();

$stmt->bind_result($id,$content);

$output = array();
$output['result'] = array();
$imgur_ext = 'jpg';
$re_image = "/(http:\\/\\/i\\.imgur.com\\/[a-zA-Z0-9]+\\.(jpg|jpeg|bmp|png|gif))/i";
$re_url = "/http:\\/\\/imgur.com\\/([a-zA-Z0-9]+)/i";
while ($stmt->fetch()) {
	$image_result = preg_match_all($re_image, $content, $matches);
	if($image_result > 0)
	{
		foreach($matches[1] as $key=>$val)
		{
			array_push($output['result'],array('id'=>$id,'image'=>$val));
		}
	}
	$url_result = preg_match_all($re_url, $content, $matches);
	if($url_result > 0)
	{
		foreach($matches[1] as $key=>$val)
		{
			array_push($output['result'],array('id'=>$id,'image'=>'http://i.imgur.com/'.$val.'.jpg'));
		}
	}
}
$output['total'] = count($output['result']);
$stmt->close();
$mysqli->close();
echo json_encode($output);
?>
