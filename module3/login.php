<?php 
require 'database.php';
session_start();
if(isset($_POST['guest']))
{
    $_SESSION['user'] = ""; // user session for guest option
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(64));
    header("Location: mainPage.php");
}
else
{
    $username = $_POST['username'];
    $password = $_POST['pw'];
    $isExist = false;

    // check if input user exists in users table
    $query = $mysqli->prepare("select password from users where username=?");
    if(!$query)
    {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $query->bind_param('s',$username);
    $query->execute();
    $query->bind_result($pw);
    if($query->fetch()) // user exist
    {
        $query->close();
        if(password_verify($password, trim($pw)))   // if password match
        {
            $_SESSION['user'] = $_POST['username'];
            $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(64));
            header("Location: mainPage.php");
        }
        else    // password not match
        {
            header("Location: loginPage.php?error=4");
        }
    }
    else    // user not exist
    {
        header("Location: loginPage.php?error=1");
    }
}?>
