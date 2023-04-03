<?php

if(!$cli) {
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $query='SELECT
        i.id
        ,i.lid
        ,i.ipv4
        ,l.name
        FROM ip_list i
        LEFT JOIN locations l
            ON i.lid = l.id 
        WHERE ipv4 LIKE "' . $user_ip .'"
    ';
    $result = $mysqli->query($query);
    $count = $result->num_rows;

    if($count == 1) {
        $allow = true;
        $location = $result->fetch_assoc();
        
    } else {
        $allow = false;
    }

    $count=false;


    if(!$allow):
        header('Location: '. REDIRECT_URL);
        exit;
    endif;
} else {
    $allow=true;

}



?>