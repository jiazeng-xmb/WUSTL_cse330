<?php 
require 'database.php';
ini_set("session.cookie_httponly", 1);
session_start();

// Json code reference: https://www.youtube.com/watch?v=zvt8ff3d63Q
$ok = true;
$messages = array();
$token = "";

$username = htmlentities($_POST['username']);
$password = htmlentities($_POST['password']);

if (!isset($username) || empty($username))
{
    $ok = false;
    $message[] = 'Username cannot be empty';
}
if (!isset($password) || empty($password))
{
    $ok = false;
    $message[] = 'Password cannot be empty';
}

if($ok)
{
    // check if input user exists in users table
    $query = $mysqli->prepare("select password from user where username=?");
    if(!$query)
    {
        $ok = false;
        $message[] = "Invalid login.";
    }
    else
    {
        $query->bind_param('s',$username);
        $query->execute();
        $query->bind_result($pw);
        if($query->fetch()) // user exist
        {
            $query->close();

            if(password_verify($password, trim(htmlentities($pw))))   // if password match
            {
                $_SESSION['user'] = $_POST['username'];
                $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(64));
                $token = $_SESSION['token'];
                $message[] = 'Successful login!';
            }
            else    // password not match
            {
                $ok = false;
                $message[] = 'Incorrect Password!';
            }
        }
        else    // user not exist
        {
            $ok = false;
            $message[] = 'User does not exist!';
        }
    }
}   

echo json_encode
(
    array
    (
        'ok' => $ok,
        'token' => $token,
        'messages' => $message
    )
);
?>

