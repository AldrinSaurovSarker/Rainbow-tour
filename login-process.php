<?php
    include 'connect-db.php';
    include 'connect-session.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        $query = "SELECT * FROM tourists WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['email'];
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['error'] = "Invalid password.";
                header("Location: login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "No account exists for the given email.";
            header("Location: login.php");
            exit();
        }
    }
?>