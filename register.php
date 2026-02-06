<?php
// register.php
session_start();
include "db.php";

if($_SERVER['REQUEST_METHOD']=="POST"){
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn->query("INSERT INTO users(username,password) VALUES ('$username','$password')");
    header("Location:login.php");
    exit;
}
?>
<form method="post">
<input name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button>Register</button>
</form>
