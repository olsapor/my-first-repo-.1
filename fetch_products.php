<?php
include 'db_connect.php';

$sql = "SELECT * FROM product";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $name = htmlspecialchars($row["name"]);
        $brand = htmlspecialchars($row["brand"]);
        $price = number_format($row["price"], 2);
        $stock = $row["stock_qty"];

        // បិទ tag php ដើម្បីសរសេរ HTML ធម្មតា វាងាយស្រួលជាង និងមិនងាយ Error
        ?>
        <div class="card">
            <img src="https://via.placeholder.com/200" alt="product">
            <h4><?php echo $name; ?></h4>
            <p style="color:#888;">ម៉ាក: <?php echo $brand; ?></p>
            <div class="price">$<?php echo $price; ?></div>
            <p>ស្តុកនៅសល់: <?php echo $stock; ?></p>
            <button class="btn-buy" onclick="addToCart('<?php echo $name; ?>')">ទិញឥឡូវនេះ</button>
        </div>
        <?php
    }
} else {
    echo '<p style="text-align:center; width:100%;">មិនមានផលិតផលនៅឡើយទេ។</p>';
}

$conn->close();
?>