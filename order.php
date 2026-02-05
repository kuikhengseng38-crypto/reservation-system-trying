<?php
include "db.php";

if(!isset($_GET['id'])) die("Order ID missing");
$order_id = intval($_GET['id']);

// 获取菜单列表
$menu = $conn->query("SELECT * FROM menu");

// 获取订单明细（关联菜单表获取菜名）
$items_res = $conn->query("
    SELECT oi.*, m.name 
    FROM order_items oi
    JOIN menu m ON oi.product_id = m.id
    WHERE oi.order_id = $order_id
");


// 初始化总价
$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order #<?= $order_id ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        // 当选择菜单菜品时自动填入价格
        function fillPrice() {
            var select = document.getElementById("menu_select");
            var priceInput = document.getElementById("price_input");
            priceInput.value = select.options[select.selectedIndex].dataset.price;
        }
    </script>
</head>
<body class="container mt-4">

<h2>Order #<?= $order_id ?></h2>

<!-- 下单表单 -->
<form action="add_item.php" method="post" class="row g-2 mb-4">
    <input type="hidden" name="order_id" value="<?= $order_id ?>">

    <div class="col-md-4">
        <select id="menu_select" name="product_id" class="form-control" onchange="fillPrice()" required>
            <option value="">Select Menu Item</option>
            <?php while($m = $menu->fetch_assoc()): ?>
                <option value="<?= $m['id'] ?>" data-price="<?= $m['price'] ?>">
                    <?= htmlspecialchars($m['name']) ?> (RM <?= number_format($m['price'],2) ?>)
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-2">
        <input type="number" id="price_input" name="price" class="form-control" readonly required>
    </div>

    <div class="col-md-2">
        <input type="number" name="qty" class="form-control" value="1" min="1" required>
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary">Add Item</button>
    </div>
</form>

<!-- 已点菜品表格 -->
<table class="table table-bordered">
    <tr>
        <th>Food</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Subtotal</th>
    </tr>

    <?php while($row = $items_res->fetch_assoc()):
        $qty = isset($row['qty']) ? $row['qty'] : 0;
        $price = isset($row['price']) ? $row['price'] : 0;
        $sub = $price * $qty;
        $total += $sub;
    ?>
    <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= number_format($price,2) ?></td>
        <td><?= $qty ?></td>
        <td><?= number_format($sub,2) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<h3>Total: RM <?= number_format($total,2) ?></h3>

</body>
</html>
