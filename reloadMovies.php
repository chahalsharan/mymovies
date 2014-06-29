<?php require_once "simple_html_dom.php" ?>
<?php require_once 'mysqlidb.php';?>
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
	
	if(!isset($db)){
		$db = new Mysqlidb('localhost', 'root', '', 'let_me_watch_this');
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

	function saveMovieLinks($db, $movieId $link){
		$html = file_get_html('http://www.watchfreemovies.ch/watch-movies/page-'. $startPage .'/');
		if($html){
			foreach($html->find('.index_item_ie') as $item){
			}
		}
	}

	function saveCategory($db, $movieId, $cats){
		foreach($cats->find('a')->find('text', 0) as $cat){
			$data = Array ("movieId" => $movieId,
						   "category" => urlencode($cat));
			dump($data);
			$db->insert('categories', $data);
		}
	}

	function loadMovies($db, $fromPage, $toPage){

		$startPage = $fromPage;
		$endPage = $toPage;
		echo $startPage. "-- " . $endPage;
		while($startPage <= $endPage){
			
			
			$html = file_get_html('http://www.watchfreemovies.ch/watch-movies/page-'. $startPage .'/');
			if($html){
				
				foreach($html->find('.index_item_ie') as $item){
					$movieLink = ($item->find('a', 0)->href);
					$movieTitle = ($item->find('h2', 0)->find('text',0));
					$movieYear = (substr($movieTitle, -5, -1));
					$movieThumbnail = ($item->find('img', 1)->src);
					$movieId = $db	->where("name", urlencode($movieTitle))
									->getOne("movies", "id");

					echo $movieTitle . " --> " . $movieId['id'];
					
					if ($db->count == 0){
						$data = Array ("name" => urlencode($movieTitle),
									   "link" => urlencode($movieLink),
									   "year" => urlencode($movieYear),
									   "thumbnail" => urlencode($movieThumbnail)
						);
						$movieId = $db->insert('movies', $data);
						dump($data);
						saveCategory($db, $movieId, $item->find('.item_categories', 0));
					}else{
						echo "movie already exists " . movieTitle. ", id->". $movieId['id'];
					}

					if($movieId > 0){
						saveMovieLinks($db, $movieId, $movieLink);
					}
				}
			}
			sleep(1);
			
			$startPage ++;
		}
	}

	function dump($data){
		echo "<pre>";
		print_r($data);
		echo "</pre>";
?>