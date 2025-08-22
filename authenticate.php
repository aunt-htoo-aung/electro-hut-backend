<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Check Authentication
$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['user_id']) && $current_page != "login.php") {
    header('location:login.php');
    exit;
}

//Login Process from the Login Page
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    check_user_login($username, $email, $password, $conn);
}

//Check User is logined or not
function check_user_login($username, $email, $password, $conn)
{
    try {
        $check_user_query = 'SELECT * FROM User WHERE (name=? OR email=?) AND password=?';
        $stmt = $conn->prepare($check_user_query);
        $stmt->execute([$username, $email, md5($password)]);
        $result = $stmt->fetch();

        if ($result) {
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['role'] = $result['role'];
            if ($result['role'] == 'ADMIN') {
                header('location:admin_dashboard.php');
                exit;
            } else {
                header('location:index.php');
                exit;
            }
        } else {
            header('location:login.php');
            exit;
        }
    } catch (PDOException $e) {
        $e->getMessage();
    }
}