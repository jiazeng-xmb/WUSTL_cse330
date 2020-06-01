<!doctype html>
<html lang='en'>
    <head>
        <title>	CSE503 New Web Site</title>
        <meta charset="utf-8"/>
    </head>
    <body>
<?php 
    require 'database.php';
    session_start(); 
    if (!hash_equals($_SESSION['token'],$_POST['token'])){
        die("Token Does Not Match!");
    }
    $username = $_SESSION['user'];
    $sid = $_POST['storyid'];

    // delete all comments of current story
    $q = "delete from comments where sid=?;";
    $query = $mysqli->prepare($q);
    if(!$query)
    {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $query->bind_param('i',$sid);
    $query->execute();
    $query->close();

    // delete current story
    $q = "delete from stories where sid=?;";
    $query = $mysqli->prepare($q);
    if(!$query)
    {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $query->bind_param('i',$sid);
    $query->execute();
    $query->close();
    header('Location: mainPage.php?error=2');
?> 
    </body>
</html>