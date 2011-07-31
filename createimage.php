<?
header('P3P: CP="CAO PSA OUR"');
//Header("Content-type: image/png");
    require '../fb/facebook.php';

    // Create our Application instance (replace this with your appId and secret).
    $facebook = new Facebook(array(
      'appId'  => '',
      'secret' => '',
      'fileUpload' => true,
     'cookie' => true,
    ));

  $mystatus =  $facebook->api( 
	array( 'method' => 'fql.query', 
			'query' => "SELECT message FROM status WHERE uid = me() LIMIT 20" ));

 $myinfo =  $facebook->api( 
	array( 'method' => 'fql.query', 
			'query' => "SELECT pic_big FROM user WHERE uid = me() " ));

   $myphoto=  $facebook->api( 
	array( 'method' => 'fql.query', 
			'query' => "SELECT object_id, src, src_height, src_width FROM photo WHERE pid IN(SELECT pid FROM photo_tag WHERE subject = me())  ORDER BY created DESC" ));

shuffle($myphoto);
//print_r($mystatus);
$image = imagecreatetruecolor(660,500);

$red = imagecolorallocate($image, 0, 0, 0);

$white = imagecolorallocate($image, 255, 255, 255);
$gray = imagecolorallocate($image, 100, 100, 100);
//imagefill($image , 0, 0, $red);
$colours = array(0x00FFFFAA, 0x00FFAAFF, 0x00AAFFFF, 0x00FFAAAA, 0x00AAFFAA, 0x00AAAAFF);
$count = 0;
   foreach($myphoto as $photo)
{
   if($count > 100)
     break;
$count++;

   if($count == 1)
    $pid = $photo[object_id];
  else
    $pid = $pid . ', '.$photo[object_id] ;

   $iconimage = imagecreatefromstring(file_get_contents($photo[src]));
   $bg = imagecreatetruecolor($photo[src_width]+20, $photo[src_height]+20);
   imagefill($bg, 0, 0, $white );
    imagecopymerge($bg, $iconimage, 10, 10, 0, 0, $photo[src_width], $photo[src_height], 100);
   $iconimage = imagerotate($bg, mt_rand(-45, 45), 0x00857234);
imagecolortransparent($iconimage, 0x00857234);
   imagecopymerge($image, $iconimage, mt_rand(-60, 660), mt_rand(-60, 500), 0, 0, imagesx($iconimage), imagesy($iconimage), 100);
}
//print_r($pid);
$mycomment =  $facebook->api( 
	array( 'method' => 'fql.query', 
			'query' => "SELECT text FROM comment WHERE object_id IN ($pid)" ));
shuffle($mycomment);

$count = 0;
//print_r($mycomment);

function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {

    for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
        for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
            $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);

   return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
}
$fontsize = array(10, 10, 20, 30);
foreach($mycomment as $status){
 if($count > 20)
     break;
$count++;

    $ran = imagecolorallocate($image, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
    imagettfstroketext($image, $fontsize[mt_rand(0, 3)], rand(-45, 45), mt_rand(-60, 660), mt_rand(-60, 500), $colours[mt_rand(0,5)], $red , 'wt040.ttf', $status[text], 2);
}

   $iconimage = imagecreatefromstring(file_get_contents($myinfo[0][pic_big]));
   $bg = imagecreatetruecolor(imagesx($iconimage)+20, imagesy($iconimage)+20);
   imagefill($bg, 0, 0, $white );
    imagecopymerge($bg, $iconimage, 10, 10, 0, 0, imagesx($iconimage), imagesy($iconimage), 100);
   $iconimage = imagerotate($bg, mt_rand(-45, 45), 0x00857234);
   imagecolortransparent($iconimage, 0x00857234);
   imagecopymerge($image, $iconimage, mt_rand(0, 360), mt_rand(0, 250), 0, 0, imagesx($iconimage), imagesy($iconimage), 100);



/*
foreach($mystatus as $status){
    $ran = imagecolorallocate($image, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));

 ImageTTFText($image, mt_rand(10, 40), rand(0, 1)*-90, mt_rand(-60, 660), mt_rand(-60, 500),  $colours[mt_rand(0,4)], 'wt040.ttf', $status[message]);
}
*/





imagePNG($image);

$uid = $facebook->getUser();
imagePNG($image, "images/$uid.png");


$args = array('message' => 'Create your DaDa Art! http://apps.facebook.com/<app name>/ ');
	$args['image'] = '@' . realpath("images/$uid.png");
$args['tags'] = array(array('tag_uid' => $uid,
                     'x' => 50,
                     'y' => 50));
	$data = $facebook->api('/me/photos', 'post', $args);


