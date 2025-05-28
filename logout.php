<?php
session_start(); // Session starten

// Alle Session-Daten löschen
$_SESSION = [];

// Session zerstören
session_destroy();

// Zurück zur Login-Seite
header("Location: index.php");
exit;
?>
