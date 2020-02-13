<?php
    session_start();
    $error = "";
    if (array_key_exists("logout", $_GET)) 
    {
        session_unset();
        setcookie("id", "", time() - 60 * 60); 
        $_COOKIE["id"] = "";
    } else if ((array_key_exists("id", $_SESSION) && $_SESSION['id']) || (array_key_exists("id", $_COOKIE) && $_COOKIE['id'])) {
        header("Location: loggedin.php");
    }

    if (array_key_exists("submit", $_POST)) {
        
        $link = mysqli_connect("localhost", "id12531403_plumdiary", "plumdiary", "id12531403_diary");
        if (mysqli_connect_error())
            die("Can't connect to Data base.");
        
        if (!$_POST['email'])
            $error .= "Empty email field!<br>";
        if (!$_POST['password'])
            $error .= "Empty password field!<br>";
        if ($error != '')
            $error = "<p>Error(s):</p>".$error;
        else {
            if ($_POST['signUp'] == '1') {
                $query = "SELECT id FROM notes WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
                $result = mysqli_query($link, $query);
                if (mysqli_num_rows($result) > 0)
                    $error .= "This email address is already taken.<br>";
                else {
                    $query = "INSERT INTO notes (email, password, note) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."', '')";
                    if (!mysqli_query($link, $query))
                        $error = "<p>Can't sign you up. Please, try again later!</p>";
                    else {
                        $query = "UPDATE notes SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";
                        if (!mysqli_query($link, $query))
                            $error = mysqli_error($link);
                        $_SESSION['id'] = mysqli_insert_id($link);
                        if (array_key_exists("remember", $_POST) && $_POST['remember'] == '1') {
                            setcookie("id", mysqli_insert_id($link), time() + 60 * 60);
                        }
                        header("Location: loggedin.php");
                    }
                }
            } else {
                $query = "SELECT * FROM notes WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
                $row = mysqli_fetch_array(mysqli_query($link, $query));
                if (isset($row)) {
                    $hashpass = md5(md5($row["id"]).$_POST["password"]);
                    if ($row["password"] == $hashpass) {
                        $_SESSION["id"] = $row["id"];
                        if (array_key_exists("remember", $_POST) && $_POST['remember'] == '1')
                        {
                            setcookie("id", $row["id"], time() + 60 * 60);
                        }
                        header("Location: loggedin.php");
                    } else {
                        $error .= "[KO]: no password;";
                    }
                }  else {
                        $error .= "[KO]: no email;";
                }
            }
        }
    }
?>
<div id="error"><?php echo $error; ?></div>
<form method="post">
    <input type="email" name="email" placeholder="E-mail">
    <input type="password" name="password" placeholder="Password">
    <input type="checkbox" name="remember" value=1>
    <input type="hidden" name="signUp" value="1">
    <input type="submit" name="submit" value="Sign Up!">
</form>
<form method="post">
    <input type="email" name="email" placeholder="E-mail">
    <input type="password" name="password" placeholder="Password">
    <input type="checkbox" name="remember" value=1>
    <input type="hidden" name="signUp" value="0 ">
    <input type="submit" name="submit" value="Log In!">
</form>