<?php
session_start();
require_once 'config.php';

$count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $conn = getDBConnection();
    
    // រាប់ចំនួនទំនិញក្នុងកន្ត្រកសម្រាប់ user នេះ
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $count = $row['total'] ? $row['total'] : 0;
    
    $stmt->close();
    $conn->close();
}

header('Content-Type: application/json');
echo json_encode(['count' => $count]);