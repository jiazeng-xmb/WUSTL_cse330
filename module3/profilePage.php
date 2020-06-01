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

    $username = $_SESSION['user'];
    if (!isset($_GET['author']))
    {
        $author = $username;
    }
    else
    {
        $author = trim($_GET['author'], "'");
    }
    
    $owner = false;
    if($username == $author)
    {
        $owner = true;
    }

    echo "<div style='text-align: center'><H1>Username:".$author."</H1></div>
        <a href='mainPage.php'>Back to Main Page</a><br />";

    // change password
    if($owner)
    {
        echo "<br /><div style='margin-left: auto; margin-right: auto; text-align: center; border:2px groove grey; width: 200px; '><br />";
        //error message
        if(isset($_GET['error']))
        {   
            $error = trim($_GET['error']);
            if ($error == '1')
            {
               echo '<span style="color: red;"> Password changed succesfully! </span>';
            }
            else if ($error == '2')
            {
               echo '<span style="color: red;"> Password do not match! </span>';
            }
            else if ($error == '3')
            {
               echo '<span style="color: red;"> Current Password is not correct! </span>';
            }
            else if($error == '4')
            {
               echo '<span style="color: red;"> You have logged out! </span>';
            }
        }

        // display change password option
        echo "
            <form action='changePW.php' method='POST'>
                <p>Current Password: <input type='password' name='cpw' /></p>
                <p>New Password: <input type='password' name='npw' /></p>
                <p>Repeat New Password: <input type='password' name='rpw' /></p>
                <input type='submit' value='Submit' />
            </form>
        </div>";
    }

    echo "<br /><br />
    <div style='margin-left: auto; margin-right: auto; border:2px dotted grey; width: 1000px;'>
    <div style='text-align: center'><H2> Stories </H2></div>";
    // display all story titles from current user
    $q = "select * from stories where author=?;";
    $query = $mysqli->prepare($q);
    if(!$query)
    {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $query->bind_param('s',$author);
    $query->execute();
    $query->bind_result($sid, $storyTitle,$storyAuthor,$storyContents,$storyLink);
    echo "<ul>";
    while($query->fetch()) 
    {   
        echo "<li>".$storyTitle."</li>";
    }
    $query->close();
    echo "</ul></div><br /><br />
    <div style='margin-left: auto; margin-right: auto; border:2px dashed grey; width: 1000px;'>
    <div style='text-align: center'><H2> Comments </H2></div>";
    // display all comments from current user
    $q = "select * from comments where author=? order by sid;";
    $query = $mysqli->prepare($q);
    if(!$query)
    {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $query->bind_param('s',$author);
    $query->execute();
    $query->bind_result($cid, $sid, $author, $content);
    $query->store_result();
    $currSid = 0;
    $flag = false;
    while($query->fetch()) 
    {   
        // if goes to next story
        if($currSid != $sid)
        {
            if($currSid != 0)
            {
                echo "</ul>";
            }
            $currSid = $sid;
            $qq = "select title from stories where sid=?;";
            $qquery = $mysqli->prepare($qq);
            if(!$qquery)
            {
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $qquery->bind_param('i',$sid);
            $qquery->execute();
            $qquery->bind_result($title);
            if($qquery->fetch())
            {
                echo "<HR />
                <div style='text-align:center'><H3>".$title."</H3></div>
                <ul>";
                $flag = true;
            }
            $qquery->close();
        }
        // display comment
        echo "<li>".$content."</li>";
    }
    if ($flag) {
        echo "</ul>";
    }
    echo "</div>";
    $query->close();
?>
</body>
</html>