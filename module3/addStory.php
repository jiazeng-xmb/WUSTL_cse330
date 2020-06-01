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
    if (!hash_equals($_SESSION['token'],$_POST['token'])){
        //die("Token Does Not Match!");
        die($_SESSION['token']."-----".$_POST['token']);
    }
    if (isset($_POST['cancel']))
    {
        header('Location: mainPage.php');
    } 
    else
    {
        //add story to query
        $username = $_SESSION['user'];
        $title = $_POST['storyTitle'];
        $link = $_POST['storyLink'];
        $content = $_POST['storyContent'];
        $q = "insert into stories (title, author, contents, link) values (?,?,?,?);";
        $query = $mysqli->prepare($q);
        if(!$query)
        {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $query->bind_param('ssss',$title,$username,$content,$link);
        $query->execute();
        $query->close();
        header('Location: mainPage.php?error=1');
    }
?> 
    </body>
</html>
