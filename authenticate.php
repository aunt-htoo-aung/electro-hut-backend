<?php
require_once "db_connect.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

//Login Process from the Login Page and Check Authentication
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    check_user_login($email, $password, $conn);
} elseif (!isset($_SESSION['user_id']) && $current_page != "electro-hut/login.php") {
    header('location:electro-hut/login.php');
    exit;
}

//Check User is login or not
function check_user_login($email, $password, $conn)
{
    try {
        $check_user_query = 'SELECT * FROM User WHERE (email=?) AND password=?';
        $stmt = $conn->prepare($check_user_query);
        $stmt->execute([$email, md5($password)]);
        $result = $stmt->fetch();

        if ($result) {
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['role'] = $result['role'];
            if ($result['role'] == 'ADMIN') {
                header('location:../electro-hut/admin/dashboard.php');
                exit;
            } else {
                header('location:../electro-hut/index.php');
                exit;
            }
        } else {
            header('location:../electro-hut/login.php');
            exit;
        }
    } catch (PDOException $e) {
        $e->getMessage();
    }
}
