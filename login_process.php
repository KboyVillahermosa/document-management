<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($db_connection, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['login_id'] = $user['id'];
            header('Location: index.php');
            exit;
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        $error_message = "User not found. Please register first.";
    }
}
?>
