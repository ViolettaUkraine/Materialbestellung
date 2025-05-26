<?php 
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

// Bestellungen abrufen inkl. Kundendaten
$stmt = $db->prepare("
    SELECT o.id, m.name AS material_name, m.price, o.quantity, o.status, o.created_at,
           u.id AS user_id, u.username, u.firstname, u.lastname, u.address, u.phone, u.city
    FROM orders o
    JOIN materials m ON o.material_id = m.id
    JOIN users u ON o.user_id = u.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");

$stmt->execute([$_SESSION['user']['id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($orders as $order) {
    $total += $order['price'] * $order['quantity'];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Bestellungen Kunde #<?= htmlspecialchars($_SESSION['user']['id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4">Kundennummer: <?= htmlspecialchars($_SESSION['user']['id']) ?></h1>

    <?php if (count($orders) > 0): ?>
        <div class="mb-4 p-3 bg-white border rounded">
            <h5>👤 Kunde: <?= htmlspecialchars($orders[0]['firstname'] . ' ' . $orders[0]['lastname']) ?> (<?= htmlspecialchars($orders[0]['username']) ?>)</h5>
            <p>📍 Adresse: <?= htmlspecialchars($orders[0]['address']) ?>, <?= htmlspecialchars($orders[0]['city']) ?></p>
            <p>📞 Telefon: <?= htmlspecialchars($orders[0]['phone']) ?></p>
        </div>

        <table class="table table-bordered table-striped bg-white">
            <thead class="table-dark">
                <tr>
                    <th>📦 Material</th>
                    <th>🔢 Menge</th>
                    <th>💶 Einzelpreis</th>
                    <th>💰 Gesamt</th>
                    <th>📅 Datum</th>
                    <th>📌 Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['material_name']) ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td><?= number_format($order['price'], 2, ',', '.') ?> €</td>
                        <td><?= number_format($order['price'] * $order['quantity'], 2, ',', '.') ?> €</td>
                        <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="3" class="text-end fw-bold">Gesamtsumme:</td>
                    <td colspan="3" class="fw-bold"><?= number_format($total, 2, ',', '.') ?> €</td>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <div class="alert alert-info">🔎 Du hast noch keine Bestellungen aufgegeben.</div>
    <?php endif; ?>
</div>

</body>
</html>
