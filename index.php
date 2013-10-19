
<?PHP

$echokey = 'MROQS3CCSCKZERMNL';
$fmakey = 'FWYXBWBNHU1EZW2C';

$dir    = 'tempaudio';
$ssh_dir = '/home/jasonsigal/jasonsigal.cc/ccrex/tempaudio';
$files_list = scandir($dir);

$current_file = $files_list[0];


//if a file is uploaded, send it to Echo Nest for analysis
//curl -X POST "http://developer.echonest.com/api/v4/track/upload" -d "api_key=MROQS3CCSCKZERMNL&url=http://example.com/audio.mp3"

function analyzeFile($file_) {

	//find file type by dissecting the string
	$file_type_ = substr($file_,strlen($file_)-3,3);  //extract original extension

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "http://developer.echonest.com/api/v4/track/upload");
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, array(
	  'filetype' => $file_type_,
	  'api_key' => $echokey,
	  'track' => $file_
	));
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


<!-- FILE UPLOAD STUFF -->
	<h2> Welcome to CCRex! Upload your sound for analysis:</h2>
	<?PHP
	ini_set('display_errors', true);
	ini_set('display_startup_errors', true);
	error_reporting(E_ALL);

	// Limit what people can upload for security reasons
	$allowed_mime_types = array("audio/x-wav"=>"wav", 
								"audio/vnd.wave"=>"wav",
								"audio/mp3"=>"mp3",
								"audio/mpeg"=>"mp3",
								"audio/wav"=>"wav"
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
					analyzeFile($current_file);

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
		<form class="small button" enctype="multipart/form-data" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<p>
				<input type="file" name="bytes" />
				<input type="hidden" name="form_submitted" value="true" />
				<br />
				<input type="submit" name="submit" value="upload .wav or .mp3" />
			</p>
		</form>
		<div class="progress" style="width: 50%"><span class="meter"></span></div>

<?
	}
?>

</body>
</html>