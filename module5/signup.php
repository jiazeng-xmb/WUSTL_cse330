<?php 
    require 'database.php';
    ini_set("session.cookie_httponly", 1);
    session_start();
    $ok = true;
    $messages = array();
    
    $username = htmlentities($_POST['username']);
    $password = htmlentities($_POST['password']);
    $repeatPW = htmlentities($_POST['rpassword']);

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
        // check if two password match
        if($password != $repeatPW)
        {
            $ok = false;
            $message[] = 'Password does not match';
        }
        else
        {
            // password is typed correctly
            $password = password_hash($password, PASSWORD_DEFAULT);

            // check if input username exists
            $q = "select username from user where username=?;";
            $query = $mysqli->prepare($q);
            if(!$query)
            {
                $ok = false;
                $message[] = $mysqli->error;
            }
            else
            {
                $query->bind_param('s',$username);
                $query->execute();
                if($query->fetch()) // user exist
                {
                    $query->close();
                    $ok = false;
                    $message[] = "Username already exists!";
                }
                else    // user not exist
                {
                    $query = $mysqli->prepare("insert into user (username, password) values (?, ?)");
                    if(!$query)
                    {
                        $ok = false;
                        $message[] = $mysqli->error;
                    }
                    $query->bind_param('ss', $username, $password);
                    $query->execute();
                    $query->close();
                    $message[] = "User sign up successfully!";
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