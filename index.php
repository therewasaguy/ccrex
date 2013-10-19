
<?PHP

$echokey = 'MROQS3CCSCKZERMNL';
$fmakey = 'FWYXBWBNHU1EZW2C';

$dir    = 'tempaudio';
$ssh_dir = '/home/jasonsigal/jasonsigal.cc/ccrex/tempaudio';
$files_list = scandir($dir);

$current_file = $files_list[0];

$arr = array();
$arrfma = array();

//if a file is uploaded, send it to Echo Nest for analysis
//curl -X POST "http://developer.echonest.com/api/v4/track/upload" -d "api_key=MROQS3CCSCKZERMNL&url=http://example.com/audio.mp3"

function analyzeFile($file_, $key_) {

	//find file type by dissecting the string
	$file_type_ = substr($file_,strlen($file_)-3,3);  //extract original extension

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "http://developer.echonest.com/api/v4/track/upload");
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS,array(
	  'filetype' => $file_type_,
	  'api_key' => $key_,
	  'format'=> 'json',
	  'track' => '@'.$file_
	));
//	curl_setopt($curl, CURLOPT_POSTFIELDS, "api_key=".$key_."&url=".$file_);

		/**array(
	  'filetype' => $file_type_,
	  'api_key' => $key_,
	  'track' => '@'.$file_
	));

	  **/
	$return_data = curl_exec($curl);
    var_dump($return_data);
    $data = json_decode($return_data, true);
  

	$tid = var_dump($data["response"]["track"]["id"])."&bucket=audio_summary";
	$apiURL= "http://developer.echonest.com/api/v4/track/profile?api_key=".$key_."&format=json&id=".$tid;
	echo($apiURL);

/**	$apiURL="http://developer.echonest.com/api/v4/playlist/static?api_key=MROQS3CCSCKZERMNL&song_id=SOJUJTO1393BE3380A&format=json&results=100&type=song-radio&bucket=id:fma&limit=true&bucket=tracks"
	$curl = curl_init();
               curl_setopt($curl, CURLOPT_URL, $apiURL);
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
               //curl_setopt($curl, CURLOPT_HEADER, true);
               $return_data = curl_exec($curl);
               //var_dump($return_data);
               $data = json_decode($return_data);

               print_r($data);

              $tempo= $data->response->track->audio_summary->tempo;                
             $TS= $data->response->track->audio_summary->time_signature;
             $acousticness = $data->response->track->audio_summary->acousticness;
             $valence= $data->response->track->audio_summary->valence;

              $acousticness = $data->response->track->audio_summary->acousticness;
	//print_r($tid);
	//echo($tid);
              **/

//seeding a playlist with variables:
//http://developer.echonest.com/api/v4/playlist/static?api_key=MROQS3CCSCKZERMNL&song_id=SOJUJTO1393BE3380A&format=json&results=100&type=song-radio&bucket=id:fma&limit=true&bucket=tracks
            
}


//take the uploaded song_id and make an array of FMA song ID's
	$apiURL="http://developer.echonest.com/api/v4/playlist/static?api_key=MROQS3CCSCKZERMNL&song_id=SOJUJTO1393BE3380A&format=json&results=10&type=song-radio&bucket=id:fma&limit=true&bucket=tracks";
	$curl = curl_init();
               curl_setopt($curl, CURLOPT_URL, $apiURL);
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
               //curl_setopt($curl, CURLOPT_HEADER, true);
               $return_data = curl_exec($curl);

               //remove padding 
				$return_data=utf8_encode($return_data);
				$return_data=preg_replace('/.+?({.+}).+/','$1',$return_data); 
				// now, process the JSON string 
               $data = json_decode($return_data, true);
               
   foreach($data["songs"] as $item){ 
   		$fma_explode = $item["tracks"][0]['foreign_id'];
   		$fma_track_ids = explode("fma:track:",$fma_explode);
   		//json_decode($fma_track_ids);
   		//print_r($fma_track_ids);
   		//var_dump($fma_track_ids);
   		//var_dump($fma_track_id[0]['foreign_id']);
	}

