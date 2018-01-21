<?php
/**
 * Created by PhpStorm.
 * User: barto
 * Date: 21.01.18
 * Time: 20:24
 */

//sprawdzenie czy zalogowany, jesli tak to przekierowanie na user.php
session_start();

if (isset($_SESSION['user_login_status']) && $_SESSION['user_login_status'] == 1) {
    echo '
        <script type="text/javascript">
           window.location = "user.php"
        </script>
    ';
    die();
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link href="imports/bootstrap.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
    <title>Zaloguj sie - UbijOkazje.pl</title>
    <script src="imports/jquery-3.1.1.slim.min.js"></script>
    <script src="imports/tether.min.js"></script>
    <script src="imports/bootstrap.min.js"></script>
</head>
<body>
<div id="loginDiv">
    <?php
    if (isset($_POST['username']) && isset($_POST['password'])) {
        include('db-connection.php');
        if (!$dbconnection->connect_errno) {
            $user_name = $dbconnection->real_escape_string($_POST['username']);
            $sql = "SELECT Username, SaltyPassword, Salt FROM user WHERE Username = '$user_name';";
            if ($result_of_login_check = $dbconnection->query($sql)) {
                if ($result_of_login_check->num_rows == 1) {
                    $result_row = $result_of_login_check->fetch_object();
                    $password = $_POST['password'];
                    $password += $result_row->Salt;
                    $hashedPass = md5($password);
                    if ($result_row->SaltyPassword == $hashedPass) {
                        $_SESSION['username'] = $result_row->Username;
                        $_SESSION['user_login_status'] = 1;
                        echo '
                        <script type="text/javascript">
                           window.location = "index.php?login"
                        </script>
                    ';
                        die();
                    } else {
                        echo '<p class="errorP">Bledne haslo!</p>';
                    }
                } else {
                    echo '<p class="errorP">Uzytkownik nie istnieje!</p>';
                }
            } else {
                echo '
                    <p class="errorP">Blad polaczenia z baza!</p>
                ';
                /*echo "Query: " . $sql . "\n";
                echo "Errno: " . $dbconnection->errno . "\n";
                echo "Error: " . $dbconnection->error . "\n";*/
            }
        } else {
            echo '
                <p class="errorP">Blad polaczenia z baza!</p>
            ';
            /*echo "Query: " . $sql . "\n";
            echo "Errno: " . $dbconnection->errno . "\n";
            echo "Error: " . $dbconnection->error . "\n";*/
        }
    }
    ?>
    <form method="post">
        <p><label for="loginInput">Login: </label><input name="username" id="loginInput" type="text"></p>
        <p><label for="passwordInput">Haslo: </label><input name="password" id="passwordInput" type="password"></p>
        <button id="loginButton">Zaloguj</button>
        <div style="clear: both;"></div>
    </form>
    <p style="text-align: center; margin-top: 15px">Nie masz konta? <a href="register.php">Zarejestruj sie.</a></p>
</div>
</body>
</html>