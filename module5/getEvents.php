<?php 
    require 'database.php';
    ini_set("session.cookie_httponly", 1);
    session_start();

    $ok = true;
    $eventsID = array();    // array of events' id for given date and current user
    $eventsTitle = array();
    $message = array();
    $eventsTime = array();
    $eventsTag = array();

    $username = $_SESSION['user'];
    $category = htmlentities($_POST['category']);
    $date = htmlentities($_POST['date']);

    date_default_timezone_set('America/Chicago');
    $date = date("Y-m-d",strtotime($date));

    if (!isset($username))  // user session expired / not log in / token not match
    {
        $ok = false;
        $message[] = 'Only valid login user can add events';
    }

    if($ok) // user exists
    {

        // get all events information from given user and date
        if ($category == 'All')
        {
            // get all events information from given user and date
            $query = $mysqli->prepare("select event.eventID, title, time, tag from (event join username_event on event.eventID=username_event.eventID) join user on user.username=username_event.username where user.username=? and date=?;");
            if(!$query)
            {
                $ok = false;
                $message[] = $mysqli->error;
            }
            else
            {
                $query->bind_param('ss',$username, $date);
                $query->execute();
                $query->bind_result($id, $title, $time, $tag);
                $message[] = 'begin get time';
                while($query->fetch())
                {
                    $eventsID[] = htmlentities($id);
                    $eventsTitle[] = htmlentities($title); 
                    $eventsTime[] = htmlentities($time);
                    $eventsTag[] = htmlentities($tag);
                }
                $query->close();
                $message[] = "Successfully get all events!";
            }
        }
        else    // get events at selected category
        {
            $query = $mysqli->prepare("select event.eventID, title, time from (event join username_event on event.eventID=username_event.eventID) join user on user.username=username_event.username where user.username=? and date=? and tag=?;");
            if(!$query)
            {
                $ok = false;
                $message[] = $mysqli->error;
            }
            else
            {
                $query->bind_param('sss',$username, $date, $category);
                $query->execute();
                $query->bind_result($id, $title, $time);

                while($query->fetch())
                {
                    $eventsID[] = htmlentities($id);
                    $eventsTitle[] = htmlentities($title);
                    $eventsTime[] = htmlentities($time);
                    $eventsTag[] = htmlentities($category);
                }
                $query->close();
                $message[] = "Successfully get all events!";
                //$message[] = $category;
            }
        }
    }   

    echo json_encode
    (
        array
        (
            'ok' => $ok,
            'eventsID' => $eventsID,
            'eventsTitle' => $eventsTitle,
            'eventsTime' => $eventsTime,
            'eventsTag' => $eventsTag,
            'message' => $message
        )
    );
?>