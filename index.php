<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/auth.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['action'] === 'login') {
        if (login($_POST['username'], $_POST['password'])) {
            $role = strtolower($_SESSION['user']['role']);
            switch ($role) {
                case 'admin':
                    header("Location: admin.php");
                    break;
                case 'bearbeiter':
                    header("Location: bestellungen.php");
                    break;
                case 'besteller':
                    header("Location: bestellen.php");
                    break;
                default:
                    header("Location: dashboard.php");
                    break;
            }
            exit;
        } else {
            $error = "Login fehlgeschlagen. Bitte überprüfe deine Zugangsdaten.";
        }
    } elseif ($_POST['action'] === 'register') {
        $username   = trim($_POST['username']);
        $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $firstname  = trim($_POST['firstname']);
        $lastname   = trim($_POST['lastname']);
        $address    = trim($_POST['address']);
        $city       = trim($_POST['city']);
        $phone      = trim($_POST['phone']);
        $email      = trim($_POST['email']);
        

        // Prüfen ob Benutzername bereits existiert
        $check = $db->prepare("SELECT * FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->rowCount() > 0) {
            $error = "Benutzername ist bereits vergeben.";
        } else {
            $stmt = $db->prepare("INSERT INTO users (username, password_hash, firstname, lastname, address, city, phone, email, role)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $password, $firstname, $lastname, $address, $city, $phone, $email, $role])) {
                $success = "Registrierung erfolgreich. Du kannst dich jetzt einloggen.";
            } else {
                $error = "Registrierung fehlgeschlagen. Bitte versuche es erneut.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <title>Login & Registrierung</title>
  <link rel="stylesheet" href="css/style1.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
</head>
<body class="body">

<header class="sticky-header">
  <div class="container d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-0 fw-bold">📦 <span style="color: #ffc107;">BFW</span>-Materialmanager</h1>
      <small class="d-block fst-italic" style="font-size: 0.9rem;">Ihr Partner für Bürobedarf – schnell, einfach, direkt.</small>
    </div>
    <?php if (isset($_SESSION['user'])): ?>
      <form method="post" action="logout.php" class="m-0">
        <button type="submit" class="btn btn-outline-light btn-sm">🚪 Abmelden</button>
      </form>
    <?php endif; ?>
  </div>
</header>

<div class="login-box">
  <div class="login-left">
    <form method="post" class="login-form" id="mainForm">
      <h2 id="formTitle">Herzlich willkommen! <p>Login Sie sich ein:</p> </h2>

      <input name="username" placeholder="Benutzername" required />
      <input name="password" type="password" placeholder="Passwort" required />

      <div id="registrationFields" style="display: none;">
        <input name="firstname" placeholder="Vorname" />
        <input name="lastname" placeholder="Nachname" />
        <input name="address" placeholder="Adresse" />
        <input name="city" placeholder="PLZ Ort" />
        <input name="phone" placeholder="Telefonnummer" />
        <input name="email" type="email" placeholder="E-Mail-Adresse" />

      </div>

      <input type="hidden" name="action" id="formAction" value="login" />
      <button type="submit">Anmelden</button>

      <p class="toggle-link" onclick="toggleForm()">Noch kein Konto? Jetzt registrieren</p>

      <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
      <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
    </form>
  </div>

  <div class="login-right">
    <img src="css/105.jpg" alt="Login Bild" />
  </div>
</div>

<footer class="footer">
  <a href="impressum.php">Impressum</a>
  <a href="agb.php">AGB</a>
</footer>

<script src="java1.js"></script>
</body>
</html>