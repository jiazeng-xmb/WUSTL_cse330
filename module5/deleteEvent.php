<?php 
require 'database.php';
ini_set("session.cookie_httponly", 1);
session_start();

// Json code reference: https://www.youtube.com/watch?v=zvt8ff3d63Q
$ok = true;
$message = array();
$id = htmlentities($_POST['id']);

if (!isset($_SESSION['user']) || $_SESSION['token'] != htmlentities($_POST['token']))// user session expired / not log in / token not match
{
    $ok = false;
    $message[] = 'Only valid login user can add events';
}
else
{
    // delete current event from username_event table
    $query = $mysqli->prepare("delete from username_event where eventID=?;");
    if(!$query)
    {
        $ok = false;
        $message[] = $mysqli->error;
    }
    else
    {
        $query->bind_param('i',$id);
        $query->execute();
        $query->close();
        $message[] = "delete from username_event table! ";
        // delete current event from event table
        $query = $mysqli->prepare("delete from event where eventID=?;");
        if(!$query)
        {
            $ok = false;
            $message[] = $mysqli->error;
        }
        else
        {
            $query->bind_param('i',$id);
            $query->execute();
            $query->close();
            $message[] = "delete from event table! ";
        }
    }
}

  

echo json_encode
(
    array
    (
        'ok' => $ok,
        'messages' => $message
    )
);
?>

