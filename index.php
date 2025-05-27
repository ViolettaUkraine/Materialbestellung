<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === "login") {
        if (login($pdo, $_POST['username'], $_POST['password'])) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "❌ Login fehlgeschlagen – falscher Benutzername oder Passwort.";
        }
    } elseif ($action === "register") {
        $result = register($pdo, $_POST['username'], $_POST['password'], $_POST['role'] ?? '');
        $success = $result === true
            ? "✅ Registrierung erfolgreich! Du kannst dich jetzt einloggen."
            : "❌ Registrierung fehlgeschlagen: $result";
    } else {
        $error = "❌ Unbekannte Aktion.";
    }
}
?>

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
  <img src="css/R.jfif" alt="Bild" class="background-image">
</div>

<div class="login-container">
  <form action="index.php" method="post" class="login-form">
    <h2 id="formTitle">Login</h2>
    <input name="username" placeholder="Benutzername" required>
    <input name="password" type="password" placeholder="Passwort" required>

    <div id="roleSelect" style="display:none;">
      <select name="role" required>
        <option value="">Bitte Rolle wählen</option>
        <option value="besteller">Besteller</option>
        <option value="bearbeiter">Geschäftsstelle</option>
        <option value="admin">Admin</option>
      </select>
    </div>

    <input type="hidden" name="action" id="formAction" value="login">
    <button type="submit">Absenden</button>

    <p class="toggle-link" onclick="toggleForm()">Noch kein Konto? Jetzt registrieren</p>

    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
      <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
  </form>
</div>

<script src="java1.js"></script>
</body>
</html>
