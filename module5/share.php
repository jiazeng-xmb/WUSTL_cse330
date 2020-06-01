<?php 
require 'database.php';
ini_set("session.cookie_httponly", 1);
session_start();

// Json code reference: https://www.youtube.com/watch?v=zvt8ff3d63Q
$ok = true;
$message = array();

$username = $_SESSION['user'];
$friends = htmlentities($_POST['friends']);
if (!isset($username) || $_SESSION['token'] != htmlentities($_POST['token']))  // user session expired / not log in / token not match
{
    $ok = false;
    $message[] = 'Only valid login user can add events';
    $message[] = $_SESSION['token'];
    //$message[] = $_POST['token'];
}

if($ok) // user exists
{
    // separate friends list
    $friendsArr = explode("\n", $friends);  

    // get all events from current user
    $query = $mysqli->prepare("select event.eventID from (event join username_event on event.eventID=username_event.eventID) join user on user.username=username_event.username where user.username=?;");
    if(!$query)
    {
        $ok = false;
        $message[] = $mysqli->error;
    }
    else
    {
        $query->bind_param('s',$username);
        $query->execute();
        $query->bind_result($id);

        while($query->fetch())
        {
            $eventsID[] = htmlentities($id);
        }
        $query->close();
        $message[] = "Successfully get all events!";
    }

    // for each friend
    foreach($friendsArr as $f)
    {
        // check if user valid
        $query = $mysqli->prepare("select username from user where username=?;");
        if(!$query)
        {
            $ok = false;
            $message[] = $mysqli->error;
        }
        else
        {
            $query->bind_param('s',$f);
            $query->execute();
            if($query->fetch()) // valid user
            {
                $query->close();
                // add all events to them
                foreach($eventsID as $id)
                {
                    $query = $mysqli->prepare("insert into username_event(username,eventID) values (?,?);");
                    if(!$query)
                    {
                        $ok = false;
                        $message[] = $mysqli->error;
                    }
                    else
                    {
                        $query->bind_param('si',$f,$id);
                        $query->execute();
                        $query->close();
                    }
                }
            }
            
            $message[] = "Successfully insert all events!";
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