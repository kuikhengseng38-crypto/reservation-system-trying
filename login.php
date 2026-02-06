<?php
// login.php
session_start();
include "db.php";

if($_SERVER['REQUEST_METHOD']=="POST"){
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE username='$username'");
    if($res->num_rows==1){
        $user = $res->fetch_assoc();
        if(password_verify($password,$user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location:index.php");
            exit;
        } else { $error="Invalid password"; }
    } else { $error="User not found"; }
}
?>
<form method="post">
<input name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button>Login</button>
<?= $error ?? '' ?>
</form>
