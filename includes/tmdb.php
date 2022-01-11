<?php
/**
* TMDB API v3 PHP class - wrapper to API version 3 of 'themoviedb.org
* API Documentation: http://help.themoviedb.org/kb/api/about-3
* Documentation and usage in README file
*
* @pakage TMDB_V3_API_PHP
* @author adangq <adangq@gmail.com>
* @copyright 2012 pixelead0
* @date 2012-02-12
* @link http://www.github.com/pixelead
* @version 0.0.2
* @license BSD http://www.opensource.org/licenses/bsd-license.php
*
*
* Portions of this file are based on pieces of TMDb PHP API class - API 'themoviedb.org'
* @Copyright Jonas De Smet - Glamorous | https://github.com/glamorous/TMDb-PHP-API
* Licensed under BSD (http://www.opensource.org/licenses/bsd-license.php)
* @date 10.12.2010
* @version 0.9.10
* @author Jonas De Smet - Glamorous
* @link {https://github.com/glamorous/TMDb-PHP-API}
*
* Function List
*   public function  __construct($apikey,$lang='en')
*   public function setLang($lang="en") 
*   public function getLang() 
*   public function setImageURL($config) 
*   public function getImageURL($size="original") 
*   public function movieTitles($idMovie) 
*   public function movieTrans($idMovie)
*   public function movieTrailer($idMovie,$source="") 
*   public function movieDetail($idMovie)
*   public function moviePoster($idMovie)
*   public function movieCast($idMovie)
*   public function movieInfo($idMovie,$option="",$print=false)
*   public function searchMovie($movieTitle)
*   public function getConfig() 
*   public function latestMovie() 
*   public function nowPlayingMovies($page=1) 
*
*   private function _call($action,$text,$lang="")
*   private function setApikey($apikey) 
*   private function getApikey() 

URL LIST:
configuration		http://api.themoviedb.org/3/configuration
Imagenes		http://cf2.imgobject.com/t/p/original/IMAGEN.jpg
Buscar Pelicula		http://api.themoviedb.org/3/search/movie
Buscar Persona		http://api.themoviedb.org/3/search/person
Movie Info		http://api.themoviedb.org/3/movie/11
Casts			http://api.themoviedb.org/3/movie/11/casts
Imagenes		http://api.themoviedb.org/3/movie/11/images
Trailers		http://api.themoviedb.org/3/movie/11/trailers
translations		http://api.themoviedb.org/3/movie/11/translations
Titulos Alternativos 	http://api.themoviedb.org/3/movie/11/alternative_titles

//Collection Info 	http://api.themoviedb.org/3/collection/11
//Person images		http://api.themoviedb.org/3/person/287/images
*
** v0.0.2:
*    fixed issue #2 (Object created in class php file)
*    added functions latestMovie, nowPlayingMovies (thank's to steffmeister)
*

*/


###########################
class TMDBv3{
     #<CONSTANTS>
	#@var string url of API TMDB
	const _API_URL_ = "http://api.themoviedb.org/3/";

	#@var string Version of this class
	const VERSION = '0.0.2';

	#@var string API KEY
	private $_apikey;

	#@var string Default language
	private $_lang;

	#@var string url of TMDB images
	private $_imgUrl;
     #</CONSTANTS>
###############################################################################################################
	/**
	* Construct Class
	* @param string apikey
	* @param string language default is english
	*/
		public function  __construct($apikey,$lang='en') {
			//Assign Api Key
			$this->setApikey($apikey);
		
			//Setting Language
			$this->setLang($lang);

			//Get Configuration
			$conf = $this->getConfig();
			if (empty($conf)){echo "Unable to read configuration, verify that the API key is valid";exit;}

			//set Images URL contain in config
			$this->setImageURL($conf);
		}//end of __construct

	/** Setter for the API-key
	 * @param string $apikey
	 * @return void
	 */
		private function setApikey($apikey) {
			$this->_apikey = (string) $apikey;
		}//end of setApikey

	/** Getter for the API-key
	 *  no input
	 **  @return string
	 */
		private function getApikey() {
			return $this->_apikey;
		}//end of getApikey

