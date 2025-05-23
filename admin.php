<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

if ($_SESSION['user']['role'] !== 'admin') {
    die("Kein Zugriff!");
}

// Materialien oder Benutzer anzeigen oder hinzufügen...
?>
