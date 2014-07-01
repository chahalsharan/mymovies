<?php require_once "simple_html_dom.php" ?>
<?php require_once 'mysqlidb.php';?>
<?php require_once 'databaseConfig.php' ?>
<?php

	echo "--------";
	$fromPage;
	$toPage;
	if(isset($_POST['fromPage'])){
		$fromPage = $_POST['fromPage'];
	}else if(isset($_GET['fromPage'])){
		$fromPage = $_GET['fromPage'];
	}
	if(isset($_POST['toPage'])){
		$toPage = $_POST['toPage'];
	}else if(isset($_GET['toPage'])){
		$toPage = $_GET['toPage'];
	}

	if(isset($fromPage) && isset($toPage)){
		echo "errerer";
		try{
			loadMovies($db, $fromPage, $toPage);
		}catch(Exception $ex){
			echo $e->getMessage();
			http_response_code(500);
		}
	}else{
		echo "fromPage, toPage not set";
		http_response_code(500);
	}

	function saveMovieLinks($db, $movieId, $link){
		$html = str_get_html(
			loadHTML($link));
		$link = false;
		if($html){
			
			foreach($html->find('.movie_version_link') as $linkData){
				
				$isStarLink = false;
				foreach($linkData->find("img") as $starLink){
					if(strpos($starLink->src, "star") != false){
						$isStarLink = true;
						break;
					}
				}
				if($isStarLink){
					foreach($linkData->find("a") as $link){
						$link = "http://www.watchfreemovies.ch". $link->href;
						break;
					}
				}
			}
		}else{
			echo "connection timed out";
		}

		if($link){
			$data = Array ("movie_id" => $movieId,
						   "link" => urlencode($link),
						   "link_date" => date('Y-m-d'));							
			$db->insert('links', $data);
		}
	}

	function infiniteLoopBreak($index){
		if($index > 10){
			echo "%%%%%%%%%%%%%%%%%%%%% Infinite loop detected &&&&&&&&&&&&&&&&&&";
			exit();
		}
	}

	function loadMovies($db, $fromPage, $toPage){

		$startPage = $fromPage;
		$endPage = $toPage;
		echo $startPage. "-- " . $endPage;
		while($startPage <= $endPage){
			
			
			$html = str_get_html(
				loadHTML('http://www.watchfreemovies.ch/watch-movies/page-'. $startPage .'/'));

			if($html){
				foreach($html->find('.index_item_ie') as $item){
					
					$movieLink = ($item->find('a', 0)->href);
					$movieTitle = ($item->find('h2', 0)->find('text',0));
					#$movieTitle = substr($movieTitle, strpos($movieTitle, "(") -1 );
					$movieYear = (substr($movieTitle, -5, -1));
					$movieThumbnail = "http://www.silvermorgandollar.com/images/no_image.gif";
					foreach($item->find('img') as $image){
						if(strpos($image->src, "thumbs") != false){
							$movieThumbnail = $image->src;
							break;
						}
					}
					
					$dbMovieId = $db	->where("name", urlencode($movieTitle))
									->getOne("movies", "id");
					$id = 0;
					if ($db->count == 0){
						echo "movies doesnt exist";
						$data = Array ("name" => urlencode($movieTitle),
									   "link" => urlencode($movieLink),
									   "year" => urlencode($movieYear),
									   "thumbnail" => urlencode($movieThumbnail)
						);
						$id = $db->insert('movies', $data);
						
						foreach ($item->find('.item_categories', 0)->find('a') as $category) {
							$cat = $category->find('text', 0);
							$data = Array ("movieId" => $id,
										   "category" => urlencode($cat));							
							$db->insert('categories', $data);
						}

					}else{
						echo "movie already exists " . $movieTitle . ", id->". $dbMovieId['id'];
						$id = $dbMovieId['id'];
					}

					if($id > 0){
						$links = $db->where("movie_id", $id)
									->getOne("links", "link_id");
						if ($db->count == 0){
							saveMovieLinks($db, $id, $movieLink);
						}
					}
				}
			}
			sleep(1);
			$startPage ++;
		}
	}

	function loadHTML($url){
		if($url == false || empty($url)) 
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
?>