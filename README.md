Creates 4K / 4K HDR / BluRay Banners depending on the resolution of your video files.. 1080 and under will get labeled Blu-Ray, 4K will get 4K banner and 4K HDR will get 4K HDR banner. 

If a poster.jpg exists already within your movie folder, it will use that poster to add the banner to. If there is no poster.jpg, it will connect to TheMovieDB.org and download one, add the banner and save it as poster.jpg, it will also save the original as poster-BACKUP.jpg incase you wish to revert to original.

Written in simple PHP code, its a mess, and I really don't care, it works. Feel free to edit it how you please or push updates to the github. I needed something fast and easy because doing this manually was tedius, and the only other script I could find required docker, and im no python savant so PHP it is.. 

This was written specifically for my Windows server, but should work with linux no problem.

Requirements:
* PHP with Curl Enabled in php.ini (https://www.php.net)
* TheMovieDB.org API Key (https://www.themoviedb.org)
* ImageMagicK (https://imagemagick.org/index.php)
* MediaInfo (https://mediaarea.net/en/MediaInfo/Download/Windows)

Make sure mediainfo, imagemagick, php are all in your windows path.

This script can be ran manually every time you wish to update, or you can set the directory to monitor in the php file and set it on a scheduler of your choice. If you wish to regenerate a poster for a particular movie, delete the poster_skip file within that directory. 

Installation:
1.) Clone to directory of your choice, edit the top of the run_me.php to suit your configuration and api keys. 
2.) Open command prompt or a bash shell, and execute: php run_me.php, pretty simple!
