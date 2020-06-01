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
    //echo $_POST['storyid'];
    if (!hash_equals($_SESSION['token'],$_POST['token'])){
        die("Token Does Not Match!");
    }
    //add comments to query
    $q = "insert into comments (sid, author, content) values (?,?,?);";
    $query = $mysqli->prepare($q);
    if(!$query)
    {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $query->bind_param('iss',$_SESSION['currSid'],$_SESSION['user'],$_POST['content']);
    $query->execute();
    $query->close();
    // echo 
    // "
    // <form action=storycommentpage.php?error=1 method='post'>
    // <input type='submit' value='Back'>
    // <input type='hidden' name='storyid' value={$_POST['storyid']}>
    // </form>
    // ";
    header('Location: storycommentpage.php?error=1');
    
?> 
    </body>
</html>
