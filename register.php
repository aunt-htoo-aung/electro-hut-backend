<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Signup Process from Signup Page
if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = "USER";
    $user_exists = check_user_exists($email, $conn);
    if ($user_exists) {
        $message = "This email is already used. Please try again!!!";
    } else {
        try {
            $insert_user_query = "INSERT INTO User(name,email,password,role) VALUES(?,?,?,?)";
            $stmt = $conn->prepare($insert_user_query);
            $stmt->execute([$username, $email, $password, $role]);

            header('location:index.php');
            exit;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }
}

//Check user existed or not by email
function check_user_exists($email, $conn)
{
    try {
        $check_username_query = 'SELECT * FROM User where email=?';
        $stmt = $conn->prepare($check_username_query);
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        if ($result) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        $e->getMessage();
    }
}