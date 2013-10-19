
<?PHP

$echokey = 'MROQS3CCSCKZERMNL';
$fmakey = 'FWYXBWBNHU1EZW2C';

$dir    = 'tempaudio';
$ssh_dir = '/home/jasonsigal/jasonsigal.cc/ccrex/tempaudio';
$files_list = scandir($dir);

$current_file = $files_list[0];


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
    //var_dump($return_data);
    $data = json_decode($return_data);
    //echo($file_);

	$tid = $data['track']['id']."&bucket=audio_summary";
	$apiURL= "http://developer.echonest.com/api/v4/track/profile?api_key=".$key_."&format=json&id=".$tid;
	echo($apiURL);
/**
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
}


?>


<html>
	<head>
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>CCREX</title>

  
  <link rel="stylesheet" href="css/foundation.css">
  

  <script src="js/vendor/custom.modernizr.js"></script>
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
				<input class="small button" type="submit" name="submit" value="upload .wav or .mp3" />
			</p>
		</form>
		</div>	
		<div class="progress" style="width: 50%"><span class="meter"></span></div>
	</div>
		<div>
	<form class="custom">
  <label for="checkbox1">
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
 			<h3>Similar songs</h3>
 			<div class="large-2 columns panel">
 					<img src="#">
 			</div>
 			<div class="large-9 columns panel">
		[		<p>Artist - Song name - License<br>
				<a href="#">Song URL</a><br>
				<a href="#">Download URL</a></p>]
 			</div>
 		</div>
 </div>







</body>
</html>