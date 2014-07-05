<?php
    class LetMeWatchThisClass{
        /**
         * Static instance of self
         *
         * @var LetMeWatchThisClass
         */
        protected static $_instance;

        protected $movieId;

        protected $movieLink;

        protected $movieHTML;

        protected $linksHTML;

        public function __construct($movieId = NULL, $movieLink = NULL){
            $this->movieId = $movieId;
            $this->movieLink = $movieLink;

            self::$_instance = $this;
        }
    }
?>