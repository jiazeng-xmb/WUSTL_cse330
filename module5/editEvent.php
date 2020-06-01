<?php 
require 'database.php';
ini_set("session.cookie_httponly", 1);
session_start();

// Json code reference: https://www.youtube.com/watch?v=zvt8ff3d63Q
$ok = true;
$message = array();

$id = htmlentities($_POST['id']);
$token = htmlentities($_POST['token']);
$username = $_SESSION['user'];
$eventTitle = htmlentities($_POST['eventTitle']);
$eventDate = htmlentities($_POST['eventDate']);
$eventTime = htmlentities($_POST['eventTime']);
$eventCategory = htmlentities($_POST['eventCategory']);
date_default_timezone_set('America/Chicago');
$eventDate = date("Y-m-d",strtotime($eventDate));
$eventTime = date("H:i:s",strtotime($eventTime));

if (!isset($username) || $_SESSION['token'] != htmlentities($_POST['token'])) // user session expired / not log in / token not match 
{
    $ok = false;
    $message[] = 'Only valid login user can add events';
}

if($ok) // user exists
{
    // insert event information into database
    $query = $mysqli->prepare("update event set date=?,time=?,tag=?,title=? where eventID=?;");
    if(!$query)
    {
        $ok = false;
        $message[] = $mysqli->error;
    }
    else
    {
        $query->bind_param('ssssi',$eventDate,$eventTime,$eventCategory,$eventTitle,$id);
        $query->execute();
        $query->close();
        //get event ID
        $message[] = "update successfully";
        $message[] = $eventDate;
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

