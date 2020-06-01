<!--display layout of editing comment form-->
<!doctype html>
<html lang='en'>
    <head>
        <title>	CSE503 New Web Site</title>
        <meta charset="utf-8"/>
    </head>
    <body>
        <div class="text" style="text-align:center;">
        <h1> Edit Current Comment </h1> <!-- display title -->
        </div>

        <?php 
            require 'database.php';
            session_start(); 
            //obtain the original comments 
            $username = $_SESSION['user'];
            $token = $_SESSION['token'];
            $cid = $_POST['commentid'];
            $q = "select content from comments where cid=?;";
            $query = $mysqli->prepare($q);
            if(!$query)
            {
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $query->bind_param('i',$cid);
            $query->execute();
            $query->bind_result($comment);
            $query->fetch();
        ?> 
        
        <!-- edit current comment -->
        <div class="text" style="text-align:center;">
            <form action='editComment.php' method='post'>
                <textarea name='commentContent' rows='20' cols='100'><?php echo $comment?></textarea><br /><br />
                <input type='hidden' name='commentid' value=<?php echo $cid ?>>
                <input type='submit' name='cancel' value='cancel'>
                <input type='hidden' name='token' value=<?php echo $token ?>>
                <input type='submit' name='edit' value='edit'>    
            </form>
        </div>
        
    </body>
</html>