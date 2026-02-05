<?php
include "db.php";

$tables = $conn->query("SELECT * FROM tables");
$reservations = $conn->query("
SELECT r.*, t.table_number
FROM reservations r
JOIN tables t ON r.table_id=t.id
WHERE r.status='active'
ORDER BY r.reserve_date, r.start_time
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Restaurant System</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

<h2>Reserve Table</h2>

<form action="reserve.php" method="post" class="row g-2">

<div class="col-md-3">
<select name="table_id" class="form-control" required>
<option value="">Select Table</option>
<?php while($t=$tables->fetch_assoc()): ?>
<option value="<?= $t['id'] ?>">
Table <?= $t['table_number'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-3">
<input type="text" name="name" class="form-control" placeholder="Customer Name" required>
</div>

<div class="col-md-2">
<input type="date" name="date" class="form-control" required>
</div>

<div class="col-md-2">
<input type="time" name="start_time" class="form-control" required>
</div>

<div class="col-md-2">
<input type="time" name="end_time" class="form-control" required>
</div>

<div class="col-md-12">
<button class="btn btn-primary">Reserve</button>
</div>

</form>

<hr>

<h3>Active Reservations</h3>
<table class="table table-bordered">
<tr>
<th>Table</th>
<th>Name</th>
<th>Date</th>
<th>Time</th>
<th>Action</th>
</tr>

<?php while($r=$reservations->fetch_assoc()): ?>
<tr>
<td><?= $r['table_number'] ?></td>
<td><?= $r['customer_name'] ?></td>
<td><?= $r['reserve_date'] ?></td>
<td><?= $r['start_time'] ?> - <?= $r['end_time'] ?></td>
<td>
<a href="create_order.php?id=<?= $r['id'] ?>" class="btn btn-success btn-sm">Order</a>
<a href="cancel.php?id=<?= $r['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
</td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
