<!doctype html>
<html lang='en'>
    <head>
        <title>	CSE503 New Web Site</title>
        <meta charset="utf-8"/>
    </head>
    <body>
        <div class="text" style="text-align:center;">
        <h1> Edit Current Story </h1>
        </div>
        <?php 
            require 'database.php';
            session_start(); 
            $token = $_SESSION['token'];
            $username = $_SESSION['user'];
            $sid = $_POST['storyid'];
            $q = "select title, contents, link from stories where sid=?;";
            $query = $mysqli->prepare($q);
            if(!$query)
            {
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $query->bind_param('i',$sid);
            $query->execute();
            $query->bind_result($storyTitle, $storyContents, $storyLink);
            $query->fetch();
        ?> 

        <!-- display current story information -->
        <b>Current Title:</b> <?php echo $storyTitle?><br /><br />
        <b>Current Link:</b> <?php echo $storyLink?><br /><br />
        <b>Current Contents:</b> <p><?php echo $storyContents?></p>
        <HR />

        <div class="text" style="text-align:center;">
            <form action='editStory.php' method='post'>
                Title: <input type=text name='storyTitle' value=<?php echo $storyTitle?>><br /><br />
                Link: <input type=text name='storyLink' value=<?php echo $storyLink?>><br /><br />
                <textarea name='storyContent' rows='20' cols='100'><?php echo $storyContents?></textarea><br /><br />
                <input type='hidden' name='storyid' value=<?php echo $sid ?>>
                <input type='submit' name='cancel' value='cancel'>
                <input type='hidden' name='token' value=<?php echo $token ?>>
                <input type='submit' name='edit' value='edit'>
            </form>
        </div>
        
    </body>
</html>