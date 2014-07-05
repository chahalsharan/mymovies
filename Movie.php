<?php require_once 'Rating.php' ?>
<?php require_once 'databaseConfig.php' ?>

<?php
    class Movie{

        public $id;
        public $name;
        public $link;
        public $watch;
        public $year;
        public $rating;
        public $synopsis;
        public $thumbnail;
        public $actors = array();
        public $categories = array();

        private static $allCategories;
        private static $allActors;

        public static function init() {
            $db = getMysqlConnection();
            
            Movie::$allCategories = getAllCategories($db);
            Movie::$allActors = getAllActors($db);

            closeMysqlConnection($db);
        }

        public function __construct($movieId = NULL) {
            $this->id = $movieId;
        }

        public function saveOrUpdate($db) {
            if (isset($this->movieId)) {
                $existingMovieId = $this->movieId;
            } else {
                $existingMovieId = Movie::existsInDb($db, $this->name);
            }
            if (isset($existingMovieId)) {
                error_log("DEBUG: Movie exists, going to update movie:" . $existingMovieId);
                $existingMovie = Movie::loadFromDb($db, $existingMovieId);
                $existingMovie->update($db, $this);
                return $existingMovie;
            }else{
                $this->save($db);
                return $this;
            }
        }

        public function update($db, $updatedMovie) {
            $data = Array ("name" => urlencode($this->name),
                           "link" => urlencode($this->link),
                           "year" => urlencode($this->year),
                           "thumbnail" => urlencode($this->thumbnail),
                           "rating" => $this->rating->general,
                           "synopsis" => urlencode($this->synopsis)
            );
            $db ->where('id', $this->id)
                ->update('movies', $data);

            $newCats = array_diff($updatedMovie->categories, $this->categories);
            $this->saveOrUpdateCategories($db, $newCats);

            $newActs = array_diff($updatedMovie->actors, $this->actors);
            $this->saveOrUpdateActors($db, $newActs);

            if ($this->watch != $updatedMovie->watch) {
                $this->saveOrUpdateWatchLinks($db, $updatedMovie->watch);
            }
        }

        public function save($db) {
            $data = Array ("name" => urlencode($this->name),
                           "link" => urlencode($this->link),
                           "year" => urlencode($this->year),
                           "thumbnail" => urlencode($this->thumbnail)
            );
            if (isset($this->rating->general)) {
                $data['rating'] = $this->rating->general;
            }
            if (isset($this->synopsis)) {
                $data['synopsis'] = urlencode($this->synopsis);
            }
            $this->id = $db->insert('movies', $data);

            if (isset($this->categories)) {
                $this->saveOrUpdateCategories($db, $this->categories);
            }
            if (isset($this->actors)) {
                $this->saveOrUpdateActors($db, $this->categories);
            }
            if (isset($this->watch)) {
                $this->saveOrUpdateWatchLinks($db, $this->watch);
            }
        }

        public function saveOrUpdateWatchLinks($db, $watchLink) {
            $watchLink = urlencode($watchLink);

            $data = Array ("movie_id" => $this->id,
                           "link" => $watchLink,
                           "link_date" => date('Y-m-d'));
            $existingLink = $db ->where("movie_id", $this->id)
                                ->where("link", $watchLink)
                                ->get("links");
            if ($db->count > 0) {
                $db ->where("link_id", $existingLink[0]['link_id'])
                    ->update('links', $data);
            }else{
                $db->insert('links', $data);
            }
        }

        private function saveOrUpdateActors($db, $updatedActors = NULL) {
            $actors = $this->actors;

            if (isset($updatedActors)) {
                $actors = $updatedActors;
            }
            $allActors = Movie::$allActors;
            echo "<pre>Actors";
            print_r($allActors);
            echo "</pre>";

            foreach ($actors as $act) {
                $actId = 0;
                if (isset($allActors[$act])) {
                    $actId = $allActors[$act];
                }else{
                    $data = Array("name" => urlencode($act));                           
                    $actId = $db->insert('actors', $data);
                    $allActors[$act] = $actId;
                }
                $data = Array("actor_id" => $actId,
                              "movie_id" => $this->id);
                $db->insert("movie_actors", $data);
            }
        }

        private function saveOrUpdateCategories($db, $updatedCategories = NULL) {
            $categories = $this->categories;
            if (isset($updatedCategories)) {
                $categories = $updatedCategories;
            }
            foreach ($categories as $cat) {
                $catId = 0;
                if (isset($allCategories[$cat])) {
                    $catId = $allCategories[$cat];
                }else{
                    $data = Array("category" => urlencode($cat));                           
                    $catId = $db->insert('categories', $data);
                    $allCategories[$cat] = $catId;
                }
                $data = Array("category_id" => $catId,
                              "movie_id" => $movieId);
                $db->insert("movie_categories", $data);
            }
        }

        public static function existsInDb($db, $name) {
            $dbMovie = $db    ->where("name", urlencode($name))
                                ->getOne("movies", "id");
            if ($db->count  > 0) {
                return $dbMovie['id'];
            }
            return;
        }
        public static function loadAllFromDB($db) {
            $movies = array();
            $movieDb = $db  ->orderBy("m.year", "desc")
                            ->orderBy("m.name", "asc")
                            ->get('movies m');
            foreach ($movieDb as $m) {
                $movie = new Movie($m['id']);
                $movie->name = urldecode($m['name']);
                $movie->year = $m['year'];

                $movie->link = urldecode($m['link']);
                $movie->thumbnail = urldecode($m['thumbnail']);
                $movie->rating = new Rating($m['rating']);
                $movie->synopsis = urldecode($m['synopsis']);

                $movies[$movie->id] = $movie;
            }
            return $movies;
        }
        public static function loadFromDb($db, $movieId) {
            $movieDb = $db   ->join("links l", "l.movie_id = m.id", "LEFT")
                            ->join("movie_categories mc", "mc.movie_id = m.id", 'LEFT')
                            ->join("categories c", "c.category_id = mc.category_id", "LEFT")
                            ->join("movie_actors ma", "ma.movie_id = m.id", "LEFT")
                            ->join("actors a", "ma.actor_id = a.actor_id", "LEFT")
                            ->join("rotten_data r", "r.movie_id = m.id", "LEFT")
                            ->where("m.id", $movieId)
                            ->groupBy( 'm.id,' .
                                       'm.name,' .
                                       'm.year,' .
                                       'l.link,' .
                                       'm.thumbnail,' .
                                       'm.rating ,' .
                                       'm.synopsis,' .
                                       'l.link,' .
                                       'c.category,' .
                                       'a.name,' .
                                       'r.critics_rating,' .
                                       'r.critics_score,' .
                                       'r.audience_rating,' .
                                       'r.audience_score,' .
                                       'r.synopsis')
                            ->orderBy("m.year", "desc")
                            ->orderBy("m.name", "asc")
                            ->get('movies m', null, 
                                'm.id, m.name, m.year, m.link as movie_link, m.thumbnail, ' .
                                'm.rating as rating_general, m.synopsis as synopsis_general,' .
                                'l.link as watching_link, ' .
                                'c.category, ' .
                                'a.name as actor_name, ' .
                                'r.critics_rating, r.critics_score, r.audience_rating, r.audience_score, ' .
                                'r.synopsis as rotten_synopsis');
            if ($db->count > 0) {
                $movie = new Movie($movieId);
                foreach ($movieDb as $m) {
                    $movie->name = urldecode($m['name']);
                    $movie->year = $m['year'];

                    $movie->link = urldecode($m['movie_link']);
                    $movie->thumbnail = urldecode($m['thumbnail']);
                    $movie->rating = new Rating($m['rating_general']);
                    $movie->synopsis = urldecode($m['synopsis_general']);
                    $movie->watch = urldecode($m['watching_link']);
                    if (isset($m['category'])) {
                        array_push($movie->categories, urldecode($m['category']));
                    }
                    if (isset($m['actor_name'])) {
                        array_push($movie->actors, urldecode($m['actor_name']));
                    }
                    if (isset($m['critics_score'])) {
                        $movie->rating->criticsRating = $m['critics_rating'];
                        $movie->rating->criticsScore = $m['critics_score'];
                    }
                    if (isset($m['audience_score'])) {
                        $movie->rating->audienceRating = $m['audience_rating'];
                        $movie->rating->audienceScore = $m['audience_score'];
                    }
                    if (isset($m['rotten_synopsis'])) {
                        $movie->synopsis = urldecode($m['rotten_synopsis']);
                    }
                }
                array_unique($movie->categories, SORT_REGULAR);
                array_unique($movie->actors);

                echo "<pre>Actors";
                print_r($movie->actors);
                echo "</pre>";

                return $movie;
            }
            return;
        }
    }

    Movie::init();
?>