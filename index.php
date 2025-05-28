<?php
// Fehler anzeigen
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once 'includes/db.php';
require_once 'includes/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['action'] === 'login') {
        if (login($_POST['username'], $_POST['password'])) {
            $role = strtolower($_SESSION['user']['role']);
            switch ($role) {
                case 'admin':
                    header("Location: admin.php");
                    break;
                case 'bearbeiter':
                case 'geschäftsstelle':
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
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $role = 'besteller';

        // Prüfen ob Benutzername bereits existiert
        $check = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->rowCount() > 0) {
            $error = "Benutzername ist bereits vergeben.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, firstname, lastname, address, city, phone, email, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
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

     <div id="registrationFields" style="display: none;">
      <input name="firstname" placeholder="Vorname" />
      <input name="lastname" placeholder="Nachname" />
      <input name="address" placeholder="Adresse" />
      <input name="city" placeholder="PLZ Ort" />
      <input name="phone" placeholder="Telefonnummer" />
      <input name="email" type="email" placeholder="E-Mail-Adresse" autocomplete="email" />
    </div>
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

<footer class="footer">
  <a href="impressum.php">Impressum</a>
  <a href="agb.php">AGB</a>
</footer>


<script src="java1.js"></script>
</body>
</html>
