<?php
header('P3P: CP="CAO PSA OUR"');

require '../fb/facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => '',
  'secret' => '',
  'fileUpload' => true,
  'cookie' => true,
));

// We may or may not have this data based on a $_GET or $_COOKIE based session.
//
// If we get a session here, it means we found a correctly signed session using
// the Application Secret only Facebook and the Application know. We dont know
// if it is still valid until we make an API call using the session. A session
// can become invalid if it has already expired (should not be getting the
// session back in this case) or if the user logged out of Facebook.
$session = $facebook->getSession();

$me = null;
// Session based API call.
if ($session) {
  try {
    $uid = $facebook->getUser();
    $me = $facebook->api('/me');
    $accesstoken = $session['access_token'];
  } catch (FacebookApiException $e) {
    error_log($e);
  }
}

// login or logout url will be needed depending on current user state.
if ($me) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl(array(
   'canvas' => 1,
   'fbconnect' => 0,
  'req_perms' => 'publish_stream, user_photos, user_status, user_photo_video_tags',
   'next' => 'http://apps.facebook.com/<app name>/'
));
echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
           
}


?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
<META http-equiv=Content-Type content="text/html; charset=UTF-8"> 
    <title>My Check-in Path</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
      .top {
      	background-image:url('top.png');
      	background-repeat:repeat-x;
      	color: white;
      	height:40px;
      	padding: 5px;
      }
      .middle {
      	background-color: #8789ec;
      	color: white;
      	height:40px;
      	padding: 5px;
      }
      .bottom {
      	background-image:url('bottom.png');
      	background-repeat:repeat-x;
      	color: white;
      	height:40px;
      	padding: 5px;
      }
      td {
      padding: 5px;
      }
      #run_box{
      height:600px;
      width:400px;
 	  overflow: hidden;
 	  float:left;
 	  margin: 10px;
 	  cursor:pointer;
 	  cursor:hand;
      }
      #run_track{
      height:2000px;
      width:400px;
      background-image:url('bg.jpg');
      }
      #result_box{
      float:left;
      height:600px;
      width:280px;
      margin: 10px;
      }
    </style>
<?php
	//print_r($_GET[uid]);

	$mystatus =  $facebook->api( 
	array( 'method' => 'fql.query', 
			'query' => "SELECT message FROM status WHERE uid = me() limit 10" ));

	$myphoto=  $facebook->api( 
	array( 'method' => 'fql.query', 
			'query' => "SELECT src_small, src_big FROM photo WHERE pid IN(SELECT pid FROM photo_tag WHERE subject = me())" ));
	
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script>
$(document).ready(function(){
$('#montage').load(function() {
  $('#loading').slideUp();
  $('#montage').slideDown();
  $('#newone').slideDown();

});
 });
</script>
  </head>
  <body> 
   <h1>Dada Montage</h1>

<a id='newone' href="javascript:location.reload(true)"  style='display:none;'>Get a new Montage</a>
 <div id="canvas" >
<img id='loading' src='images/now_loading.gif'> 
<img id='montage' src='createimage.php' style='display:none;'> 

</div> 

<div id="fb-root"></div>
	<?php if ($me): ?>
    

    <?php else: ?>
      <a href="<?php echo $loginUrl; ?>">
        <img src="http://static.ak.fbcdn.net/rsrc.php/zB6N8/hash/4li2k73z.gif">
      </a>
	
    <?php endif ?>

    <?php if ($me): ?>

	
    

	

	 <?php else: ?>
    <strong><em>Login &#30535;&#19979;D friend&#21435;&#26348;&#37002;!</em></strong>
   <?php endif ?>
	
  </body>
</html>