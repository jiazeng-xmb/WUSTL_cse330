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
        die("Token Does Not Match!");
    }
    if (isset($_POST['cancel']))
    {
        header('Location: storycommentpage.php');
    } 
    else
    {
        //edit comments to query
        $content = $_POST['commentContent'];
        $cid = $_POST['commentid'];
        $q = "update comments set content=? where cid=?;";
        $query = $mysqli->prepare($q);
        if(!$query)
        {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $query->bind_param('si',$content,$cid);
        $query->execute();
        $query->close();
        header('Location: storycommentpage.php?error=3');
    }
?> 
	</body>
</html>
