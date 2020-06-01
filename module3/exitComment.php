<!doctype html>
<html lang='en'>
    <head>
        <title>	CSE503 New Web Site</title>
        <meta charset="utf-8"/>
    </head>
    <body>
<?php
    session_start();
    if(isset($_SESSION['currSid']))
    {
        unset($_SESSION['currSid']);
    }
    header('Location: mainPage.php');
?> 
	</body>
</html>