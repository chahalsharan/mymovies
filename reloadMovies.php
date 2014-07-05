<?php require_once "simple_html_dom.php" ?>
<?php require_once 'mysqlidb.php';?>
<?php require_once 'databaseConfig.php' ?>
<?php require_once 'Movie.php' ?>
<?php require_once 'RottenData.php' ?>
<?php

	if (isset($_GET['reloadMovieLinks'])) {
		if (isset($_GET['movieId'])) {
			$id = $_GET['movieId'];
		}
		$db = getMysqlConnection();
		$movies = getAllMovies($db, $id);
		foreach ($movies as $id => $movie)  {
			saveMovieLinksAndSynopsis($db, $id, $movie);
			exit();
		}
		closeMysqlConnection($db);
	}

	if (isset($_GET['reloadRottenData'])) {
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
			loadMovieRottenData($db, $movie, $rots[$movie->id]);
		}
		closeMysqlConnection($db);
	}
	
	if (getRequestVariable('loadMovies') == FALSE) {
		$fromPage = getRequestVariable('fromPage');
		$fromPage = getRequestVariable('toPage');
		$fromYear = getRequestVariable('fromYear');
		$toYear = getRequestVariable('toYear');

		if (isset($fromPage) && isset($toPage) 
			&& isset($fromYear) && isset($toYear)) {
			$db = getMysqlConnection();
			loadMovies($db, $fromPage, $toPage, $fromYear, $toYear);
			closeMysqlConnection($db);
		}else {
			echo "fromPage, toPage not set";
			http_response_code(500);
		}
	}

	function getRequestVariable($variableName) {
		if (isset($_POST[$variableName])) {
			return $_POST[$variableName];
		}else if (isset($_GET[$variableName])) {
			return $_GET[$variableName];
		}
		return;
	}

	function extractMovieLinksAndSynopsis($db, $movie) {
		$link = urldecode($movie->link);
		$linksRawData = loadHTML($link);
		if (!isset($link)) {
			error_log("timed out wile extracting movie link: " + $link);
			return;
		}
		$html = str_get_html($linksRawData);
		if (! isset($html)) {
			rror_log("ERROR: can not parse links html for link: " + $link);
			return;
		}

		$watchingLink = findMovieLinks($html);
		if (isset($watchingLink)) {
			$movie->watch = $watchingLink;
		}
		
		$synopsis = findMovieSynopsis($html);
		if (isset($synopsis)) {
			$movie->synopsis = $synopsis;
		}
		$rating = findMovieRating($html);
		if (isset($rating)) {
			if (!isset($movie->rating)) {
				$movie->rating = new Rating();
			}
			$movie->rating->general = $rating;
		}
	}

	function findMovieLinks($html) {
		$link = false;
		if (isset($html)) {

			foreach ($html->find('.movie_version_link') as $linkData) {
				
				$isStarLink = false;
				foreach ($linkData->find("img") as $starLink) {
					if (strpos($starLink->src, "star") != false) {
						$isStarLink = true;
						break;
					}
				}
				if ($isStarLink) {
					foreach ($linkData->find("a") as $link) {
						$link = "http://www.watchfreemovies.ch". $link->href;
						break;
					}
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
				$summary = $movieInfo->find("p", 0)->find('text', 1);
				$summary = substr($summary, 2);
				return $summary;
			}
		}
	}

	function findMovieRating($html) {
		if (isset($html)) {
			foreach ($html->find(".movie_info") as $movieInfo) {
				$rating = $movieInfo->find(".current-rating", 0)->find('span', 0)->find('text', 0);
				return $rating;
			}
		}
	}

	function infiniteLoopBreak($index) {
		if ($index > 10) {
			echo "%%%%%%%%%%%%%%%%%%%%% Infinite loop detected &&&&&&&&&&&&&&&&&&";
			exit();
		}
	}

	function loadMovies($db, $fromPage, $toPage, $fromYear, $toYear) {

		$startPage = $fromPage;
		$endPage = $toPage;
		echo $startPage. "-- " . $endPage;
		while($startPage <= $endPage) {
			
			$rawHtml = loadHTML('http://www.watchfreemovies.ch/watch-movies/page-'. $startPage .'/');
			if (!isset($rawHtml)) {
				error_log("Times out while loading page: " . $startPage);
				$startPage++;
				continue;
			}
			$html = str_get_html($rawHtml);
			if (!isset($html)) {
				error_log("TError parsing HTML for page: " . $startPage);
				$startPage++;
				continue;
			}
			
			foreach ($html->find('.index_item_ie') as $item) {
				
				
				$movieTitle = ($item->find('a', 0)->title);
				$movieYear = (substr($movieTitle, -5, -1));
				if ($year == $movieYear) {

					$movieLink = ($item->find('a', 0)->href);
					$movieTitle = substr($movieTitle, 6, -7);
					$movieThumbnail = "http://www.silvermorgandollar.com/images/no_image.gif";
					foreach ($item->find('img') as $image) {
						if (strpos($image->src, "thumbs") != false) {
							$movieThumbnail = $image->src;
							break;
						}
					}
					
					$movieId = Movie::existsInDb($db, $movieTitle);
					
					if (! isset($movieId)) {
						$movie = new Movie();
						$movie->name = $movieTitle;
						$movie->link = $movieLink;
						$movie->year = $movieYear;
						$movie->thumbnail = $movieThumbnail;
						
						foreach ($item->find('.item_categories', 0)->find('a') as $category)  {
							$cat = $category->find('text', 0);
							array_push($movie->categories, $cat);							
						}
						$movie->save();
					}else {
						$movie = Movie::loadFromDb($db, $movieId);
					}
					extractMovieLinksAndSynopsis($db, $movie);
				}
			}
			sleep(1);
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

	function loadMovieRottenData($db, $movie, $rotten) {
		$twoDayInterval = (24 * 60 * 60);
		$rottenInterval = floor((time() - strtotime($rotten->loadDate))/$twoDayInterval);
		if (isset($rotten)
			&& $rottenInterval < 0)  {
			error_log("DEBUG: Skipping rotten data reload as it was loaded recently, for movie: " . $movie->id);
			return;
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
		$rotten->criticsRating = $movieRotternData['ratings']['critics_rating'];
		$rotten->criticsScore = $movieRotternData['ratings']['critics_score'];
		$rotten->audienceRating = $movieRotternData['ratings']['audience_rating'];
		$rotten->audienceScore = $movieRotternData['ratings']['audience_score'];
		$rotten->audienceScore = $movieRotternData['ratings']['audience_score'];
		$rotten->thumbnail = $movieRotternData['posters']['thumbnail'];;
		$rotten->synopsis = $movieRotternData['synopsis'];
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