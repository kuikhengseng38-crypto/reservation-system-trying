<?php
include "db.php";

if($_SERVER['REQUEST_METHOD'] !== 'POST') die("Invalid Access");

if(!isset($_POST['order_id']) || !isset($_POST['product_id']) || !isset($_POST['qty'])){
    die("Missing data");
}

$order = intval($_POST['order_id']);
$product_id = intval($_POST['product_id']);
$qty = intval($_POST['qty']);

// 获取菜单价格
$res = $conn->query("SELECT price FROM menu WHERE id=$product_id");
$row = $res->fetch_assoc();
$price = floatval($row['price']);

// 插入订单明细
$conn->query("
    INSERT INTO order_items(order_id, product_id, price, qty)
    VALUES ($order, $product_id, $price, $qty)
");

// 更新订单总价
$conn->query("
    UPDATE orders
    SET total = (SELECT SUM(price*qty) FROM order_items WHERE order_id=$order)
    WHERE id=$order
");

header("Location: order.php?id=$order");
exit;
