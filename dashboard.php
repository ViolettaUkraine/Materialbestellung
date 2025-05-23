<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

$user = $_SESSION['user'];
$role = $user['role'];
?>

<h1>Willkommen, <?= htmlspecialchars($user['username']) ?>!</h1>

<?php if ($role === 'besteller'): ?>
    <ul>
        <li><a href="bestellen.php">Neue Bestellung erstellen</a></li>
        <li><a href="bestellungen.php">Meine Bestellungen anzeigen</a></li>
    </ul>

<?php elseif ($role === 'bearbeiter'): ?>
    <ul>
        <li><a href="bestellungen.php">Alle Bestellungen bearbeiten</a></li>
    </ul>

<?php elseif ($role === 'admin'): ?>
    <ul>
        <li><a href="admin.php">Benutzer & Materialverwaltung</a></li>
        <li><a href="bestellungen.php">Bestellübersicht</a></li>
    </ul>

<?php endif; ?>

<a href="logout.php">Abmelden</a>
