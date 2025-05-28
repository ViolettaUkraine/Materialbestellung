<?php
session_start();
require_once 'includes/db.php';  // Verbindung zur Datenbank
require_once 'includes/auth.php'; // login() Funktion

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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login / Registrierung</title>
  <link rel="stylesheet" href="css/style1.css" />
  <link href="https://fonts.googleapis.com/css2?family=Caveat&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .sticky-header {
      position: fixed;
      top: 0; left: 0; width: 100%;
      z-index: 1030;
      background-color: #343a40;
      color: white;
      padding: 0.75rem 0;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .sticky-header h1, .sticky-header small {
      color: white;
    }

    body {
      padding-top: 80px;
      font-family: Arial, sans-serif;
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      background: url('img/login.jpg') no-repeat center center fixed;
      background-size: cover;
    }

    .login-container {
      background: rgba(255, 255, 255, 0.95);
      padding: 2.5rem 3rem;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.8);
      max-width: 480px;
      width: 100%;
      text-align: center;
    }

    .login-form input, .login-form select {
      width: 100%;
      margin-bottom: 1rem;
      padding: 0.5rem 0.75rem;
      border: 1px solid #ced4da;
      border-radius: 6px;
      font-size: 1rem;
      box-sizing: border-box;
    }

    .login-form button {
      width: 100%;
      background-color: #0d6efd;
      border: none;
      color: white;
      padding: 0.6rem 1rem;
      font-size: 1.1rem;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .login-form button:hover {
      background-color: #084298;
    }

    .toggle-link {
      margin-top: 1rem;
      cursor: pointer;
      color: #ffc107;
      font-weight: 600;
    }

    .error {
      margin-top: 1rem;
      color: #dc3545;
      font-weight: 600;
    }

    .success {
      margin-top: 1rem;
      color: #198754;
      font-weight: 600;
    }

    .footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      background-color: rgba(52, 58, 64, 0.85);
      padding: 0.5rem 0;
      text-align: center;
      font-size: 0.9rem;
    }

    .footer a {
      color: white;
      margin: 0 1rem;
      text-decoration: none;
    }

    .footer a:hover {
      color: #ffc107;
    }
  </style>
</head>
<body>

<header class="sticky-header">
  <div class="container d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-0 fw-bold">📦 BüroDirekt<span style="color: #ffc107;">24</span></h1>
      <small class="d-block fst-italic" style="font-size: 0.9rem;">Ihr Partner für Bürobedarf – schnell, einfach, direkt.</small>
    </div>
    <?php if (isset($_SESSION['user'])): ?>
      <form method="post" action="logout.php" class="m-0">
        <button type="submit" class="btn btn-outline-light btn-sm">🚪 Abmelden</button>
      </form>
    <?php endif; ?>
  </div>
</header>

<div class="login-container">
  <form method="post" class="login-form" id="mainForm">
    <h2 id="formTitle">Login</h2>

    <input name="username" placeholder="Benutzername" required autocomplete="username" />
    <input name="password" type="password" placeholder="Passwort" required autocomplete="current-password" />

    <div id="registrationFields" style="display: none;">
      <input name="firstname" placeholder="Vorname" />
      <input name="lastname" placeholder="Nachname" />
      <input name="address" placeholder="Adresse" />
      <input name="city" placeholder="PLZ Ort" />
      <input name="phone" placeholder="Telefonnummer" />
      <input name="email" type="email" placeholder="E-Mail-Adresse" autocomplete="email" />
    </div>

    <input type="hidden" name="action" id="formAction" value="login" />
    <button type="submit">Absenden</button>

    <p class="toggle-link" onclick="toggleForm()">Noch kein Konto? Jetzt registrieren</p>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
  </form>
</div>

<footer class="footer">
  <a href="impressum.php">Impressum</a>
  <a href="agb.php">AGB</a>
</footer>

<script>
function toggleForm() {
  const title = document.getElementById("formTitle");
  const actionInput = document.getElementById("formAction");
  const regFields = document.getElementById("registrationFields");
  const toggleLink = document.querySelector(".toggle-link");

  const isLogin = actionInput.value === "login";

  title.textContent = isLogin ? "Registrieren" : "Login";
  actionInput.value = isLogin ? "register" : "login";
  regFields.style.display = isLogin ? "block" : "none";
  toggleLink.textContent = isLogin ? "Schon registriert? Jetzt einloggen" : "Noch kein Konto? Jetzt registrieren";
}
</script>

</body>
</html>
