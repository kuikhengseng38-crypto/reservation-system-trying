<?php
include "db.php";

/* ===== Business Hour Control ===== */
date_default_timezone_set("Asia/Kuala_Lumpur");
$now = date("H:i");
if($now < "10:45" || $now > "20:45"){
    die("Ordering is only available from 10:45 AM to 8:45 PM");
}

/* ===== Validate ===== */
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    die("Invalid Access");
}

$order_id   = intval($_POST['order_id']);
$product_id = intval($_POST['product_id']);
$price      = floatval($_POST['price']);
$qty        = intval($_POST['qty']);

if($order_id<=0 || $product_id<=0 || $qty<=0){
    die("Invalid Data");
}

/* ===== Insert Item ===== */
$conn->query("
INSERT INTO order_items(order_id,product_id,price,qty)
VALUES ($order_id,$product_id,$price,$qty)
");

header("Location: order.php?id=$order_id");
exit;
?>
