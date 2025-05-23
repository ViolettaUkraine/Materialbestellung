<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

$stmt = $db->prepare("SELECT o.id, m.name, o.quantity, o.status, o.created_at 
                      FROM orders o
                      JOIN materials m ON o.material_id = m.id
                      WHERE o.user_id = ?");
$stmt->execute([$_SESSION['user']['id']]);
$orders = $stmt->fetchAll();
?>
<h2>Meine Bestellungen</h2>
<ul>
<?php foreach ($orders as $order): ?>
    <li><?= $order['name'] ?> – <?= $order['quantity'] ?> Stück – Status: <?= $order['status'] ?></li>
<?php endforeach; ?>
</ul>
