<?php

    function getMysqlConnection(){
        return new Mysqlidb('localhost', 'mymovies', '', 'mymovies');
    }

    function closeMysqlConnection($db){
        $db->__destruct();
    }

    function getAllCategories($db){
        $categories = [];
        foreach($db->get("categories") as $cat){
            $categories[urldecode($cat['category'])] = $cat['category_id'];
        }
        return $categories;
    }

    function getAllActors($db){
        $actors = [];
        foreach($db->get("actors") as $actor){
            $actors[urldecode($actor['name'])] = $actor['actor_id'];
        }
        return $actors;
    }

    function getAllMovies($db, $id = NULL){
        $movies = [];
        if(isset($id)){
            $db->where('id', $id);
        }
        foreach($db->get("movies") as $movie){
            $movies[$movie['id']] = $movie;
        }
        return $movies;
    }

?>