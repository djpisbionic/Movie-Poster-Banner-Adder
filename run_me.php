<?php

// Turn logging off or on.
$logging = 'ON';

// TheMovieDB.org API Key
$tmdb_apikey = 'XXXXXXXXXXXXXXXXXXXXX';

// Change to 'AUTO' if you plan to use this with a scheduler or crontab, 'MANUAL' if you will run the script manually.
$manual = 'MANUAL'; // USE MANUAL FOR DIRECTORY ENTRY

// Directory to update if set on a movie scheduler. 
$auto_directory = 'n:/mov/'; // INCLUDE ENDING SLASH

// --------------------- DO NOT EDIT BELOW THIS LINE --------------------- //


// TMDB API WRAPPER
require_once("includes/tmdb.php");
// mediainfo WRAPPER
require_once("includes/class.medianfo.php");


if ($manual == 'MANUAL') { 
	$file = readline("Enter Library Directory With Ending Slash: ");
} else { 
	$file = $auto_directory;
}

// Initialize Variables //

$mi = '';
$width = '';
$info = '';
$hdr = '';

$supported_movie = array(
    'mkv',
    'avi',
    'mov',
    'mp4',
	'mpg',
	'ts'
);
$timestamp = date('m/d/Y H:i:s', time());

// SCAN DIRECTORY & IGNORE FILES THAT START WITH A PERIOD 	   
$filesarray = preg_grep('/^([^.])/', scandir($file));
// BUILD NEW ARRAY SO IT DOESNT START AT NUMBER 2
$files = array();
foreach ($filesarray as $filesnewarray) {
	$files[] = $filesnewarray;	
}
foreach($files as $filemy) {
		$filesarray2 = preg_grep('/^([^.])/', scandir($file.$filemy));
		$files2 = array();
		foreach ($filesarray2 as $filesnewarray2) {
			$files2[] = $filesnewarray2;	
		}	
	    foreach($files2 as $filemy2) {
			$skip_check = $file.$filemy.'/poster_skip';
			if (!file_exists($skip_check)) {
				$ext = strtolower(pathinfo($filemy2, PATHINFO_EXTENSION));
				if (in_array($ext, $supported_movie)) {
					$mi = new mediaInfo($file.$filemy.'/'.$filemy2);
					$info = $mi->get_video_info();
					if (isset($info['HDR format'])) { $hdr = $info['HDR format']; };
					$width = str_replace(' ', '', $info['Width']); 
					$width = str_replace('pixels', '', $width);
					if ($width > 1920) { $type = 'UHD'; } else { $type = 'BLURAY'; }
					if ($hdr != '') { $type = 'HDR'; }
					$poster = '"'.$file.$filemy.'/poster.jpg"';
					$poster_check = $file.$filemy.'/poster.jpg';
					

					if (file_exists($poster_check)) {
						copy($poster_check, $file.$filemy.'/poster-BACKUP.jpg');
						exec('magick '.$poster.' '.$type.'.png ^ -resize %[fx:u.w]x%[fx:u.h] -gravity north -composite "'.$file.$filemy.'/poster.jpg"');

						echo "-- Created Poster For ".$filemy." --";
						echo "\n";
						$log  = "-- Created Poster For ".$filemy." --".PHP_EOL;
						//Save string to log, use FILE_APPEND to append.
						$skip_file = "Ignore";
						file_put_contents($file.$filemy.'/poster_skip', $skip_file, FILE_APPEND);
						file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);
					} else {
						// MAKE CONNECTION TO TMDB
						$tmdb_V3 = new TMDBv3($tmdb_apikey);

						$search_name = explode('(', $filemy);
						$searchTitle = $tmdb_V3->searchMovie($search_name[0],'en');
						$poster_dl = 'http://image.tmdb.org/t/p/w600_and_h900_bestv2/'.$searchTitle['results']['0']['poster_path'];
						$save_poster = $file.$filemy."/poster.jpg";
						$original_poster = $file.$filemy."/poster-BACKUP.jpg";					
						if (file_put_contents($save_poster, file_get_contents($poster_dl)) !== false) { 
							file_put_contents($original_poster, file_get_contents($poster_dl));					
							echo "-- Downloaded Poster For ".$filemy." --";
							echo "\n";
							exec('magick '.$poster.' '.$type.'.png ^ -resize %[fx:u.w]x%[fx:u.h] -gravity north -composite "'.$file.$filemy.'/poster.jpg"');
							echo "-- Created Poster For ".$filemy." --";
							echo "\n";
							sleep(5);
							$log  = $timestamp." -- Downloaded Poster For ".$filemy." --".PHP_EOL;					
							$log  .= $timestamp." -- Created Poster For ".$filemy." --".PHP_EOL;
							//Save string to log, use FILE_APPEND to append.
							$skip_file = "Ignore";
							file_put_contents($file.$filemy.'/poster_skip', $skip_file, FILE_APPEND);
							file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);
						}
					}
				}
			} 
		}
}

?>