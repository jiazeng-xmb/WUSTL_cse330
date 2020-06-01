<?php
    session_start();
    if(isset($_SESSION['user']))
    {
        //destroy session
        unset($_SESSION['user']);
        session_destroy();
    }
    header('Location: loginPage.php');
?>