<?php
include "db.php";

/* ===== Business Hour Control ===== */
date_default_timezone_set("Asia/Kuala_Lumpur");
$now = date("H:i");
if($now < "10:45" || $now > "20:45"){
    die("Ordering is only available from 10:45 AM to 8:45 PM");
}

/* ===== Order ID ===== */
if(!isset($_GET['id'])) die("Order ID missing");
$order_id = intval($_GET['id']);

/* ===== Menu ===== */
$menu = $conn->query("SELECT * FROM menu");

/* ===== Order Items ===== */
$items_res = $conn->query("
SELECT oi.*, m.name
FROM order_items oi
JOIN menu m ON oi.product_id = m.id
WHERE oi.order_id = $order_id
");

/* ===== Total ===== */
$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Order #<?= $order_id ?></title>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<script>
function fillPrice(){
    var s = document.getElementById("menu_select");
    document.getElementById("price_input").value =
        s.options[s.selectedIndex].dataset.price;
}
</script>
</head>

<body class="container mt-4">

<h2>Order #<?= $order_id ?></h2>
<p class="text-muted">Ordering Time: 10:45 AM - 8:45 PM</p>

<!-- Add Item -->
<form action="add_item.php" method="post" class="row g-2 mb-4">

<input type="hidden" name="order_id" value="<?= $order_id ?>">

<div class="col-md-4">
<select id="menu_select" name="product_id"
class="form-control" onchange="fillPrice()" required>
<option value="">Select Menu</option>
<?php while($m=$menu->fetch_assoc()): ?>
<option value="<?= $m['id'] ?>"
data-price="<?= $m['price'] ?>">
<?= $m['name'] ?> (RM <?= number_format($m['price'],2) ?>)
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-2">
<input type="number" id="price_input"
name="price" class="form-control" readonly required>
</div>

<div class="col-md-2">
<input type="number" name="qty"
class="form-control" value="1" min="1" required>
</div>

<div class="col-md-2">
<button class="btn btn-primary">Add Item</button>
</div>

</form>

<!-- Order Items -->
<table class="table table-bordered">
<tr>
<th>Food</th>
<th>Price</th>
<th>Qty</th>
<th>Subtotal</th>
</tr>

<?php while($row=$items_res->fetch_assoc()):
$sub = $row['price'] * $row['qty'];
$total += $sub;
?>
<tr>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= number_format($row['price'],2) ?></td>
<td><?= $row['qty'] ?></td>
<td><?= number_format($sub,2) ?></td>
</tr>
<?php endwhile; ?>
</table>

<h3>Total: RM <?= number_format($total,2) ?></h3>

<?php
// Save total
$conn->query("UPDATE orders SET total=$total WHERE id=$order_id");
?>

</body>
</html>
