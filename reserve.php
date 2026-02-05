<?php
include "db.php";

if($_SERVER['REQUEST_METHOD'] !== 'POST') die("Invalid Access");

$table = intval($_POST['table_id']);
$name = trim($_POST['name']);
$date = $_POST['date'];
$start = $_POST['start_time'];
$end = $_POST['end_time'];

// 检查时间冲突
$check = $conn->query("
SELECT id FROM reservations
WHERE table_id=$table
AND reserve_date='$date'
AND status='active'
AND NOT (end_time <= '$start' OR start_time >= '$end')
");

if($check->num_rows > 0){
    die("Table already reserved! <a href='index.php'>Back</a>");
}

// 插入预订
$conn->query("
INSERT INTO reservations
(table_id, customer_name, reserve_date, start_time, end_time)
VALUES ($table,'$name','$date','$start','$end')
");

// 更新桌子状态
$conn->query("UPDATE tables SET status='reserved' WHERE id=$table");

header("Location:index.php");
exit;
