<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])) {
    die("Please login first <a href='login.php'>Login</a>");
}
$user_id = intval($_SESSION['user_id']);
$message = "";

// Handle initial submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $guests = intval($_POST['guests'] ?? 0);
    $name   = trim($_POST['name'] ?? '');
    $date   = $_POST['date'] ?? '';
    $start  = $_POST['start_time'] ?? '';
    $end    = $_POST['end_time'] ?? '';

    if($guests < 1 || !$name || !$date || !$start || !$end){
        $message = "All fields are required.";
    } elseif($start >= $end){
        $message = "Start time must be before end time.";
    } elseif($start < "10:45" || $end > "20:55"){
        $message = "Reservations allowed only 10:45 - 20:55.";
    } else {
        // Validate date
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if(!$dateObj || $dateObj->format('Y-m-d') !== $date){
            $message = "Invalid date format.";
        } else {
            // Save data to session
            $_SESSION['reserve_data'] = [
                'guests' => $guests,
                'name' => $name,
                'date' => $date,
                'start_time' => $start,
                'end_time' => $end
            ];
            header("Location: reserve.php");
            exit;
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Reserve Table - Step 1</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

<h2>Step 1: Enter Basic Info</h2>
<p class="text-muted">Business hours: 10:45 - 20:55</p>

<?php if($message): ?>
<div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post" class="row g-2">
    <div class="col-md-2">
        <input type="number" name="guests" class="form-control" placeholder="Guests" min="1" required>
    </div>
    <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="Customer Name" required>
    </div>
    <div class="col-md-2">
        <input type="date" name="date" class="form-control" required min="<?= date('Y-m-d') ?>">
    </div>
    <div class="col-md-1">
        <input type="time" name="start_time" class="form-control" required>
    </div>
    <div class="col-md-1">
        <input type="time" name="end_time" class="form-control" required>
    </div>
    <div class="col-md-12 mt-2">
        <button class="btn btn-primary">Next</button>
    </div>
</form>

</body>
</html>
