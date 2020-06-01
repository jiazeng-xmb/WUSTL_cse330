<!--display the layout of adding story form-->
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
            $token = $_SESSION['token'];
        ?> 
        <div class="text" style="text-align:center;">
        <h1> Add a New Story </h1>
        <form action='addStory.php' method='post'>
            Title: <input type=text name='storyTitle'><br /><br />
            Link: <input type=text name='storyLink'><br /><br />
            <textarea name='storyContent' rows='20' cols='100'></textarea><br /><br />
            <input type='submit' name='cancel' value='cancel'>
            <input type='hidden' name='token' value=<?php echo $token ?>>
            <input type='submit' name='submit' value='submit'>
        </form>
        </div>
        
    </body>
</html>