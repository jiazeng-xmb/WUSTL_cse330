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
            if(!isset($_SESSION['currSid']))
            {
                $_SESSION['currSid'] = $_POST['storyid'];
            }
            $username = $_SESSION['user'];
            $currSid = $_SESSION['currSid'];
            //$currSid = $_POST['storyid'];
            //然后把post里的东西删掉
            $token = $_SESSION['token'];
            // display current story title
            $q = "select title from stories where sid=?;";
            $query = $mysqli->prepare($q);
            if(!$query)
            {
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $query->bind_param('i',$currSid);
            $query->execute();
            $query->bind_result($title);
            if($query->fetch()) // get each story info
            {   
                echo "<h1 style='text-align: center'>".$title." Comments</h1>";
            }
            $query->close();
        ?>

            <div style="text-align: center">
            <span style="float:right;"><a href="logout.php">Logout</a></span>
            <span style="float:left;"><a href="exitComment.php">Back to Main Page</a></span>
            </div><br />

            <?php
            //error message
            if(isset($_GET['error']))
            {   
                $error = trim($_GET['error']);
                if ($error == '1')
                {
                    echo '<span style="color: red;"> Comment added succesfully! </span>';
                }
                else if ($error == '2')
                {
                    echo '<span style="color: red;"> Comment deleted succesfully! </span>';
                }
                else if ($error == '3')
                {
                    echo '<span style="color: red;"> Comment updated succesfully! </span>';
                }
            }

            //display all comments of current story
            $q = "select * from comments where sid=?;";
            $query = $mysqli->prepare($q);
            if(!$query)
            {
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $query->bind_param('i',$currSid);
            $query->execute();
            $query->bind_result($cid, $sid, $author, $content);
            while($query->fetch()) // get each comment
            {   
                echo "<HR />";
                echo "<H3>".$author."</H3>";
                echo "<p>".$content."</p>";
                if ($username == $author)   // edit/delete comments if own
                {
                    echo
                    "<div class='text' style='text-align:center;'>
                        <form action='deleteComment.php' method='post' style='float: right'>
                        <input type='hidden' name='commentid' value={$cid}>
                        <input type='hidden' name='token' value={$token}>
                        <input type='hidden' name='storyid' value={$currSid}>
                        <input type='submit' name='submit' value='Delete Comment'>
                        </form>
                        <form action='editCommentPage.php' method='post' style='float: left'>
                        <input type='hidden' name='commentid' value={$cid}>
                        <input type='hidden' name='token' value={$token}>
                        <input type='hidden' name='storyid' value={$currSid}>
                        <input type='submit' name='submit' value='Edit Comment'>
                        </form>
                    </div><br />";
                }   
            }
            $query->close();
            
            // Add comment if not guest
            if($_SESSION['user']!="")   // if not guest
            {
                echo "<HR />
                <div class='text' style='text-align:center;''>
                    <h3> Add a New Comment </h3>
                    <form action='addComment.php' method='post'>
                        <textarea name='content' rows='20' cols='100'></textarea><br /><br />
                        <input type='submit' name='submit' value='Add Comment'>
                        <input type='hidden' name='storyid' value={$currSid}>
                        <input type='hidden' name='token' value={$token}>
                    </form>
                </div>";
            }
        ?>
    </body>
</html>