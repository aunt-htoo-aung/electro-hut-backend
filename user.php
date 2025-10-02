<?php
require_once 'db_connect.php';
if (!isset($_SESSION)) {
    session_start();
}
$user_id = $_SESSION['user_id'];
//Select user by id

$user_info = get_user_by_id($user_id, $conn);

if (isset($_POST['update_profile_details'])) {
    $profile_details = [
        "first_name" => $_POST['first_name'],
        "last_name" => $_POST['last_name'],
        "email" => $_POST['email'],
        "phone" => $_POST['phone'],
        "gender" => $_POST['gender'],
        "date_of_birth" => $_POST['date_of_birth']
    ];
    $message = update_user_by_id($user_id, $conn, $profile_details);
    $_SESSION['message'] = $message;
    header('location:profile.php');
}
// if (isset($_SESSION['message'])) {
//     echo '<div class="alert">' . $_SESSION['message'] . '</div>';
//     unset($_SESSION['message']); // ✅ Remove it so it doesn’t persist
// }
function get_user_by_id($id, $conn)
{
    try {
        $sql = "SELECT * From User where user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    } catch (PDOException $e) {
        $e->getMessage();
    }
}

function update_user_by_id($id, $conn, $info)
{
    try {
        $sql = "UPDATE User SET first_name=?, last_name=?, email=?,phone=?,gender=?,date_of_birth=? where user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$info['first_name'], $info['last_name'], $info['email'], $info['phone'], $info['gender'], $info['date_of_birth'], $id]);
        $message = "Successfully Update Profile Details.";
    } catch (PDOException $e) {
        $e->getMessage();
        $message = "Failed Update Profile Details.";
    }
    return $message;
}
