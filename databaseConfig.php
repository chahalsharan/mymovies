<?php
    // start database connection if one doesnt already exist
    if(!isset($db)){
        $db = new Mysqlidb('localhost', 'mymovies', '', 'mymovies');
    }

?>