	/** Setter for the default language
	 * @param string $lang
	 * @return void
	 **/
		public function setLang($lang="en") {
			$this->_lang = $lang;
		}//end of setLang

	/** Getter for the default language
	 * no input
	 * @return string
	 **/
		public function getLang() {
			return $this->_lang;
		}//end of getLang

	/**
	* Set URL of images
	* @param  $config Configurarion of API
	* @return array
	*/
		public function setImageURL($config) {
			$this->_imgUrl = (string) $config['images']["base_url"];
		} //end of setImageURL

	/** Getter for the URL images
	 * no input
	 * @return string
	 */
		public function getImageURL($size="original") {
			return $this->_imgUrl . $size;
		}//end of getImageURL

	/**
	* movie Alternative Titles
	* http://api.themoviedb.org/3/movie/$id/alternative_titles
	* @param array  titles
	*/
		public function movieTitles($idMovie) {
			$titleTmp = $this->movieInfo($idMovie,"alternative_titles",false);
			foreach ($titleTmp['titles'] as $titleArr){
				$title[]=$titleArr['title']." - ".$titleArr['iso_3166_1'];
			}
			return $title;
		}//end of movieTitles

	/**
	* movie translations
	* http://api.themoviedb.org/3/movie/$id/translations
	* @param array  translationsInfo
	*/
		public function movieTrans($idMovie)
		{
			$transTmp = $this->movieInfo($idMovie,"translations",false);

			foreach ($transTmp['translations'] as $transArr){
				$trans[]=$transArr['english_name']." - ".$transArr['iso_639_1'];
			}
			return $trans;
		}//end of movieTrans

	/**
	* movie Trailer
	* http://api.themoviedb.org/3/movie/$id/trailers
	* @param array  trailerInfo
	*/
		public function movieTrailer($idMovie) {
			$trailer = $this->movieInfo($idMovie,"trailers",false);
			return $trailer;
		} //movieTrailer


	/**
	* movie Detail
	* http://api.themoviedb.org/3/movie/$id
	* @param array  movieDetail
	*/
		public function movieDetail($idMovie)
		{
			return $this->movieInfo($idMovie,"",false);
		}//end of movieDetail

	/**
	* movie Poster
	* http://api.themoviedb.org/3/movie/$id/images
	* @param array  moviePoster
	*/
		public function moviePoster($idMovie)
		{
			$posters = $this->movieInfo($idMovie,"images",false);
			$posters =$posters['posters'];
			return $posters;
		}//end of 

	/**
	* movie Casting
	* http://api.themoviedb.org/3/movie/$id/casts
	* @param array  movieCast
	*/
		public function movieCast($idMovie)
		{
			$castingTmp = $this->movieInfo($idMovie,"casts",false);
			foreach ($castingTmp['cast'] as $castArr){
				$casting[]=$castArr['name']." - ".$castArr['character'];
			}
			return $casting;
		}//end of movieCast

	public function searchPerson($query, $page = 1, $adult = FALSE)
	{
		$params = array(
			'query' => $query,
			'page' => (int) $page,
			'include_adult' => (bool) $adult,
		);
		return $this->_call('search/person', $params);
	}


	/**
	* Movie Info
	* http://api.themoviedb.org/3/movie/$id
	* @param array  movieInfo
	*/
		public function movieInfo($idMovie,$option="",$print=false){
			$option = (empty($option))?"":"/" . $option;
			$params = "movie/" . $idMovie . $option;
			$movie= $this->_call($params,"");
				return $movie;
		}//end of movieInfo

	/**
	* Search Movie
	* http://api.themoviedb.org/3/search/movie?api_keyf&language&query=future
	* @param string  $peopleName
	*/
		public function searchMovie($movieTitle){
			$movieTitle="query=".urlencode($movieTitle);
			return $this->_call("search/movie",$movieTitle,$this->_lang);
		}//end of searchMovie


	/**
	* Get Confuguration of API
	* configuration	
	* http://api.themoviedb.org/3/configuration?apikey
	* @return array
	*/
		public function getConfig() {
			return $this->_call("configuration","");
		}//end of getConfig

