<?php  
    session_start(); 
?>
<!doctype html>
<html lang='en'>
    <head>
        <title>	CSE503 File Sharing </title>
        <meta charset="utf-8"/>
    </head>
    <body>
        <div class="text" style="text-align:center;">
        <h1> File Sharing Site </h1>
        </div>
        <span style="float:right;"><a href="logout.php">Logout</a></span>
        <br/><br/>
        <!-- error message -->
        <?php
            if(isset($_GET['error']))
            {
                $error = trim($_GET['error']);
                if ($error == '1') 
                {
                    echo '<span style="color: red;"> Your friend does not exit!</span>';
                }
                else if ($error == '2') 
                {
                    echo '<span style="color: red;"> You already have this friend. </span>';
                } 
                else if ($error == '3')
                {
                    echo '<span style="color: red;"> Your friend was added succesfully. </span>';
                }
                else if ($error == '10')
                {
                    echo '<span style="color: red;"> Please enter the name of your friend! </span>';
                }
            }
        ?>
        
        <!-- add friends-->
        <form action="addFriend.php" method="POST" enctype="multipart/form-data">
            <b>Add Friend: </b><br/>
            <input type="text" name="friend" /><br/>
            <input type="submit" value="Submit" />
        </form>
        <br/>
        <!-- list of existing files-->
        <?php
            $username = $_SESSION['user'];
            // https://stackoverflow.com/questions/17334676/how-to-display-directory-files-on-a-php-html-webpage
            // https://stackoverflow.com/questions/15774669/list-all-files-in-one-directory-php
            //list current users' files
            echo "<h3> Current User: ".$username .'</h3>';
            $files = array_diff(scandir("/srv/uploads/".$username."/"), array('.', '..', '.DS_Store'));
            foreach( $files as $file )
            {
                echo $file."<br/>";   
            }
            //list the files belong to my friends
            $friends = fopen('/srv/uploads/'.$username.'group.txt','r');
            if(filesize('/srv/uploads/'.$username.'group.txt') != 0)
            {
                while(!feof($friends))
                {
                    $friend = trim(fgets($friends));
                    if($friend != "")
                    {
                        echo '<h3> Friend: '.$friend.'</h3>';
                        $files = array_diff(scandir("/srv/uploads/".$friend."/"), array('.', '..', '.DS_Store'));
                        foreach( $files as $file )
                        {
                            echo $file."<br/>"; 
                        }
                    } 
                }
            }
            
        ?>
        <br/><br/><br/><br/>
        <!-- error message for uploading, viewing and deleting-->
        <?php
            if(isset($_GET['error']))
            {
                 if ($error == '4')
                {
                    echo '<span style="color: red;"> The user name is not valid. </span>';
                }
                else if ($error == '5')
                {
                    echo '<span style="color: red;"> Please fill in both correct username and filename! </span>';
                }
                else if ($error == '6')
                {
                    echo '<span style="color: red;"> There was an error uploading the file, you shall not pass</span>';
                }
                else if ($error == '7')
                {
                    echo '<span style="color: red;"> Image broken</span>';
                }
                else if ($error == '8')
                {
                    echo '<span style="color: red;"> Sorry, the file does not exist.</span>';
                }
                else if ($error == '9')
                {
                    echo '<span style="color: red;"> Please Login</span>';
                }
            }
        ?>
        <!-- option for uploading files-->
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <b>Upload file here: </b><br/>
            User:<input type = "text" name = "name" /><br/>
            FileName:<input type="file" name="filename" /><br/>
            <input type="submit" value="Upload" />
        </form>
        <br/>
        
        <!-- option for deleting files-->
        <form action="delete.php" method="POST">
            <b>Delete file here:</b><br/>
            User:<input type = "text" name = "name" /><br/>
            FileName:<input type="text" name="filename"><br/>
            <input type="submit" value="Delete">
        </form><br/>

         <!-- option for viewing files-->
         <form action="view.php" method="POST">
            <b>View file here:</b><br/>
            User:<input type = "text" name = "name" /><br/>
            FileName:<input type="text" name="filename"><br/>
            <input type="submit" value="View">
        </form><br/>
    </body>
</html>