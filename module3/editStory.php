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
    if(!hash_equals($_SESSION['token'],$_POST['token']))
    {
        die("Token Does Not Match!");
    }
    if (isset($_POST['cancel']))
    {
        header('Location: mainPage.php');
    } 
    else
    {
        //edit story to query
        $username = $_SESSION['user'];
        $title = $_POST['storyTitle'];
        $link = $_POST['storyLink'];
        $content = $_POST['storyContent'];
        $sid = $_POST['storyid'];

        // add http:// to the beginning of the link if not have
        // refer to https://stackoverflow.com/questions/8591623/checking-if-a-url-has-http-at-the-beginning-inserting-if-not
        if(empty(parse_url($link)['scheme']))
        {
            $link = 'http://'.ltrim($link, '/');
        }

        $q = "update stories set title=?, contents=?, link=? where sid=?;";
        $query = $mysqli->prepare($q);
        if(!$query)
        {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $query->bind_param('sssi',$title,$content,$link,$sid);
        $query->execute();
        $query->close();
        header('Location: mainPage.php?error=3');
    }
?> 
    </body>
</html>