//loop thru FMA song ID array to FMA API for FMA Track Title, Artist Name, Track URL (download), Artist Image, Source URL, License, 
//while ($start !== false &&  $start < strlen($fma_track_ids)) {
//	$temp = explode("fma:track:",$fma_track_ids);
//	var_dump($temp[0]);
//	var_dump($temp[1]);
//	foreach ($fma_track_ids as $pair) {
//		list($key,$value) = explode('track:',$pair);
		//$arr[$key]=str_replace("\"","",$value);
//		$arr[] = $value;

//	}

	//var_dump($arr);

$x = 0;
echo(count($fma_track_ids));

//for($i = 0; $i<=arraylen())

foreach($fma_track_ids as $fma_query) {
//	$x = $x+10;
//	print($x);
//	print_r($fma_query);
	$fma_apiURL="http://freemusicarchive.org/api/get/tracks.json?api_key=".$fmakey."&track_id=".$fma_query;
	//echo($fma_apiURL);
	$curl = curl_init();
               curl_setopt($curl, CURLOPT_URL, $fma_apiURL);
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
               //curl_setopt($curl, CURLOPT_HEADER, true);
               $return_data = curl_exec($curl);
               //var_dump($return_data);
               //remove padding 
				$return_data=utf8_encode($return_data);
				$return_data=preg_replace('/.+?({.+}).+/','$1',$return_data); 
				// now, process the JSON string 
               //print_r($return_data);
               $data = json_decode($return_data, true);
               //print_r($data);
    $artist_name = $data['artist_name'];
	$track_title = $data['track_title'];
	$track_download_url = $data['track_url']."/download";
	array_push($arrfma, $artist_name." - ".$track_title." - ".$track_download_url);
}

print_r($arrfma);

?>

<html>
	<head>
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>CCREX</title>

  
  <link rel="stylesheet" href="css/foundation.css">
	<link rel="stylesheet" type="text/css" href="css/jplayer.blue.monday.css" />
  

  <script src="js/vendor/custom.modernizr.js"></script>
  <script src="js/jquery.jplayer.min.js"></script>
  <script src="js/jquery.jplayer.inspector.js"></script>
   <script src="js/jplayer.playlist.min.js"></script>
  <script src="js/popcorn.jplayer.js"></script>

	</head>

	<body>
	<!-- Introduction -->
	<div class="row">
		<div class="large-12 columns">
			<h1>CCRex</h1>
			<h3>Find CC song recommendations that closely match any song you upload!</h3>
			<hr />
	</div>

<!-- FILE UPLOAD STUFF -->
	
	<?PHP
	ini_set('display_errors', true);
	ini_set('display_startup_errors', true);
	error_reporting(E_ALL);

	// Limit what people can upload for security reasons
	$allowed_mime_types = array("audio/x-wav"=>"wav", 
								"audio/vnd.wave"=>"wav",
								"audio/mp3"=>"mp3",
								"audio/mpeg"=>"mp3",
								"audio/wav"=>"wav",
								"video/mpeg"=>"mpg",
								"audio/mp4"=> 'mp4',
								"audio/x-m4a"=> 'm4a'
								);

	// Make sure form was submitted
	if (isset($_POST['form_submitted']) && $_POST['form_submitted'] == "true")
	{
		// Check the mime type
		$allowed_types = array_keys($allowed_mime_types);
		$allowed = false;
		if (isset($_FILES['bytes']['type']))
		{		
			for ($i = 0; $i < sizeof($allowed_types) && !$allowed; $i++)
			{
				if (strstr($_FILES['bytes']['type'], $allowed_types[$i]))
				{
					$allowed = true;
				}
			}
		
			// If the mime type is good, save it..
			if ($allowed)
			{
				// Create a name
				$uploadfilename = /**time() . "_" . rand(1000,9999) . "_" . **/basename($_FILES['bytes']['name']);
				
				// Make sure apache can write to this folder
				$uploaddir = '/home/jasonsigal/jasonsigal.cc/ccrex/tempaudio';
				$uploadfile = $uploaddir ."/". $uploadfilename;

				$uploadrelativefile = 'http://jasonsigal.cc/ccrex/tempaudio/' . $uploadfilename;
		
				if (move_uploaded_file($_FILES['bytes']['tmp_name'], $uploadfile))
				{
					// Make sure the file isn't executable and you can delete it if you need
					chmod($uploadfile, 0666);
										
					// Tell the user
					$current_file = $uploadfilename;
					$files_list = scandir($dir);
				
					echo "<p><span class=\"highlight\">Success!! <a href=\"" . $uploadrelativefile  . "\">" . $current_file . "</a> is the current file.</span></p>";

					//send to Echo Nest for analysis...
					analyzeFile($ssh_dir."/".$current_file,$echokey);

				}
				else
				{
					echo "<p>Error on upload...!  Here is some debugging info:</p>";
					var_dump($_FILES);
				}
			}
			else
			{
				echo "<p>Type not allowed...! Here is some debugging info:</p>";
				var_dump($_FILES);
			}
		}
		else
		{
			echo "<p>Strange, file type not sent by browser...!  Here is some debugging info:</p>";
			var_dump($_FILES);
		}
	}
	else
	{
?>
		
	<div class="large-12 columns">
			<h3>Upload Your Song For Analysis</h3>

		<div class="large-3 columns">	
		<form enctype="multipart/form-data" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<p>
				<input class="small button" type="file" name="bytes" />
				<input type="hidden" name="form_submitted" value="true" />
				<br />
				<input class="small button" type="submit" name="submit" value="Upload Song" />
			</p>
		</form>
		</div>	
		<div class="large-9 columns progress" style="width: 50%"><span class="meter"></span></div>
	</div>
		<div>
	<form class="custom">
  <label>
    <input type="checkbox" id="commercialcheckbox" style="display: none;">
    <span class="custom checkbox"></span> Do you want to use this song commercially?
  </label>
    <input type="checkbox" id="remixcheckbox" style="display: none;">
    <span class="custom checkbox"></span> Will you remix the song or use it in a video?
  </label>
</form>
</div>


<?
	}
