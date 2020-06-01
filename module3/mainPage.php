
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
            
        echo "<div class='text' style='text-align:center;'>
            <h1> CSE503 New Web Site </h1>
        </div>
        <div style='text-align: center'>
            <span style='float:right;'><a href='logout.php'>Logout</a></span>";
        
        if($_SESSION['user']!="")   // if not guest
        {
            echo "<span style='float:left;'><a href='profilePage?".$_SESSION['user'].".php'>Account</a></span>";
            echo "<a href='addStoryPage.php'>Add Story</a>";
        }
       
        echo "</div><br />";   
    
        //error message
        if(isset($_GET['error']))
        {   
            $error = trim($_GET['error']);
            if ($error == '1')
            {
                echo '<span style="color: red;"> Story added succesfully! </span>';
            }
            else if ($error == '2')
            {
                echo '<span style="color: red;"> Story deleted succesfully! </span>';
            }
            else if ($error == '3')
            {
                echo '<span style="color: red;"> Story updated succesfully! </span>';
            }
        }

    //main page
    $username = $_SESSION['user'];
    $token = $_SESSION['token'];
    $q = "select * from stories; ";
    $query = $mysqli->prepare($q);
    if(!$query)
    {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $query->execute();
    $query->bind_result($sid, $storyTitle,$storyAuthor,$storyContents,$storyLink);
    $query->store_result();
    while($query->fetch()) // get each story info
    {   
        // get number of likes for current story
        $qq = "select * from likes where sid=?;";
        $qquery = $mysqli->prepare($qq);
        if(!$qquery)
        {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $qquery->bind_param('i',$sid);
        $qquery->execute();
        $nLikes = 0;
        while($qquery->fetch())
        {
            $nLikes++;
        }
        $nLikes = $qquery->num_rows();
        $qquery->close();

        echo 
        "<HR />
        <div>
            <H2>Story Title: ".$storyTitle."</H2>
            <div style='float:right;'>";
                if($_SESSION['user']=="")   // if guest
                {
                    echo "<img src='dislike.png' alt='dislike'>".$nLikes;
                }
                else
                {
                    // check if current user liked this story before
                    $qq = "select * from likes where sid=? and username=?;";
                    $qquery = $mysqli->prepare($qq);
                    if(!$qquery)
                    {
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $qquery->bind_param('is',$sid,$username);
                    $qquery->execute();
                    if($qquery->fetch())    // liked before
                    {
                        $pic = "like.png";
                        $picName = "like";
                    }
                    else
                    {
                        $pic = "dislike.png";
                        $picName = "unlike";
                    }
                    $qquery->close();

                    echo "<form action='like.php' method='post' style='display: inline;'>
                        <input type='hidden' name='storyid' value={$sid}>
                        <input type='hidden' name='token' value={$token}>
                        <input type='hidden' name='picture' value={$picName}>
                        <input type='image' src='". $pic ."' alt='like'>
                    </form>".$nLikes;
                }
                
            echo "</div>
            <i><a href='profilePage?author=".$storyAuthor."'>".$storyAuthor."</a></i><br />
            <p>".$storyContents."</p>
            <a href='".$storyLink."'>Link</a> <br/> <br />";
        
        if ($username == $storyAuthor)
        {
            // three options
            echo"
            <div class='text' style='text-align:center;'>
                <form action='storycommentpage.php' method='post' style='float: left'>
                <input type='hidden' name='storyid' value={$sid}>
                <input type='hidden' name='token' value={$token}>
                <input type='submit' name='submit' value='Comments'>
                </form>
                <form action='deleteStory.php' method='post' style='float: right'>
                <input type='hidden' name='storyid' value={$sid}>
                <input type='hidden' name='token' value={$token}>
                <input type='submit' name='submit' value='Delete Story'>
                </form>
                <form action='editStoryPage.php' method='post'>
                <input type='hidden' name='storyid' value={$sid}>
                <input type='hidden' name='token' value={$token}>
                <input type='submit' name='submit' value='Edit Story'>
                </form>  
            </div>";
        } 
        else 
        {
            echo
            // only comment
            "<form action='storycommentpage.php' method='post'>
            <input type='hidden' name='storyid' value={$sid}>
            <input type='hidden' name='token' value={$token}>
            <input type='submit' name='submit' value='Comments'>
            </form>";
        }
        echo "</div>";    
    }
    $query->close();
    echo "<br/><br/><br/><br/>";
?>
	</body>
</html>
        