	/**
	* Latest Movie
	* http://api.themoviedb.org/3/latest/movie?api_key
	* @return array
	*/
		public function latestMovie() {
			return $this->_call('latest/movie','');
		}
	/**
	* Now Playing Movies
	* http://api.themoviedb.org/3/movie/now-playing?api_key&language&page
	* @param integer $page
	*/
	public function nowPlayingMovies($page=1) {
		return $this->_call('movie/now-playing', 'page='.$page);
	}

	/**
	 * Makes the call to the API
	 *
	 * @param string $action	API specific function name for in the URL
	 * @param string $text		Unencoded paramter for in the URL
	 * @return string
	 */
		private function _call($action,$text,$lang=""){
		// # http://api.themoviedb.org/3/movie/11?api_key=XXX
			$lang=(empty($lang))?$this->getLang():$lang;
			$url= TMDBv3::_API_URL_.$action."?include_adult=true&api_key=".$this->getApikey()."&language=".$lang."&".$text;
			// echo "<pre>$url</pre>";
			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FAILONERROR, 1);

			$results = curl_exec($ch);
			$headers = curl_getinfo($ch);

			$error_number = curl_errno($ch);
			$error_message = curl_error($ch);

			curl_close($ch);
			// header('Content-Type: text/html; charset=iso-8859-1');
			//echo"<pre>";print_r(($results));echo"</pre>";
			$results = json_decode(($results),true);
			return (array) $results;
		}//end of _call


} //end of class

