<?php
    
    function getRatingColor($rating){
        if($rating > 0 && $rating < 25){
            return "progress-bar-danger";
        }
        if($rating >=30 && $rating < 50){
            return "progress-bar-warning";
        }
        if($rating >=50 && $rating < 75){
            return "progress-bar-info";
        }
        if($rating >=75){
            return "progress-bar-success";
        }
    };

    function getPaginationLimits($currentPage, $pageSize){
        $fromLimit = ($currentPage-1) * $pageSize;
        if($fromLimit < 0){
            $fromLimit = 0;
        }
        return array($fromLimit, $pageSize);
    }

    function getFilterCriteriaFromRequest($filterName, $isArray=FALSE, $defaultVal=FALSE){
        if (isset($_GET[$filterName])) {
            error_log("DEBUG: Filter name:" . $filterName . ", value:" . $_GET[$filterName]);
            if($isArray){
                return explode(",", $_GET[$filterName]);
            }else{
                return $_GET[$filterName];
            }
        }else{
            return $defaultVal;
        }
    }

    function getFilteredMoviesNumRows($db, 
        $featured=FALSE, $years=FALSE, $categories=FALSE, $actors=FALSE, $searchText=FALSE){

    }

    function getFilteredMovies($db, 
        $featured=FALSE, $years=FALSE, $categories=FALSE, $actors=FALSE, $searchText=FALSE,
        $limits=NULL){

        if($featured){
            $db->join("links l", "l.movie_id = m.id");    
        }else{
            $db->join("links l", "l.movie_id = m.id", "LEFT");
        }
        
        $db->join("rotten_data r", "r.movie_id = m.id", "LEFT");
         
        if ($years) {
            $db->where('m.year', $years, "IN");
        } else if(! isset($_GET['filter'])) {
            $db->where('m.year', date("Y"));
            $featured = TRUE;
            error_log("DEBUG: going to query featured listings only");
        } else {
            $featured = FALSE;
        }

        if ($categories){
            $db->join("movie_categories mc", "mc.movie_id = m.id");
            $db->join("categories c", "c.category_id = mc.category_id");
            $db->where("c.category", $categories, "IN");
        }

        if ($actors) {
            $db->join("movie_actors ma", "ma.movie_id = m.id");
            $db->where("ma.actor_id", $actors, "IN");
        }
        
        $movies = array();
        if ($searchText) {
            error_log("DEBUG: here1");
            $params = array($searchText);
            $searchQuery = "SELECT m.id, m.name, m.year, l.link as link, m.rating, m.synopsis,  ". 
                                "m.thumbnail, r.synopsis as rsynopsis, r.critics_score, r.audience_score " .
                        "FROM movies m " . 
                        "LEFT OUTER JOIN links l ON m.id = l.movie_id ".
                        "LEFT OUTER JOIN rotten_data r ON r.movie_id = m.id ".
                        "WHERE match(m.name) AGAINST(?) ".
                        "GROUP BY m.id, m.name, m.year, l.link, m.rating, m.thumbnail ".
                        "ORDER BY m.year desc, r.critics_score desc, r.audience_score desc, m.rating desc, m.name asc";
            error_log("DEBUG: searchQuery:" . $searchQuery);
            error_log("DEBUG: searchQueryParams:" . $searchText);
            if(isset($limits)){
                $searchQuery = $searchQuery . " LIMIT " . implode(",", $limits);
            }
            #echo "{" . $searchQuery . "}";
            $movies = $db->rawQuery($searchQuery, $params);
            error_log("DEBUG: searchQuery result size:" . $db->count);
        }else{
            error_log("DEBUG: here2");
            $movies = $db   ->groupBy('m.id, m.name, m.year, l.link, m.rating, m.thumbnail')
                            ->orderBy("m.year", "desc")
                            ->orderBy("COALESCE(r.critics_score, r.audience_score, m.rating)", "desc")
                            ->orderBy("m.name", "asc")
                            ->get('movies m', $limits, 
                                  'm.id, m.name, m.year, l.link as link, m.rating, m.synopsis,  m.thumbnail, r.synopsis as rsynopsis, r.critics_score, r.audience_score');
        }
        error_log("DEBUG: Query: " . $db->getLastQuery());

        return $movies;
    }
?>