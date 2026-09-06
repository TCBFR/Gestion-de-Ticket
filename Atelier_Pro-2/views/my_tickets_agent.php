<?php
// views/my_tickets_agent.php
require_once '../controllers/my_tickets_agent_ctrl.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes tickets - Agent municipal</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<h1>Mes tickets</h1>
<div class="nav"><a href="index.php?action=home">← Dashboard</a></div>

<?php if (empty($tickets)): ?>
    <p class="empty">Aucun ticket assigné pour le moment.</p>
<?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Titre</th><th>Description</th><th>Adresse</th>
                    <th>Priorité</th><th>Statut</th><th>Catégorie</th>
                    <th>Technicien assigné</th><th>Date</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><?= h($t['id']) ?></td>
                        <td><?= h($t['titre']) ?></td>
                        <td><?= h(tronquer($t['corps'], 60)) ?></td>
                        <td><?= h($t['adresse_lieu']) ?></td>
                        <td><?= h(labelPriorite($t['priorite'])) ?></td>
                        <td><span class="badge"><?= h(labelStatut($t['statut'])) ?></span></td>
                        <td><?= h($t['categorie_nom'] ?? '—') ?></td>
                        <td><?= h($t['technicien_nom'] ?? 'Non assigné') ?></td>
                        <td><?= h($t['date_creation']) ?></td>
                        <td>
                            <?php if ($t['statut'] === 'en cours'): ?>
                                <form method="post">
                                    <input type="hidden" name="ticket_id" value="<?= h($t['id']) ?>">
                                    <input type="hidden" name="statut"    value="termine">
                                    <button type="submit">Clôs</button>
                                </form>
                            <?php endif; ?>
                            <a href="index.php?action=read_ticket&id=<?= h($t['id']) ?>">Voir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

</body>
</html>