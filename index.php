<?php
// Fehler anzeigen
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db.php';
require_once 'includes/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $action = $_POST['action'] ?? '';

    echo "<pre>DEBUG:\n";
    echo "Aktion: $action\n";
    echo "Benutzername: " . $_POST['username'] . "\n";
    echo "Passwort: " . $_POST['password'] . "\n";
    echo "Rolle: " . ($_POST['role'] ?? '---') . "\n";
    echo "</pre>";

    if ($action === "login") {
        $success = login($pdo, $_POST['username'], $_POST['password']);
        if ($success) {
            echo "<p style='color:green;'>✅ Login erfolgreich! Weiterleitung ...</p>";
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "❌ Login fehlgeschlagen – falscher Benutzername oder Passwort?";
        }
    } elseif ($action === "register") {
        $result = register($pdo, $_POST['username'], $_POST['password'], $_POST['role'] ?? '');
        if ($result === true) {
            $success = "✅ Registrierung erfolgreich!";
        } else {
            $error = "❌ Registrierung fehlgeschlagen: $result";
        }
    } else {
        $error = "❌ Unbekannte Aktion: $action";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <link rel="stylesheet" href="css/style1.css" />
</head>
<body>

<div class="background-container">
  <img src="css/R.jfif" alt="Bild" class="background-image" />
</div>

<div class="login-container">
  <form action="index.php" method="post" class="login-form">
    <h2 id="formTitle">Login</h2>
    <input name="username" placeholder="Benutzername" required />
    <input name="password" type="password" placeholder="Passwort" required />
    <div id="roleSelect" style="display:none;">
      <select name="role">
        <option value="">Bitte Rolle wählen</option>
        <option value="besteller">Besteller</option>
        <option value="bearbeiter">Geschäftsstelle</option>
        <option value="admin">Admin</option>
      </select>
    </div>
    <input type="hidden" name="action" id="formAction" value="login" />
    <button type="submit">Absenden</button>
    <p class="toggle-link" onclick="toggleForm()">Noch kein Konto? Jetzt registrieren</p>
  </form>

  <!-- ✅ PHP-Ausgabe kommt NACH dem Formular -->
  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
</div>

<script src="java1.js"></script>
</body>
</html>
