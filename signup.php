<?php
require_once "db_connect.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Signup Process from Signup Page
if (isset($_POST['signup'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = "USER";
    if ($password == $confirm_password) {
        $encrypt_password = md5($password);

        $user_exists = check_user_exists($email, $conn);
        if ($user_exists) {
            $message = "This email is already used. Please try again!!!";
            header('location:../electro-hut/signup.php');
        } else {
            try {
                $insert_user_query = "INSERT INTO User(first_name,last_name,email,phone,gender,date_of_birth,password,role) VALUES(?,?,?,?,?,?,?,?)";
                $stmt = $conn->prepare($insert_user_query);
                $stmt->execute([$first_name, $last_name, $email, $phone, $gender, $date_of_birth, $encrypt_password, $role]);
                header('location:../electro-hut/login.html');
                exit;
            } catch (PDOException $e) {
                $e->getMessage();
            }
        }
    } else {
        $message = "Password and Confirm Password are not match!!!";
        header('location:../electro-hut/signup.php');
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
