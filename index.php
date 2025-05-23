<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (login($_POST['username'], $_POST['password'])) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Login fehlgeschlagen!";
    }
}
?>
<form method="post">
    <input name="username" placeholder="Benutzername" required>
    <input name="password" type="password" placeholder="Passwort" required>
    <button type="submit">Login</button>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
</form>
