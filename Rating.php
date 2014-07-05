<?php
    class Rating{

        public function __construct($general = NULL){
            $this->general = $general;
        }

        public $general;
        public $criticsRating;
        public $criticsScore;
        public $audienceRating;
        public $audienceScore;
    }
?>