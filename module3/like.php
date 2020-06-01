<?php
    require 'database.php';
    session_start();
    if (!hash_equals($_SESSION['token'],$_POST['token'])){
        die("Token Does Not Match!");
    }
    $sid = $_POST['storyid'];
    $username = $_SESSION['user'];

    if($_POST['picture']=="like")   // want to unlike
    {
        $q = "delete from likes where sid=? and username=?;";
        $query = $mysqli->prepare($q);
        if(!$query)
        {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $query->bind_param('is',$sid,$username);
        $query->execute();
    }
    else    // want to like
    {   
        $q = "insert into likes (username, sid) values (?,?);";
        $query = $mysqli->prepare($q);
        if(!$query)
        {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $query->bind_param('si',$username,$sid);
        $query->execute();
    }
    header("Location: mainPage.php");
?>