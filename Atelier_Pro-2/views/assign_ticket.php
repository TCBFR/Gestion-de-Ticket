<?php
// public/assign_ticket.php — technicien & agent municipal

require_once '../config/config.php';
require_once '../config/function.php';

requireRole(['technicien', 'agent municipal']);

$userId   = (int)$_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$userName = $_SESSION['user_name'];

// Traitement : s'assigner un ticket — statut forcé à "en cours" à l'assignation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticketId = (int)$_POST['ticket_id'];

    // assign_to IS NULL : impossible de voler un ticket déjà pris
    $stmt = $pdo->prepare('UPDATE tickets SET assign_to = ?, statut = "en cours" WHERE id = ? AND assign_to IS NULL');
    $stmt->execute([$userId, $ticketId]);

    header('Location: my_tickets.php');
    exit;
}

// Catégorie selon le rôle
$categorie = $userRole === 'technicien' ? 'Technicien' : 'Agent municipal';

$stmt = $pdo->prepare('
    SELECT t.id, t.titre, t.corps, t.adresse_lieu, t.priorite, t.date_creation
    FROM tickets t
    JOIN categories c ON t.categorie_id = c.id
    WHERE t.assign_to IS NULL AND c.nom = ?
    ORDER BY t.date_creation DESC
');
$stmt->execute([$categorie]);
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'assigner un ticket</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="page-header">
    <h1>Tickets disponibles</h1>
    <div class="user-info">
        <?= h($userName) ?> <span class="badge"><?= h($userRole) ?></span>
        &nbsp;|&nbsp; <a href="dashboard.php">← Dashboard</a>
    </div>
</div>

<?php if (empty($tickets)): ?>
    <p class="empty">Aucun ticket disponible pour le moment.</p>
<?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Adresse</th>
                    <th>Priorité</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($tickets as $t): ?>
                <tr>
                    <td><?= h($t['id']) ?></td>
                    <td><?= h($t['titre']) ?></td>
                    <td><?= h(tronquer($t['corps'])) ?></td>
                    <td><?= h($t['adresse_lieu']) ?></td>
                    <td><?= h(labelPriorite($t['priorite'])) ?></td>
                    <td><?= h($t['date_creation']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="ticket_id" value="<?= h($t['id']) ?>">
                            <button type="submit">✋ M'assigner</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

</body>
</html>