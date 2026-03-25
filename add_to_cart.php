<?php
session_start();
require_once 'config.php';

// ១. ឆែកមើលថាបាន Login ឬនៅ?
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'សូមចូលប្រើប្រាស់ (Login) ជាមុនសិន!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    
    $conn = getDBConnection();

    // ២. ឆែកមើលថា តើមានផលិតផលហ្នឹងក្នុងកន្ត្រករួចហើយឬនៅ?
    // ចំណុចសំខាន់៖ ត្រូវប្រាកដថាឈ្មោះ column ក្នុង table cart របស់អ្នកគឺ user_id និង product_id
    $check_stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // បើមានហើយ ថែមចំនួន +1
        $new_qty = $row['quantity'] + 1;
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $new_qty, $row['id']);
        
        if ($update_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'បានបច្ចុប្បន្នភាពចំនួនទំនិញ!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'មិនអាច Update បាន៖ ' . $conn->error]);
        }
    } else {
        // បើមិនទាន់មាន គឺបញ្ចូលថ្មី (Insert)
        $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $insert_stmt->bind_param("ii", $user_id, $product_id);
        
        if ($insert_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'បានថែមទៅក្នុងកន្ត្រក!']);
        } else {
            // បើ Insert មិនចូល វានឹងប្រាប់ថាខុសអ្វី (ឧទាហរណ៍៖ ខុសឈ្មោះ Table ឬ Column)
            echo json_encode(['status' => 'error', 'message' => 'កំហុស Database៖ ' . $conn->error]);
        }
    }
    $conn->close();
}
?>