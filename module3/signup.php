<?php 
    require 'database.php';
    session_start();
    
    $username = trim($_POST['user']);
    $password = trim($_POST['pw']);
    $repeatPW = trim($_POST['rpw']);

    // check if two password match
    if($password != $repeatPW)
    {
        header("Location: loginPage.php?error=5");
        exit;
    }
    
    // password is typed correctly
    $password = password_hash($_POST['pw'], PASSWORD_DEFAULT);

    // check if input username exists
    $q = "select * from users where username=?;";
    $query = $mysqli->prepare($q);
    if(!$query)
    {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $query->bind_param('s',$username);
    $query->execute();
    if($query->fetch()) // user exist
    {
        $query->close();
        header("Location: loginPage.php?error=2");
    }
    else    // user not exist
    {
        $query = $mysqli->prepare("insert into users (username, password) values (?, ?)");
        if(!$query)
        {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $query->bind_param('ss', $username, $password);
        $query->execute();
        $query->close();
        header("Location: loginPage.php?error=3");
    }
?>