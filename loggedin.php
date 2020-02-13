<?php
    session_start();

    if (array_key_exists("id", $_COOKIE)) {
        $_SESSION['id'] = $_COOKIE['id'];
    }

    if (array_key_exists("id", $_SESSION)) {
        echo "<p>Now you are logged in.</p><p><a href='index.php?logout=1'>Please, leave!</a></p>";
    }
    else
        header("Location: index.php");
?>