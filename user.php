<?php
require_once 'db_connect.php';
//Select user by id
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $user_info = get_user_by_id($user_id, $conn);
}
function get_user_by_id($id, $conn)
{
    try {
        $sql = "Select * From User where user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    } catch (PDOException $e) {
        $e->getMessage();
    }
}
