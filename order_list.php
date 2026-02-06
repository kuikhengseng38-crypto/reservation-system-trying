<?php
include "db.php";

// 获取所有订单
$sql = "
SELECT 
    o.id,
    o.total,
    o.status,
    o.reservation_id,
    o.id AS order_id
FROM orders o
ORDER BY o.id DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

<h2>All Orders</h2>

<table class="table table-bordered">
<tr>
    <th>Order ID</th>
    <th>Reservation ID</th>
    <th>Total (RM)</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['order_id'] ?></td>
    <td><?= $row['reservation_id'] ?></td>
    <td><?= number_format($row['total'],2) ?></td>
    <td><?= $row['status'] ?></td>
    <td>
        <a href="order.php?id=<?= $row['order_id'] ?>" 
           class="btn btn-sm btn-primary">
           Open
        </a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
