<?php 
    ini_set("session.cookie_httponly", 1);
    session_start();
    if(isset($_SESSION['user']))
    {
        unset($_SESSION['user']);
        unset($_SESSION['token']);
        session_destroy();
    }
    
    echo json_encode
    (
        array
        (
            'ok' => true
        )
    );
?>