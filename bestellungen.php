<?php 
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

// Bestellung löschen (nur Admin oder Bearbeiter)
if (isset($_POST['delete_order']) && 
    ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'bearbeiter')) {

    $orderId = (int)$_POST['order_id'];
    $stmt = $db->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);

    header("Location: bestellungen.php");
    exit;
}

// Statusänderung verarbeiten
if (isset($_POST['change_status']) && 
    ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'bearbeiter')) {

    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['new_status'];

    if (in_array($newStatus, ['offen', 'in Bearbeitung', 'abgeschlossen', 'storniert'])) {
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);
        header("Location: bestellungen.php");
        exit;
    }
}

// Bestellungen abrufen (je nach Rolle)
if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'bearbeiter') {
    $stmt = $db->query("
        SELECT o.id, m.id AS material_id, m.name AS material_name, m.price, o.quantity, o.status, o.created_at,
               u.id AS user_id, u.username, u.firstname, u.lastname, u.address, u.phone, u.city
        FROM orders o
        JOIN materials m ON o.material_id = m.id
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $db->prepare("
        SELECT o.id, m.id AS material_id, m.name AS material_name, m.price, o.quantity, o.status, o.created_at,
               u.id AS user_id, u.username, u.firstname, u.lastname, u.address, u.phone, u.city
        FROM orders o
        JOIN materials m ON o.material_id = m.id
        JOIN users u ON o.user_id = u.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$_SESSION['user']['id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$total = 0;
foreach ($orders as $order) {
    $total += $order['price'] * $order['quantity'];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Bestellungen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-delete-btn {
            background-color: #ff6b6b;
            border-color: #ff6b6b;
            color: white;
        }
        .custom-delete-btn:hover {
            background-color: #ff4c4c;
            border-color: #ff4c4c;
        }
        /* Fixierter Header */
        .sticky-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1030;
            background-color: #343a40;
            color: white;
            padding: 0.75rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        body {
            padding-top: 80px; /* Höhe des Headers + etwas Luft */
        }
    </style>
</head>
<body class="bg-light">

<!-- 📦 BüroDirekt24 Logo-Leiste -->
<header class="sticky-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h4 mb-0 fw-bold" style="letter-spacing: 1px;">📦 BüroDirekt<span style="color: #ffc107;">24</span></h1>
            <small class="d-block text-white fst-italic" style="font-size: 0.9rem;">Ihr Partner für Bürobedarf – schnell, einfach, direkt.</small>
        </div>
        <form method="post" action="logout.php" class="m-0">
            <button type="submit" class="btn btn-outline-light btn-sm">🚪 Abmelden</button>
        </form>
    </div>
</header>

<div class="container py-5">
    <h1 class="mb-4">
        <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'bearbeiter'): ?>
            Alle Bestellungen
        <?php else: ?>
            Meine Bestellungen
        <?php endif; ?>
    </h1>

    <?php if (count($orders) > 0): ?>
        <?php if ($_SESSION['user']['role'] === 'besteller'): ?>
            <div class="mb-4 p-3 bg-white border rounded">
                <h5>👤 Kunde: <?= htmlspecialchars($orders[0]['firstname'] . ' ' . $orders[0]['lastname']) ?> (<?= htmlspecialchars($orders[0]['username']) ?>)</h5>
                <p>📍 Adresse: <?= htmlspecialchars($orders[0]['address']) ?>, <?= htmlspecialchars($orders[0]['city']) ?></p>
                <p>📞 Telefon: <?= htmlspecialchars($orders[0]['phone']) ?></p>
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-striped bg-white">
            <thead class="table-dark">
                <tr>
                    <th>🆔 Material-ID</th>
                    <th>📦 Material</th>
                    <th>👤 Benutzer</th>
                    <th>🔢 Menge</th>
                    <th>💶 Einzelpreis</th>
                    <th>💰 Gesamt</th>
                    <th>📅 Datum</th>
                    <th>📌 Status / Aktion</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['material_id'] ?></td>
                        <td><?= htmlspecialchars($order['material_name']) ?></td>
                        <td><?= htmlspecialchars($order['username']) ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td><?= number_format($order['price'], 2, ',', '.') ?> €</td>
                        <td><?= number_format($order['price'] * $order['quantity'], 2, ',', '.') ?> €</td>
                        <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                        <td>
                            <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'bearbeiter'): ?>
                                <form method="post" class="d-flex mb-1">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <select name="new_status" class="form-select form-select-sm me-2">
                                        <option value="offen" <?= $order['status'] === 'offen' ? 'selected' : '' ?>>offen</option>
                                        <option value="in Bearbeitung" <?= $order['status'] === 'in Bearbeitung' ? 'selected' : '' ?>>in Bearbeitung</option>
                                        <option value="abgeschlossen" <?= $order['status'] === 'abgeschlossen' ? 'selected' : '' ?>>abgeschlossen</option>
                                        <option value="storniert" <?= $order['status'] === 'storniert' ? 'selected' : '' ?>>storniert</option>
                                    </select>
                                    <button type="submit" name="change_status" class="btn btn-sm btn-primary">Ändern</button>
                                </form>
                                <form method="post" onsubmit="return confirm('Wirklich löschen?');">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit" name="delete_order" class="btn btn-sm custom-delete-btn">🗑️ Löschen</button>
                                </form>
                            <?php else: ?>
                                <?= htmlspecialchars($order['status']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="5" class="text-end fw-bold">Gesamtsumme:</td>
                    <td colspan="3" class="fw-bold"><?= number_format($total, 2, ',', '.') ?> €</td>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <div class="alert alert-info">🔎 Keine Bestellungen gefunden.</div>
    <?php endif; ?>

    <div class="alert alert-info mt-4" role="alert">
        💬 <strong>Hinweis zur Bestelländerung oder Stornierung:</strong><br>
        Sollten Sie Änderungen an Ihrer Bestellung wünschen oder diese stornieren wollen, hilft Ihnen unser Kundenservice gerne weiter.<br>
        Sie erreichen uns <strong>kostenlos unter der Service-Hotline 0800 12345</strong> – wir sind werktags von 8:00 bis 18:00 Uhr für Sie da.
    </div>

    <div class="action-links mt-4 d-flex gap-2">
        <a href="bestellen.php" class="btn btn-secondary">← Zurück zum Shop</a>
        
        <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'bearbeiter'): ?>
            <a href="material_hinzufügen.php" class="btn btn-outline-secondary">Material hinzufügen</a>
        <?php endif; ?>
    </div>

</div>
</body>
</html>