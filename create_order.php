<?php
include "db.php";

if(!isset($_GET['id'])) die("Reservation ID missing");

$res_id = intval($_GET['id']);

$conn->query("INSERT INTO orders(reservation_id) VALUES ($res_id)");

$order_id = $conn->insert_id;

header("Location:order.php?id=$order_id");
exit;
