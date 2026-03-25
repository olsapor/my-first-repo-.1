<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skincare_shop_db";

// បង្កើតការតភ្ជាប់ (Port ជាទូទៅគឺ 3306)
$conn = new mysqli($servername, $username, $password, $dbname);

// ពិនិត្យមើលការតភ្ជាប់
if ($conn->connect_error) {
    die("ការតភ្ជាប់បានបរាជ័យ: " . $conn->connect_error);
}

// កំណត់ឲ្យបង្ហាញអក្សរខ្មែរបានត្រឹមត្រូវ
$conn->set_charset("utf8");
?>