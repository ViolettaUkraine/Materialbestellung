<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function login($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'] ?? 'user'
        ];
        return true;
    }
    return false;
}
function register($pdo, $username, $password, $role) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        return "Benutzername existiert bereits!";
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $hashedPassword, $role]) ? true : "Registrierung fehlgeschlagen!";
}

function require_login() {
    if (!isset($_SESSION['user'])) {
        header("Location: index.php");
        exit;
    }
}

// Nur bestimmte Rolle zulassen
function require_role($role) {
    require_login();
    if ($_SESSION['user']['role'] !== $role) {
        die("⛔ Zugriff verweigert – nur für {$role}.");
    }
}

// Helferfunktionen zur Rollenprüfung
function is_admin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function is_moderator() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'moderator';
}

function is_user() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'user';
}
?>