class TMDb
{
	const POST = 'post';
	const GET = 'get';
	const HEAD = 'head';
	const IMAGE_BACKDROP = 'backdrop';
	const IMAGE_POSTER = 'poster';
	const IMAGE_PROFILE = 'profile';
	const API_VERSION = '3';
	const API_URL = 'api.themoviedb.org';
	const API_SCHEME = 'http://';
	const API_SCHEME_SSL = 'https://';
	const VERSION = '1.5.0';
	/**
	 * The API-key
	 *
	 * @var string
	 */
	protected $_apikey;
	/**
	 * The default language
	 *
	 * @var string
	 */
	protected $_lang;
	/**
	 * The TMDb-config
	 *
	 * @var object
	 */
	protected $_config;
	/**
	 * Stored Session Id
	 *
	 * @var string
	 */
	protected $_session_id;
	/**
	 * API Scheme
	 *
	 * @var string
	 */
	protected $_apischeme;
	/**
	 * Default constructor
	 *
	 * @param string $apikey			API-key recieved from TMDb
	 * @param string $defaultLang		Default language (ISO 3166-1)
	 * @param boolean $config			Load the TMDb-config
	 * @return void
	 */
	public function __construct($apikey, $default_lang = 'en', $config = FALSE, $scheme = TMDb::API_SCHEME)
	{
		$this->_apikey = (string) $apikey;
		$this->_apischeme = ($scheme == TMDb::API_SCHEME) ? TMDb::API_SCHEME : TMDb::API_SCHEME_SSL;
		$this->setLang($default_lang);
		if($config === TRUE)
		{
			$this->getConfiguration();
		}
	}
	/**
	 * Search a movie by querystring
	 *
	 * @param string $text				Query to search after in the TMDb database
	 * @param int $page					Number of the page with results (default first page)
	 * @param bool $adult				Whether of not to include adult movies in the results (default FALSE)
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function searchMovie($query, $page = 1, $adult = FALSE, $year = NULL, $lang = NULL)
	{
		$params = array(
			'query' => $query,
			'page' => (int) $page,
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
			'include_adult' => (bool) $adult,
			'year' => $year,
		);
		return $this->_makeCall('search/movie', $params);
	}
	/**
	 * Search a person by querystring
	 *
	 * @param string $text				Query to search after in the TMDb database
	 * @param int $page					Number of the page with results (default first page)
	 * @param bool $adult				Whether of not to include adult movies in the results (default FALSE)
	 * @return TMDb result array
	 */
	public function searchPerson($query, $page = 1, $adult = FALSE)
	{
		$params = array(
			'query' => $query,
			'page' => (int) $page,
			'include_adult' => (bool) $adult,
		);
		return $this->_makeCall('search/person', $params);
	}
	/**
	 * Search a company by querystring
	 *
	 * @param string $text				Query to search after in the TMDb database
	 * @param int $page					Number of the page with results (default first page)
	 * @return TMDb result array
	 */
	public function searchCompany($query, $page = 1)
	{
		$params = array(
			'query' => $query,
			'page' => $page,
		);
		return $this->_makeCall('search/company', $params);
	}
	/**
	 * Retrieve information about a collection
	 *
	 * @param int $id					Id from a collection (retrieved with getMovie)
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getCollection($id, $lang = NULL)
	{
		$params = array(
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('collection/'.$id, $params);
	}
	/**
	 * Retrieve all basic information for a particular movie
	 *
	 * @param mixed $id					TMDb-id or IMDB-id
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getMovie($id, $lang = NULL)
	{
		$params = array(
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('movie/'.$id, $params);
	}
	/**
	 * Retrieve alternative titles for a particular movie
	 *
	 * @param mixed $id					TMDb-id or IMDB-id
	 * @params string $country			Only include titles for a particular country (ISO 3166-1)
	 * @return TMDb result array
	 */
	public function getMovieTitles($id, $country = NULL)
	{
		$params = array(
			'country' => $country,
		);
		return $this->_makeCall('movie/'.$id.'/alternative_titles', $params);
	}
	/**
	 * Retrieve all of the movie cast information for a particular movie
	 *
	 * @param mixed $id					TMDb-id or IMDB-id
	 * @return TMDb result array
	 */
	public function getMovieCast($id)
	{
		return $this->_makeCall('movie/'.$id.'/casts');
	}
	/**
	 * Retrieve all of the keywords for a particular movie
	 *
	 * @param mixed $id					TMDb-id or IMDB-id
	 * @return TMDb result array
	 */
	public function getMovieKeywords($id)
	{
		return $this->_makeCall('movie/'.$id.'/keywords');
	}
	/**
	 * Retrieve all the release and certification data for a particular movie
	 *
	 * @param mixed $id					TMDb-id or IMDB-id
	 * @return TMDb result array
	 */
	public function getMovieReleases($id)
	{
		return $this->_makeCall('movie/'.$id.'/releases');
	}
	/**
	 * Retrieve available translations for a particular movie
	 *
	 * @param mixed $id					TMDb-id or IMDB-id
	 * @return TMDb result array
	 */
	public function getMovieTranslations($id)
	{
		return $this->_makeCall('movie/'.$id.'/translations');
	}
	/**
	 * Retrieve available trailers for a particular movie
	 *
	 * @param mixed $id					TMDb-id or IMDB-id
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getMovieTrailers($id, $lang = NULL)
	{
		$params = array(
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('movie/'.$id.'/trailers', $params);
	}
	/**
	 * Retrieve all images for a particular movie
	 *
	 * @param mixed $id					TMDb-id or IMDB-id
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getMovieImages($id, $lang = NULL)
	{
		$params = array(
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('movie/'.$id.'/images', $params);
	}
	/**
	 * Retrieve similar movies for a particular movie
	 *
	 * @param mixed $id					TMDb-id or IMDB-id
	 * @param int $page					Number of the page with results (default first page)
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getSimilarMovies($id, $page = 1, $lang = NULL)
	{
		$params = array(
			'page' => (int) $page,
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('movie/'.$id.'/similar_movies', $params);
	}
	/**
	 * Retrieve newest movie added to TMDb
	 *
	 * @return TMDb result array
	 */
	public function getLatestMovie()
	{
		return $this->_makeCall('movie/latest');
	}
	/**
	 * Retrieve movies arriving to theatres within the next few weeks
	 *
	 * @param int $page					Number of the page with results (default first page)
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getUpcomingMovies($page = 1, $lang = NULL)
	{
		$params = array(
			'page' => (int) $page,
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('movie/upcoming', $params);
	}
	/**
	 * Retrieve movies currently in theatres
	 *
	 * @param int $page					Number of the page with results (default first page)
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getNowPlayingMovies($page = 1, $lang = NULL)
	{
		$params = array(
			'page' => (int) $page,
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('movie/now_playing', $params);
	}
	/**
	 * Retrieve popular movies (list is updated daily)
	 *
	 * @param int $page					Number of the page with results (default first page)
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getPopularMovies($page = 1, $lang = NULL)
	{
		$params = array(
			'page' => (int) $page,
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('movie/popular', $params);
	}
	/**
	 * Retrieve top-rated movies
	 *
	 * @param int $page					Number of the page with results (default first page)
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getTopRatedMovies($page = 1, $lang = NULL)
	{
		$params = array(
			'page' => (int) $page,
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('movie/top_rated', $params);
	}
	/**
	 * Retrieve changes for a particular movie
	 *
	 * @param mixed $id					TMDb-id or IMDB-id
	 * @return TMDb result array
	 */
	public function getMovieChanges($id)
	{
		return $this->_makeCall('movie/'.$id.'/changes');
	}
	/**
	 * Retrieve all id's from changed movies
	 *
	 * @param int $page					Number of the page with results (default first page)
	 * @param string $start_date		String start date as YYYY-MM-DD
	 * @param string $end_date			String end date as YYYY-MM-DD (not inclusive)
	 * @return TMDb result array
	 */
	public function getChangedMovies($page = 1, $start_date = NULL, $end_date = NULL)
	{
		$params = array(
			'page' => (int) $page,
			'start_date' => $start_date,
			'end_date' => $end_date,
		);
		return $this->_makeCall('movie/changes', $params);
	}
	/**
	 * Retrieve all basic information for a particular person
	 *
	 * @param int $id					TMDb person-id
	 * @return TMDb result array
	 */
	public function getPerson($id)
	{
		return $this->_makeCall('person/'.$id);
	}
	/**
	 * Retrieve all cast and crew information for a particular person
	 *
	 * @param int $id					TMDb person-id
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getPersonCredits($id, $lang = NULL)
	{
		$params = array(
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('person/'.$id.'/credits', $params);
	}
	/**
	 * Retrieve all images for a particular person
	 *
	 * @param mixed $id					TMDb person-id
	 * @return TMDb result array
	 */
	public function getPersonImages($id)
	{
		return $this->_makeCall('person/'.$id.'/images');
	}
	/**
	 * Retrieve changes for a particular person
	 *
	 * @param mixed $id					TMDb person-id
	 * @return TMDb result array
	 */
	public function getPersonChanges($id)
	{
		return $this->_makeCall('person/'.$id.'/changes');
	}
	/**
	 * Retrieve all id's from changed persons
	 *
	 * @param int $page					Number of the page with results (default first page)
	 * @param string $start_date		String start date as YYYY-MM-DD
	 * @param string $end_date			String end date as YYYY-MM-DD (not inclusive)
	 * @return TMDb result array
	 */
	public function getChangedPersons($page = 1, $start_date = NULL, $end_date = NULL)
	{
		$params = array(
			'page' => (int) $page,
			'start_date' => $start_date,
			'start_date' => $end_date,
		);
		return $this->_makeCall('person/changes', $params);
	}
	/**
	 * Retrieve all basic information for a particular production company
	 *
	 * @param int $id					TMDb company-id
	 * @return TMDb result array
	 */
	public function getCompany($id)
	{
		return $this->_makeCall('company/'.$id);
	}
	/**
	 * Retrieve movies for a particular production company
	 *
	 * @param int $id					TMDb company-id
	 * @param int $page					Number of the page with results (default first page)
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getMoviesByCompany($id, $page = 1, $lang = NULL)
	{
		$params = array(
			'page' => (int) $page,
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('company/'.$id.'/movies', $params);
	}
	/**
	 * Retrieve a list of genres used on TMDb
	 *
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getGenres($lang = NULL)
	{
		$params = array(
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('genre/list', $params);
	}
	/**
	 * Retrieve movies for a particular genre
	 *
	 * @param int $id					TMDb genre-id
	 * @param int $page					Number of the page with results (default first page)
	 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use FALSE to retrieve results from all languages
	 * @return TMDb result array
	 */
	public function getMoviesByGenre($id, $page = 1, $lang = NULL)
	{
		$params = array(
			'page' => (int) $page,
			'language' => ($lang !== NULL) ? $lang : $this->getLang(),
		);
		return $this->_makeCall('genre/'.$id.'/movies', $params);
	}
	/**
	 * Authentication: retrieve authentication token
	 * More information about the authentication process: http://help.themoviedb.org/kb/api/user-authentication
	 *
	 * @return TMDb result array
	 */
	public function getAuthToken()
	{
		$result = $this->_makeCall('authentication/token/new');
		if( ! isset($result['request_token']))
		{
			if($this->getDebugMode())
			{
				throw new TMDbException('No valid request token from TMDb');
			}
			else
			{
				return FALSE;
			}
		}
		return $result;
	}
	/**
	 * Authentication: retrieve authentication session and set it to the class
	 * More information about the authentication process: http://help.themoviedb.org/kb/api/user-authentication
	 *
	 * @param string $token
	 * @return TMDb result array
	 */
	public function getAuthSession($token)
	{
		$params = array(
			'request_token' => $token,
		);
		$result = $this->_makeCall('authentication/session/new', $params);
		if(isset($result['session_id']))
		{
			$this->setAuthSession($result['session_id']);
		}
		return $result;
	}
	/**
	 * Authentication: set retrieved session id in the class for authenticated requests
	 * More information about the authentication process: http://help.themoviedb.org/kb/api/user-authentication
	 *
	 * @param string $session_id
	 */
	public function setAuthSession($session_id)
	{
		$this->_session_id = $session_id;
	}
	/**
	 * Retrieve basic account information
	 *
	 * @param string $session_id		Set session_id for the account you want to retrieve information from
	 * @return TMDb result array
	 */
	public function getAccount($session_id = NULL)
	{
		$session_id = ($session_id === NULL) ? $this->_session_id : $session_id;
		return $this->_makeCall('account', NULL, $session_id);
	}
	/**
	 * Retrieve favorite movies for a particular account
	 *
	 * @param int $account_id			TMDb account-id
	 * @param string $session_id		Set session_id for the account you want to retrieve information from
	 * @param int $page					Number of the page with results (default first page)
	 * @param mixed $lang				Get result in other language then default for this user account (ISO 3166-1)
	 * @return TMDb result array
	 */
	public function getAccountFavoriteMovies($account_id, $session_id = NULL, $page = 1, $lang = FALSE)
	{
		$session_id = ($session_id === NULL) ? $this->_session_id : $session_id;
		$params = array(
			'page' => (int) $page,
			'language' => ($lang !== NULL) ? $lang : '',
		);
		return $this->_makeCall('account/'.$account_id.'/favorite_movies', $params, $session_id);
	}
	/**
	 * Retrieve rated movies for a particular account
	 *
	 * @param int $account_id			TMDb account-id
	 * @param string $session_id		Set session_id for the account you want to retrieve information from
	 * @param int $page					Number of the page with results (default first page)
	 * @param mixed $lang				Get result in other language then default for this user account (ISO 3166-1)
	 * @return TMDb result array
	 */
	public function getAccountRatedMovies($account_id, $session_id = NULL, $page = 1, $lang = FALSE)
	{
		$session_id = ($session_id === NULL) ? $this->_session_id : $session_id;
		$params = array(
			'page' => (int) $page,
			'language' => ($lang !== NULL) ? $lang : '',
		);
		return $this->_makeCall('account/'.$account_id.'/rated_movies', $params, $session_id);
	}
	/**
	 * Retrieve movies that have been marked in a particular account watchlist
	 *
	 * @param int $account_id			TMDb account-id
	 * @param string $session_id		Set session_id for the account you want to retrieve information from
	 * @param int $page					Number of the page with results (default first page)
	 * @param mixed $lang				Get result in other language then default for this user account (ISO 3166-1)
	 * @return TMDb result array
	 */
	public function getAccountWatchlistMovies($account_id, $session_id = NULL, $page = 1, $lang = FALSE)
	{
		$session_id = ($session_id === NULL) ? $this->_session_id : $session_id;
		$params = array(
			'page' => (int) $page,
			'language' => ($lang !== NULL) ? $lang : '',
		);
		return $this->_makeCall('account/'.$account_id.'/movie_watchlist', $params, $session_id);
	}
	/**
	 * Add a movie to the account favorite movies
	 *
	 * @param int $account_id			TMDb account-id
	 * @param string $session_id		Set session_id for the account you want to retrieve information from
	 * @param int $movie_id				TMDb movie-id
	 * @param bool $favorite			Add to favorites or remove from favorites (default TRUE)
	 * @return TMDb result array
	 */
	public function addFavoriteMovie($account_id, $session_id = NULL, $movie_id = 0, $favorite = TRUE)
	{
		$session_id = ($session_id === NULL) ? $this->_session_id : $session_id;
		$params = array(
			'movie_id' => (int) $movie_id,
			'favorite' => (bool) $favorite,
		);
		return $this->_makeCall('account/'.$account_id.'/favorite', $params, $session_id, TMDb::POST);
	}
	/**
	 * Add a movie to the account watchlist
	 *
	 * @param int $account_id			TMDb account-id
	 * @param string $session_id		Set session_id for the account you want to retrieve information from
	 * @param int $movie_id				TMDb movie-id
	 * @param bool $watchlist			Add to watchlist or remove from watchlist (default TRUE)
	 * @return TMDb result array
	 */
	public function addMovieToWatchlist($account_id, $session_id = NULL, $movie_id = 0, $watchlist = TRUE)
	{
		$session_id = ($session_id === NULL) ? $this->_session_id : $session_id;
		$params = array(
			'movie_id' => (int) $movie_id,
			'movie_watchlist' => (bool) $watchlist,
		);
		return $this->_makeCall('account/'.$account_id.'/movie_watchlist', $params, $session_id, TMDb::POST);
	}
	/**
	 * Add a rating to a movie
	 *
	 * @param string $session_id		Set session_id for the account you want to retrieve information from
	 * @param int $movie_id				TMDb movie-id
	 * @param float $value				Value between 1 and 10
	 * @return TMDb result array
	 */
	public function addMovieRating($session_id = NULL, $movie_id = 0, $value = 0)
	{
		$session_id = ($session_id === NULL) ? $this->_session_id : $session_id;
		$params = array(
			'value' => is_numeric($value) ? floatval($value) : 0,
		);
		return $this->_makeCall('movie/'.$movie_id.'/rating', $params, $session_id, TMDb::POST);
	}
	/**
	 * Get configuration from TMDb
	 *
	 * @return TMDb result array
	 */
	public function getConfiguration()
	{
		$config = $this->_makeCall('configuration');
		if( ! empty($config))
		{
			$this->setConfig($config);
		}
		return $config;
	}
	/**
	 * Get Image URL
	 *
	 * @param string $filepath			Filepath to image
	 * @param const $imagetype			Image type: TMDb::IMAGE_BACKDROP, TMDb::IMAGE_POSTER, TMDb::IMAGE_PROFILE
	 * @param string $size				Valid size for the image
	 * @return string
	 */
	public function getImageUrl($filepath, $imagetype, $size)
	{
		$config = $this->getConfig();
		if(isset($config['images']))
		{
			$base_url = $config['images']['base_url'];
			$available_sizes = $this->getAvailableImageSizes($imagetype);
			if(in_array($size, $available_sizes))
			{
				return $base_url.$size.$filepath;
			}
			else
			{
				throw new TMDbException('The size "'.$size.'" is not supported by TMDb');
			}
		}
		else
		{
			throw new TMDbException('No configuration available for image URL generation');
		}
	}
	/**
	 * Get available image sizes for a particular image type
	 *
	 * @param const $imagetype			Image type: TMDb::IMAGE_BACKDROP, TMDb::IMAGE_POSTER, TMDb::IMAGE_PROFILE
	 * @return array
	 */
	public function getAvailableImageSizes($imagetype)
	{
		$config = $this->getConfig();
		if(isset($config['images'][$imagetype.'_sizes']))
		{
			return $config['images'][$imagetype.'_sizes'];
		}
		else
		{
			throw new TMDbException('No configuration available to retrieve available image sizes');
		}
	}
	/**
	 * Get ETag to keep track of state of the content
	 *
	 * @param string $uri				Use an URI to know the version of it. For example: 'movie/550'
	 * @return string
	 */
	public function getVersion($uri)
	{
		$headers = $this->_makeCall($uri, NULL, NULL, TMDb::HEAD);
		return isset($headers['Etag']) ? $headers['Etag'] : '';
	}
	/**
	 * Makes the call to the API
	 *
	 * @param string $function			API specific function name for in the URL
	 * @param array $params				Unencoded parameters for in the URL
	 * @param string $session_id		Session_id for authentication to the API for specific API methods
	 * @param const $method				TMDb::GET or TMDb:POST (default TMDb::GET)
	 * @return TMDb result array
	 */
	private function _makeCall($function, $params = NULL, $session_id = NULL, $method = TMDb::GET)
	{
		$params = ( ! is_array($params)) ? array() : $params;
		$auth_array = array('api_key' => $this->_apikey);
		if($session_id !== NULL)
		{
			$auth_array['session_id'] = $session_id;
		}
		$url = $this->_apischeme.TMDb::API_URL.'/'.TMDb::API_VERSION.'/'.$function.'?'.http_build_query($auth_array, '', '&');
		if($method === TMDb::GET)
		{
			if(isset($params['language']) AND $params['language'] === FALSE)
			{
				unset($params['language']);
			}
			$url .= ( ! empty($params)) ? '&'.http_build_query($params, '', '&') : '';
		}
		$results = '{}';
		if (extension_loaded('curl'))
		{
			$headers = array(
				'Accept: application/json',
			);
			$ch = curl_init();
			if($method == TMDB::POST)
			{
				$json_string = json_encode($params);
				curl_setopt($ch,CURLOPT_POST, 1);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $json_string);
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Content-Length: '.strlen($json_string);
			}
			elseif($method == TMDb::HEAD)
			{
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
				curl_setopt($ch, CURLOPT_NOBODY, 1);
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			$body = substr($response, $header_size);
			$error_number = curl_errno($ch);
			$error_message = curl_error($ch);
			if($error_number > 0)
			{
				throw new TMDbException('Method failed: '.$function.' - '.$error_message);
			}
			curl_close($ch);
		}
		else
		{
			throw new TMDbException('CURL-extension not loaded');
		}
		$results = json_decode($body, TRUE);
		if(strpos($function, 'authentication/token/new') !== FALSE)
		{
			$parsed_headers = $this->_http_parse_headers($header);
			$results['Authentication-Callback'] = $parsed_headers['Authentication-Callback'];
		}
		if($results !== NULL)
		{
			return $results;
		}
		elseif($method == TMDb::HEAD)
		{
			return $this->_http_parse_headers($header);
		}
		else
		{
			throw new TMDbException('Server error on "'.$url.'": '.$response);
		}
	}
	/**
	 * Setter for the default language
	 *
	 * @param string $lang		(ISO 3166-1)
	 * @return void
	 */
	public function setLang($lang)
	{
		$this->_lang = $lang;
	}
	/**
	 * Setter for the TMDB-config
	 *
	 * $param array $config
	 * @return void
	 */
	public function setConfig($config)
	{
		$this->_config = $config;
	}
	/**
	 * Getter for the default language
	 *
	 * @return string
	 */
	public function getLang()
	{
		return $this->_lang;
	}
	/**
	 * Getter for the TMDB-config
	 *
	 * @return array
	 */
	public function getConfig()
	{
		if(empty($this->_config))
		{
			$this->_config = $this->getConfiguration();
		}
		return $this->_config;
	}
	/*
	 * Internal function to parse HTTP headers because of lack of PECL extension installed by many
	 *
	 * @param string $header
	 * @return array
	 */
	protected function _http_parse_headers($header)
	{
		$return = array();
		$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
		foreach($fields as $field)
		{
			if(preg_match('/([^:]+): (.+)/m', $field, $match))
			{
				$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
				if( isset($return[$match[1]]) )
				{
					$return[$match[1]] = array($return[$match[1]], $match[2]);
				}
				else
				{
					$return[$match[1]] = trim($match[2]);
				}
			}
		}
		return $return;
	}
}
/**
 * TMDb Exception class
 *
 * @author Jonas De Smet - Glamorous
 */
class TMDbException extends Exception{}
?>