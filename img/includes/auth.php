<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php'; // Sicherstellen, dass $db verfügbar ist

function login($username, $password) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Nur benötigte Daten in die Session legen
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'] ?? 'user' // Fallback: 'user', falls leer
        ];
        return true;
    }
    return false;
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

function is_bearbeiter() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'bearbeiter';
}

function is_besteller() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'besteller';
}

// 🟢 Letzte Aktivität aktualisieren (für Online-Status)
if (isset($_SESSION['user']['id'])) {
    $stmt = $db->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
    $stmt->execute([$_SESSION['user']['id']]);
}
?>