?>

<!-- Results -->

<div class="row">
 		<div class="large-12 columns">
 			<h3>CC Song Recommendations</h3>
 			<h4>We found the following similar songs based on a number of criteria including tempo, mode, timbre and more<h4>
<!-- jPlayer -->
<div  id="jquery_jplayer_1" class="jp-jplayer large-8 columns"></div>
<div id="jp_container_1" class="jp-audio">
    <div class="jp-type-single">
        <div class="jp-gui jp-interface">
            <ul class="jp-controls">
                <!-- comment out any of the following <li>s to remove these buttons -->
                <li><a href="javascript:;" class="jp-play" tabindex="1">play</a>
                </li>
                <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a>
                </li>
                <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a>
                </li>
               <!-- <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a>
                </li>
                <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a>
                </li> 
                -->

                <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a>
                </li>
            </ul>
            <!-- you can comment out any of the following <div>s too -->
            <div class="jp-progress">
                <div class="jp-seek-bar">
                    <div class="jp-play-bar"></div>
                </div>
            </div>
            <div class="jp-volume-bar">
                <div class="jp-volume-bar-value"></div>
            </div>
            <div class="jp-current-time"></div>
            <div class="jp-duration"></div>
        </div>
        <div class="jp-title">
            <ul>
                <li>Cro Magnon Man</li>
            </ul>
        </div>
        <div class="jp-no-solution"> <span>Update Required</span>
To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.</div>
    </div>
</div>


<!-- list -->
 			<ul id="playlist">
 				<li class="item">

		 			<div class="large-12 columns panel">
		 				<div class="large-3 columns">
		 				<img src="http://cdn.7static.com/static/img/sleeveart/00/010/119/0001011998_200.jpg">
		 				</div>
		 				<div class="large-8 columns">
		 				<input type="hidden" name="type" value="<?php echo $var; ?>" >
						<p><? echo($Artist); ?>Artist <? echo($songname); ?> Song name<? echo($license); ?>License<br>
						<a href="http://previews.7digital.com/clips/34/11123262.clip.mp3">Song URL<?php echo $_GET['link']; ?></a>
						<a href="#" class="small button">Download</a></p>]
						</div>
		 			</div>
	 			</li>
	 				
	 		</ul>	
 		</div>
 </div>






<script type="text/javascript">
 $(document).ready(function(){
  $("#jquery_jplayer_1").jPlayer({
   ready: function () {
    $(this).jPlayer("setMedia", {
     m4a: "/media/mysound.mp4",
     oga: "/media/mysound.ogg"
    });
   },
   swfPath: "/js",
   supplied: "m4a, oga"
  });
 });
</script>
</body>
</html>