<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    die("Please login first <a href='login.php'>Login</a>");
}
$user_id = intval($_SESSION['user_id']);

if(!isset($_SESSION['reserve_data'])){
    die("Please complete Step 1 first <a href='index.php'>Back</a>");
}

$data = $_SESSION['reserve_data'];
$guests = intval($data['guests']);
$name = $data['name'];
$date = $data['date'];
$start = $data['start_time'];
$end = $data['end_time'];
$message = "";

// Handle reservation submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $table_id = intval($_POST['table_id'] ?? 0);

    if(!$table_id){
        $message = "Please select a table.";
    } else {
        // Check table capacity
        $stmt = $conn->prepare("SELECT capacity FROM tables WHERE id=?");
        $stmt->bind_param("i",$table_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res->num_rows === 0){
            $message = "Selected table not found.";
        } else {
            $cap = $res->fetch_assoc()['capacity'];
            if($guests > $cap){
                $message = "Guests exceed table capacity ($cap pax).";
            } else {
                // Check time conflict
                $stmt2 = $conn->prepare("
                    SELECT id FROM reservations
                    WHERE table_id=? AND reserve_date=? AND status='active'
                    AND NOT (end_time<=? OR start_time>=?)
                ");
                $stmt2->bind_param("isss",$table_id,$date,$start,$end);
                $stmt2->execute();
                $res2 = $stmt2->get_result();
                if($res2->num_rows>0){
                    $message = "Table already reserved for this time.";
                } else {
                    // Insert reservation
                    $stmt3 = $conn->prepare("
                        INSERT INTO reservations
                        (table_id,user_id,customer_name,guests,reserve_date,start_time,end_time,status)
                        VALUES (?,?,?,?,?,?,?,'active')
                    ");
                    $stmt3->bind_param("iisisss",$table_id,$user_id,$name,$guests,$date,$start,$end);
                    if($stmt3->execute()){
                        $conn->query("UPDATE tables SET status='reserved' WHERE id=$table_id");
                        unset($_SESSION['reserve_data']);
                        $message = "Reservation successful!";
                    } else {
                        $message = "Failed: ".$conn->error;
                    }
                    $stmt3->close();
                }
                $stmt2->close();
            }
        }
        $stmt->close();
    }
}

// Get tables that fit the number of guests
$tables = $conn->query("SELECT * FROM tables WHERE capacity>=$guests ORDER BY capacity, table_number");

// Get user's active reservations
$reservations = $conn->query("
SELECT r.*, t.table_number, t.capacity, o.id AS order_id
FROM reservations r
JOIN tables t ON r.table_id=t.id
LEFT JOIN orders o ON r.id = o.reservation_id
WHERE r.status='active' AND r.user_id=$user_id
ORDER BY r.reserve_date, r.start_time
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Reserve Table - Step 2</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

<h2>Step 2: Select Table (Guests: <?= $guests ?>)</h2>
<p class="text-muted">Business Hours: 10:45 - 20:55</p>

<?php if($message): ?>
<div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post" class="row g-2 mb-4">
    <div class="col-md-3">
        <select name="table_id" class="form-control" required>
            <option value="">Select Table</option>
            <?php while($t=$tables->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>">
                    Table <?= htmlspecialchars($t['table_number']) ?> (<?= intval($t['capacity']) ?> pax)
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-12 mt-2">
        <button class="btn btn-primary">Reserve</button>
    </div>
</form>

<hr>
<h3>My Active Reservations</h3>
<table class="table table-bordered">
<tr>
<th>Table</th>
<th>Name</th>
<th>Guests</th>
<th>Date</th>
<th>Time</th>
<th>Order</th>
<th>Cancel</th>
</tr>
<?php while($r = $reservations->fetch_assoc()): ?>
<tr>
<td>Table <?= $r['table_number'] ?> (<?= $r['capacity'] ?> pax)</td>
<td><?= htmlspecialchars($r['customer_name']) ?></td>
<td><?= $r['guests'] ?></td>
<td><?= $r['reserve_date'] ?></td>
<td><?= $r['start_time'] ?> - <?= $r['end_time'] ?></td>

<td>
<?php
date_default_timezone_set("Asia/Kuala_Lumpur");
$now = date("H:i");
if($now < "10:45" || $now > "20:55"): ?>
    <button class="btn btn-secondary btn-sm" disabled>Order Closed</button>
<?php else: ?>
    <?php if($r['order_id']): ?>
        <a href="order.php?id=<?= $r['order_id'] ?>" class="btn btn-primary btn-sm">Open Order</a>
    <?php else: ?>
        <a href="create_order.php?id=<?= $r['id'] ?>" class="btn btn-success btn-sm">Create Order</a>
    <?php endif; ?>
<?php endif; ?>
</td>

<td>
<a href="cancel.php?id=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this reservation?')">Cancel</a>
</td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
