<?php 
    ini_set("session.cookie_httponly", 1);
    session_start();
    $ok = false;
    $token = "";

    // if user log in
    if(isset($_SESSION['user']))
    {
        $ok = true;
        $token = $_SESSION['token'];
    }

    echo json_encode
    (
        array
        (
            'ok' => $ok,
            'token' => $token
        )
    );
?>