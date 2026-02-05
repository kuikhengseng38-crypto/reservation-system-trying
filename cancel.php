<?php
include "db.php";

if(!isset($_GET['id'])) die("Reservation ID missing");

$id = intval($_GET['id']);

// 找桌子
$res = $conn->query("SELECT table_id FROM reservations WHERE id=$id");
$row = $res->fetch_assoc();
$table_id = $row['table_id'] ?? 0;

// 取消预订
$conn->query("UPDATE reservations SET status='cancelled' WHERE id=$id");

// 恢复桌子状态
$conn->query("UPDATE tables SET status='available' WHERE id=$table_id");

header("Location:index.php");
exit;
