<?php
    class RottenData{
        public $id;
        public $title;
        public $criticsRating;
        public $audienceRating;
        public $criticsScore;
        public $audienceScore;
        public $synopsis;
        public $thumbnail;
        public $movieId;
        public $loadDate;
        public $rottenReferenceId;

        public static function loadAllFromDB($db) {
            $rottenData = $db->get("rotten_data");
            $rots = array();
            foreach ($rottenData as $rotten) {
                $rot = new RottenData();
                $rot->id = $rotten['rotten_id'];
                $rot->title = urldecode($rotten['title']);

                $rot->criticsRating = $rotten['critics_rating'];
                $rot->audienceRating = $rotten['audience_rating'];
                $rot->criticsScore = $rotten['critics_score'];
                $rot->audienceScore = $rotten['audience_score'];
                $rot->synopsis = urldecode($rotten['synopsis']);
                $rot->thumbnail = urldecode($rotten['thumbnail']);
                $rot->movieId = $rotten['movie_id'];
                $rot->loadDate = $rotten['load_date'];
                $rot->rottenReferenceId = $rotten['rotten_reference_id'];

                $rots[$rot->movieId] = $rot;
            }
            return $rots;
        }

        public static function loadFromDB($db, $movieId) {
            $rotten = $db   ->where("movie_id", $movieId)
                            ->getOne("rotten_data");
            
            if(! isset($rotten)){
                error_log("DEBUG: Rotten data not found for movie: " . $movieId);
                return;
            }
            $rot = new RottenData();
            $rot->id = $rotten['rotten_id'];
            $rot->title = urldecode($rotten['title']);

            $rot->criticsRating = $rotten['critics_rating'];
            $rot->audienceRating = $rotten['audience_rating'];
            $rot->criticsScore = $rotten['critics_score'];
            $rot->audienceScore = $rotten['audience_score'];
            $rot->synopsis = urldecode($rotten['synopsis']);
            $rot->thumbnail = urldecode($rotten['thumbnail']);
            $rot->movieId = $rotten['movie_id'];
            $rot->loadDate = $rotten['load_date'];
            $rot->rottenReferenceId = $rotten['rotten_reference_id'];

            return $rot;
        }

        public function saveOrUpdate($db) {
            $data = Array(  "rotten_reference_id" => $this->rottenReferenceId,
                            "title" => $this->title,
                            "movie_id" => $this->movieId,
                            "load_date" => date('Y-m-d'));

            if (isset($this->title)) {
                $data['title'] = urlencode($this->title);
            }
            if (isset($this->criticsRating)) {
                $data['critics_rating'] = $this->criticsRating;
            }
            if (isset($this->audienceRating)) {
                $data['audience_rating'] = $this->audienceRating;
            }

            if (isset($this->criticsScore)) {
                $data['critics_score'] = $this->criticsScore;
            }

            if (isset($this->audienceScore)) {
                $data['audience_score'] = $this->audienceScore;
            }
            if (isset($this->thumbnail)) {
                $data['thumbnail'] = urlencode($this->thumbnail);
            }
            if (isset($this->synopsis)) {
                $data['synopsis'] = urlencode($this->synopsis);
            }

            if (isset($this->movieId)) {
                $data['movie_id'] = $this->movieId;
            }else{
                error_log("ERROR: Mandatory field movieId missing from RottenData object");
                return false;
            }

            $existingRotten = $this->loadFromDB($db, $this->movieId);
            if (isset($existingRotten)) {
                $db ->where("rotten_id", $existingRotten->id)
                    ->update("rotten_data", $data); 
            } else {
                $this->id = $db->insert("rotten_data", $data); 
            }
        }
    }
?>