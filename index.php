<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="css/style1.css">
  <link href="https://fonts.googleapis.com/css2?family=Caveat&display=swap" rel="stylesheet">
  
</head>
<body>

<div class="background-container">
  <img src="css/bild33.avif" alt="Bild" class="background-image">
</div>

<div class="login-container">
  <form method="post" class="login-form">
    <h2 id="formTitle">Login</h2>
    <input name="username" placeholder="Benutzername" required>
    <input name="password" type="password" placeholder="Passwort" required>
    <div id="roleSelect" style="display:none;">
        <select name="role">
        <option value="Besteller">Besteller</option>
        <option value="Geschäftsstelle">Geschäftsstelle</option>
        <option value="Admin">Admin</option>
      </select>
    </div>
    <input type="hidden" name="action" id="formAction" value="login">
    <button type="submit">Absenden</button>
    
    <p class="toggle-link" onclick="toggleForm()">Noch kein Konto? Jetzt registrieren</p>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  </form>
</div>

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
<script src="java1.js"></script>

</body>
</html>


