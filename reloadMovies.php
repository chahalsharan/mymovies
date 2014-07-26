<?php require_once "simple_html_dom.php" ?>
<?php require_once 'mysqlidb.php';?>
<?php require_once 'databaseConfig.php' ?>
<?php require_once 'Movie.php' ?>
<?php require_once 'RottenData.php' ?>
<?php
	# http://localhost/~sharandc/mymovies/reloadMovies.php?loadMovies=1&fromPage=10&toPage=1&fromYear=2000&toYear=2014&reloadRottenData=1
	session_start();
	if(!(isset($_SESSION['type']) && $_SESSION['type'] == "admin")){
		error_log("DEBUG: Not admin");
		echo "Not Authorized";
		http_response_code(403);
		exit();
	}
	if (isset($_GET['reloadMovieLinks'])) {
		$db = getMysqlConnection();

		$movies = array();
		if (isset($_GET['movieId'])) {
			$id = $_GET['movieId'];
			$movies[$id] = Movie::loadFromDb($db, $id);
		} else {
			$movies = Movie::loadAllFromDB($db);
		}

		foreach ($movies as $movie)  {
			error_log("DEBUG: going to load watching linnk for movie:" . $movie->id);
			$movie = loadMovieLinksSynopsisAndGeneralRating($db, $movie);
            if(! isset($movie->watch)){
                echo "Link found for movie " . $movie->name . " \n<br/>";
            }
		}
        http_response_code(200);
		closeMysqlConnection($db);
	}
	
	if (getRequestVariable('loadMovies') != FALSE) {
		echo $fromPage = getRequestVariable('fromPage');
		echo $toPage = getRequestVariable('toPage');
		echo $fromYear = getRequestVariable('fromYear');
		echo $toYear = getRequestVariable('toYear');

		if (isset($fromPage) && isset($toPage) 
			&& isset($fromYear) && isset($toYear)) {
			$db = getMysqlConnection();
			loadMovies($db, $fromPage, $toPage, (int)$fromYear, (int)$toYear);
			closeMysqlConnection($db);
		}else {
			echo "fromPage, toPage not set";
			http_response_code(500);
		}
	}

	if (isset($_GET['reloadRottenData'])) {
		loadRottenData();
	}

	function loadRottenData(){
		$db = getMysqlConnection();

		$movies = array();
		if (isset($_GET['movieId'])) {
			$id = $_GET['movieId'];
			$movies[$id] = Movie::loadFromDb($db, $id);
		} else {
			$movies = Movie::loadAllFromDB($db);
		}
		$rots = RottenData::loadAllFromDB($db);
		foreach ($movies as $movie)  {
			error_log("DEBUG: going to load rottend for movie: " . $movie->id);
			if (isset($rots[$movie->id])) {
				loadMovieRottenData($db, $movie, $rots[$movie->id]);
			} else {
                if(! isUpdatedSinceHours($movie->lastUpdated, 24)){
				    loadMovieRottenData($db, $movie);
                }else{
                    error_log("DEBUG: loaded recently **skipping**:" . $movie->id);
                }
			}
			sleep(1);
		}
		closeMysqlConnection($db);
	}

	function getRequestVariable($variableName) {
		if (isset($_POST[$variableName])) {
			return $_POST[$variableName];
		}else if (isset($_GET[$variableName])) {
			return $_GET[$variableName];
		}
		return;
	}

	function extractMovieLinksAndSynopsis($movie) {
		$link = urldecode($movie->link);
		error_log("DEBUG: LOAD_LINK: going to load link page: " . $link);
		$linksRawData = loadHTML($link);
		if (!isset($link)) {
			error_log("ERROR: LOAD_LINK: timed out wile extracting movie link: " + $link);
			return;
		}
		$html = str_get_html($linksRawData);
		if (! isset($html)) {
			rror_log("ERROR: LOAD_LINK:  can not parse links html for link: " + $link);
			return;
		}

		$watchingLink = findMovieLinks($html);
		if (isset($watchingLink)) {
			$movie->watch = $watchingLink;
		
    		$synopsis = findMovieSynopsis($html);
    		if (isset($synopsis)) {
    			$movie->synopsis = $synopsis;
    		}

    		$generalRating = findMovieRating($html);
    		if (isset($generalRating)) {
    			if (! isset($movie->rating)) {
    				$movie->rating = new Rating();
    			}
    			$movie->rating->general = (float)$generalRating;
    		}
        }
		return $movie;
	}

	function findMovieLinks($html) {
		$link = FALSE;
		if (isset($html)) {
			foreach ($html->find(".movie_version_alt", 0)->find("tr") as $row) {
				$dvdLink = $row->find(".quality_dvd", 0);
				if (isset($dvdLink)) {
					error_log("DEBUG: found dvd quality link");
					$linkData = $row->find('.movie_version_link', 0);
					$link = $linkData->find("a", 0);
					$link = "http://www.watchfreemovies.ch". $link->href;
					break;
				}
			}
		}else {
			echo "connection timed out";
		}
		return $link;
	}

	function findMovieSynopsis($html) {
		if (isset($html)) {
			foreach ($html->find(".movie_info") as $movieInfo) {
				$summary = strip_tags($movieInfo->find("p", 0)->find('text', 1));
				$summary = substr($summary, 2);
				return $summary;
			}
		}
	}

	function findMovieRating($html) {
		if (isset($html)) {
			foreach ($html->find(".movie_info") as $movieInfo) {

				#echo "rating:" . $rating = $movieInfo->find(".current-rating", 0)->find('span', 0)->find('text', 0);
				$rating = $movieInfo->find(".current-rating", 0)->find('span', 0)->find('text', 0);
				return strip_tags($rating);
			}
		}
	}

	function infiniteLoopBreak($index) {
		if ($index > 10) {
			echo "%%%%%%%%%%%%%%%%%%%%% Infinite loop detected &&&&&&&&&&&&&&&&&&";
			exit();
		}
	}

	function loadMovieLinksSynopsisAndGeneralRating($db, $movie){
		$movie = Movie::loadFromDb($db, $movie->id);
        if(! isset($movie->watch)){
            // check if the movie has been updated in last 24 hours
            if(! isUpdatedSinceHours($movie->lastUpdated, 24)){
                $movie = extractMovieLinksAndSynopsis($movie);
                error_log("DEBUG: LOAD_LINK: Loaded movie watch link for " . $movie->id);
                $movie->saveOrUpdate($db);
                sleep(1);
            }else{
                error_log("DEBUG: movie updated recently ** skipping **:" . $movie->id);
            }
        }else{
            error_log("DEBUG: LOAD_LINK: Movie watch link laredy present for " . $movie->id);
        }
        return $movie;
	}

    function isUpdatedSinceHours($var, $hours){
        return (time()-(60*60*$hours)) < strtotime($var);
    }

	function loadMovies($db, $fromPage, $toPage, $fromYear, $toYear) {

		$startPage = $fromPage;
		$endPage = $toPage;
		echo $startPage. "-- " . $endPage;
        $retryCount = 0;
        $retryMax = 3;
		while($startPage <= $endPage) {
			
			error_log("DEBUG: going to load for page:" . $startPage);

			$url = 'http://www.watchfreemovies.ch/watch-movies/page-'. $startPage .'/';
			error_log("DEBUG: Going to load movies page: " . $url);
			$rawHtml = loadHTML('http://www.watchfreemovies.ch/watch-movies/page-'. $startPage .'/');
			if (!isset($rawHtml)) {
				error_log("Times out while loading page: " . $startPage);
				$startPage++;
				continue;
			}
			$html = str_get_html($rawHtml);
			if (!isset($html) || !$html) {
				error_log("Error parsing HTML for page: " . $startPage);

                if($retryCount >= $retryMax){
				    $startPage++;
                    $retryCount = 0;
                }else{
                    $retryCount ++;
                }
				continue;
			}
			
			foreach ($html->find('.index_item_ie') as $item) {
				
				
				$movieTitle = ($item->find('a', 0)->title);
				$movieYear = (substr($movieTitle, -5, -1));
				$movieYear = (int)$movieYear;
				if ($movieYear >= $fromYear && $movieYear <= $toYear) {

					$movieLink = ($item->find('a', 0)->href);
					$movieTitle = substr($movieTitle, 6, -7);

					$movieId = Movie::existsInDb($db, $movieTitle);
					if (! isset($movieId)) {
						error_log("DEBUG: movie doe not exist:" . $movieTitle);
						$movieThumbnail = "http://www.silvermorgandollar.com/images/no_image.gif";
						foreach ($item->find('img') as $image) {
							if (strpos($image->src, "thumbs") != false) {
								$movieThumbnail = $image->src;
								break;
							}
						}
					
						$movie = new Movie();
						$movie->name = $movieTitle;
						$movie->link = $movieLink;
						$movie->year = $movieYear;
						$movie->thumbnail = $movieThumbnail;
						
						foreach ($item->find('.item_categories', 0)->find('a') as $category)  {
							$cat = $category->find('text', 0);
							array_push($movie->categories, strip_tags($cat));							
						}

						//$movie = extractMovieLinksAndSynopsis($movie);
						$movie->save($db);
					}else {
						error_log("DEBUG: movie exists:" . $movieTitle);
						$movie = Movie::loadFromDb($db, $movieId);
					}
					echo "<pre>Movie "; print_r($movie); echo "</pre>";
				}
			}
			sleep(2);
			$startPage ++;
		}
	}

	function loadHTML($url) {
		if ($url == false || empty($url)) 
			return false;

	    $options = array(
	    	CURLOPT_URL            => $url,     // URL of the page
	    	CURLOPT_RETURNTRANSFER => true,     // return web page
	    	CURLOPT_HEADER         => false,    // don't return headers
	    	CURLOPT_FOLLOWLOCATION => false,     // follow redirects
	    	CURLOPT_ENCODING       => "",       // handle all encodings
	    	CURLOPT_USERAGENT      => "spider", // who am i
	    	CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	    	CURLOPT_CONNECTTIMEOUT => 2,      // timeout on connect
	    	CURLOPT_TIMEOUT        => 2,      // timeout on response
	    	CURLOPT_MAXREDIRS      => 3,       // stop after 3 redirects
	    );

	    $ch      = curl_init( $url );
	    curl_setopt_array( $ch, $options );
	    $content = curl_exec( $ch );
	    $header  = curl_getinfo( $ch );
	    curl_close( $ch );

	    //Ending all that cURL mess...


	    //Removing linebreaks,multiple whitespace and tabs for easier Regexing
	    #$content = str_replace(array("\n", "\r", "\t", "\o", "\xOB"), '', $content);
	    #$content = preg_replace('/\s\s+/', ' ', $content);
	    #$this->profilehtml = $content;
	    return $content;
	}

	function loadMovieRottenData($db, $movie, $rotten = NULL) {
		$twoDayInterval = (24 * 60 * 60);
		
		if (isset($rotten)){
			error_log("DEBUG: rottne data exists for movie:" . $movie->id);
			$rottenInterval = floor((time() - strtotime($rotten->loadDate))/$twoDayInterval);
			if($rottenInterval < 2)  {
				error_log("DEBUG: Skipping rotten data reload as it was loaded recently, for movie: " . $movie->id);
				return;
			}
		}
		$url = "http://api.rottentomatoes.com/api/public/v1.0/movies.json?apikey=kvdsxtvpapb8ywawa2dntuvx&q=" .
			urlencode($movie->name) . "+" . $movie->year . 
			"&page_limit=1";
		error_log("DEBUG: Going to load rotten data for movie: " . $movie->id . 
			", url: " . $url);

		$jsonData = loadHTML($url);
		if (!isset($jsonData)) {
			error_log("Timed out from rotten for movie: " . $movie->id);
			return;
		}
		$rottenData = json_decode($jsonData, true);
		if (!isset($rottenData) and $rottenData['total'] == 0) {
			error_log("Error parsing JSON from rotten response for movie: : " . $movie->id);
			return;
		}
		error_log("DEBUG:Going to process rotten data for movie: " . $movie->id);

		if(!isset($rotten)){
			$rotten = new RottenData();
		}
		$movieRotternData = $rottenData['movies'][0];

		$rotten->rottenReferenceId = $movieRotternData['id'];
		$rotten->title = $movieRotternData['title'];
		$rotten->movieId = $movie->id;
		if(isset($movieRotternData['ratings'])) {
			if(isset($movieRotternData['ratings']['critics_rating'])){
				$rotten->criticsRating = $movieRotternData['ratings']['critics_rating'];
			}
			if(isset($movieRotternData['ratings']['critics_score'])){
				$rotten->criticsScore = $movieRotternData['ratings']['critics_score'];
			}			
			if(isset($movieRotternData['ratings']['audience_score'])){
				$rotten->audienceScore = $movieRotternData['ratings']['audience_score'];
			}
			if(isset($movieRotternData['ratings']['audience_rating'])){
				$rotten->audienceRating = $movieRotternData['ratings']['audience_rating'];
			}
		}
		if(isset($movieRotternData['release_dates'])){
			if(isset($movieRotternData['release_dates']['theater'])){
				$rotten->releaseDate = $movieRotternData['release_dates']['theater'];
			}
			if(isset($movieRotternData['release_dates']['dvd'])){
				$rotten->dvdReleaseDate = $movieRotternData['release_dates']['dvd'];
			}
		}
		$rotten->thumbnail = $movieRotternData['posters']['thumbnail'];;
		$rotten->synopsis = $movieRotternData['synopsis'];
		$rotten->rottenLink = $movieRotternData['links']['alternate'];
		
		$rotten->saveOrUpdate($db);

		error_log("DEBUG:saved rotten data : " . $rotten->id);

		if (isset($movieRotternData['abridged_cast'])) {
			$movie->actors = extractActors($movieRotternData['abridged_cast']);
			$movie->saveOrUpdate($db);
			error_log("DEBUG:updated movies data (with Actors info) : " . $movie->id);
        }
	}

	function extractActors($rottenActors){
		$actors = array();
		foreach($rottenActors as $rottenActor){
			array_push($actors, $rottenActor['name']);
		}

		return $actors;
	}
?>