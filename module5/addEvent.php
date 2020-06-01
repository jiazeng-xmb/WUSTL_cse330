<?php 
require 'database.php';
ini_set("session.cookie_httponly", 1);
session_start();

// Json code reference: https://www.youtube.com/watch?v=zvt8ff3d63Q
$ok = true;
$message = array();

$username = $_SESSION['user'];
$eventTitle = htmlentities($_POST['eventTitle']);
$eventDate = htmlentities($_POST['eventDate']);
$eventTime = htmlentities($_POST['eventTime']);
$eventCategory = htmlentities($_POST['eventCategory']);
$friends = htmlentities($_POST['friends']);
date_default_timezone_set('America/Chicago');
$eventDate = date("Y-m-d",strtotime($eventDate));
$eventTime = date("H:i:s",strtotime($eventTime));

if (!isset($username) || $_SESSION['token'] != htmlentities($_POST['token']))  // user session expired / not log in / token not match
{
    $ok = false;
    $message[] = 'Only valid login user can add events';
}

if($ok) // user exists
{
    // get all friends
    $friendsArr = explode("\n", $friends);  
    $friendsArr[] = $username;
    // insert event information into database
    $query = $mysqli->prepare("insert into event(date,time,tag,title) values (?,?,?,?);");
    if(!$query)
    {
        $ok = false;
        $message[] = $mysqli->error;
        $message = "fail to insert event";
    }
    else
    {
        $query->bind_param('ssss',$eventDate,$eventTime,$eventCategory,$eventTitle);
        $query->execute();
        $query->close();
        //get event ID
        $query = $mysqli->prepare("select max(eventID) from event;");
        if(!$query)
        {
            $ok = false;
            $message[] = $mysqli->error;
            $message[] = "fail to get max event";
        }
        else
        {
            $query->execute();
            $query->bind_result($eventID);
            if($query->fetch()) // 
            {
                $query->close();

                //insert into event-user table
                foreach($friendsArr as $f)
                {
                    // check if user valid
                    $query = $mysqli->prepare("select username from user where username=?;");
                    if(!$query)
                    {
                        $ok = false;
                        $message[] = "a friends is not valid";
                    }
                    else
                    {
                        $query->bind_param('s',$f);
                        $query->execute();
                        if($query->fetch()) // valid user
                        {
                            $query->close();
                            // add events
                            $query = $mysqli->prepare("insert into username_event(username,eventID) values (?,?);");
                            if(!$query)
                            {
                                $ok = false;
                                $message[] = $mysqli->error;
                                $message[] = "fail to add to username_event table";
                            }
                            else    // successfully add into event-user table
                            {
                                $query->bind_param('si',$f, $eventID);
                                $query->execute();
                                $query->close();
                            }
                        }
                    }
                }
                
            }
            else
            {
                $ok = false;
                $message[] = "Fail to add event!";
            }
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

