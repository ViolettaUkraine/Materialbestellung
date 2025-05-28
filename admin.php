<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Rolle ändern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $userId = (int)$_POST['user_id'];
    $newRole = $_POST['role'];

    if (in_array($newRole, ['admin', 'bearbeiter', 'besteller'])) {
        $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$newRole, $userId]);
    }
}

// Benutzer löschen (nicht Admin)
if (isset($_GET['delete'])) {
    $userId = (int)$_GET['delete'];
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $role = $stmt->fetchColumn();

    if ($role !== 'admin') {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
    }
}

// Benutzer laden
$stmt = $db->query("SELECT id, username, email, role, last_activity FROM users ORDER BY role, username");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Online-Status prüfen
if (!function_exists('isOnline')) {
    function isOnline($lastActivity) {
        return (strtotime($lastActivity) > time() - 300); // 5 Minuten
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #343a40;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1, h2 {
            color: #34495e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color:  #3498db;
            color: white;
        }

        tr:hover {
            background-color: #ecf0f1;
        }

        select, button {
            padding: 8px;
            margin: 5px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        button {
            background: linear-gradient(135deg, #343a40, #3498db);
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        .action-links {
            text-align: center;
            padding: 10px;
        }

        .action-links a {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        .action-links a:hover {
            text-decoration: underline;
        }

        .status {
            text-align: center;
        }

        .status .online {
            color: green;
        }

        .status .offline {
            color: grey;
        }

        .role-group {
            font-weight: bold;
            background-color: #ecf0f1;
            padding: 10px;
            text-align: center;
            margin-top: 20px;
        }

        .fancy-button {
            display: inline-block;
            margin: 10px 10px 0 0;
            padding: 12px 24px;
            background: linear-gradient(135deg, #343a40, #3498db);
            color: #ffffff !important;
            font-weight: bold;
            font-size: 16px;
            text-decoration: none;
            border-radius: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .fancy-button:hover {
            background: linear-gradient(135deg, #2980b9, #343a40);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
            color: #ffffff !important;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Willkommen im Admin-Dashboard</h1>
    <p>Du bist als Administrator angemeldet. Verwalte hier alle Benutzerkonten.</p>

    <h2>Benutzerübersicht</h2>

    <h3>Aktuell online:</h3>
    <ul>
        <?php
        $onlineCount = 0;
        foreach ($users as $user):
            if (isOnline($user['last_activity'])):
                $onlineCount++;
                echo "<li>" . htmlspecialchars($user['username']) . " (" . htmlspecialchars($user['role']) . ")</li>";
            endif;
        endforeach;
        if ($onlineCount === 0):
            echo "<li>Niemand ist gerade online.</li>";
        endif;
        ?>
    </ul>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Benutzername</th>
            <th>E-Mail</th>
            <th>Rolle</th>
            <th>Status</th>
            <th>Aktionen</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $currentRole = '';
        foreach ($users as $user):
            if ($user['role'] !== $currentRole):
        ?>
            <tr class="role-group">
                <td colspan="6"><?= ucfirst($user['role']) ?></td>
            </tr>
            <?php $currentRole = $user['role']; endif; ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td class="status">
                    <span class="<?= isOnline($user['last_activity']) ? 'online' : 'offline' ?>">
                        <?= isOnline($user['last_activity']) ? '🟢 Online' : '⚪ Offline' ?>
                    </span>
                </td>
                <td>
                    <?php if ($user['id'] !== $_SESSION['user']['id'] && $user['role'] !== 'admin'): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="role">
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="bearbeiter" <?= $user['role'] === 'bearbeiter' ? 'selected' : '' ?>>Bearbeiter</option>
                                <option value="besteller" <?= $user['role'] === 'besteller' ? 'selected' : '' ?>>Besteller</option>
                            </select>
                            <button type="submit" name="action" value="update_role">Ändern</button>
                        </form>
                        <br>
                        <a href="admin.php?delete=<?= $user['id'] ?>" onclick="return confirm('Bist du sicher, dass du diesen Benutzer löschen möchtest?')">🗑️ Löschen</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="action-links">
        <a href="logout.php">Abmelden</a>
    </div>

    <div class="action-links">
        <a href="bestellungen.php" class="fancy-button">📋 Bestellungen ansehen</a>
        <a href="bestellen.php" class="fancy-button">🛒 Neue Bestellung</a>
    </div>
</div>

</body>
</html>