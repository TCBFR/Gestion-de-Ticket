<?php
// public/views/manage_tech_tickets.php
require_once '../controllers/manage_tech_tickets_ctrl.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tickets techniciens</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <h1>Tickets techniciens</h1>

    <div class="nav">
    <a href="dashboard.php">← Dashboard</a>
    </div>

    <?php if (empty($tickets)): ?>
        <p class="empty">Aucun ticket technicien pour le moment.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Adresse</th>
                        <th>Priorité</th>
                        <th>Statut</th>
                        <th>Technicien</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>

                        <?php if ($ticket_edit && (int) $ticket_edit['id'] === (int) $ticket['id']): ?>
                            <!-- ── Ligne en mode édition ── -->
                            <tr>
                                <td colspan="8">
                                    <form method="POST" action="index.php?action=manage_tech_tickets&edit=<?= h($ticket_edit['id']) ?>">
                                        <input type="hidden" name="id" value="<?= h($ticket_edit['id']) ?>">

                                        <label>Titre
                                            <input type="text" name="titre" value="<?= h($ticket_edit['titre']) ?>" required>
                                        </label>

                                        <label>Description
                                            <textarea name="corps"><?= h($ticket_edit['corps']) ?></textarea>
                                        </label>

                                        <label>Adresse
                                            <input type="text" name="adresse_lieu" value="<?= h($ticket_edit['adresse_lieu']) ?>">
                                        </label>

                                        <label>Priorité
                                            <select name="priorite">
                                                <option value="low"    <?= $ticket_edit['priorite'] === 'low'    ? 'selected' : '' ?>>Basse</option>
                                                <option value="medium" <?= $ticket_edit['priorite'] === 'medium' ? 'selected' : '' ?>>Moyenne</option>
                                                <option value="hard"   <?= $ticket_edit['priorite'] === 'hard'   ? 'selected' : '' ?>>Haute</option>
                                            </select>
                                        </label>

                                        <label>Statut
                                            <select name="statut">
                                                <option value="en attente" <?= $ticket_edit['statut'] === 'en attente' ? 'selected' : '' ?>>En attente</option>
                                                <option value="en cours"   <?= $ticket_edit['statut'] === 'en cours'   ? 'selected' : '' ?>>En cours</option>
                                                <option value="termine"    <?= $ticket_edit['statut'] === 'termine'    ? 'selected' : '' ?>>Terminé</option>
                                                <option value="cloture"    <?= $ticket_edit['statut'] === 'cloture'    ? 'selected' : '' ?>>Clôturé</option>
                                            </select>
                                        </label>

                                        <button type="submit">Enregistrer</button>
                                        <a href="index.php?action=manage_tech_tickets">Annuler</a>
                                    </form>
                                </td>
                            </tr>

                        <?php else: ?>
                            <!-- ── Ligne normale ── -->
                            <tr>
                                <td><?= h($ticket['titre']) ?></td>
                                <td><?= h(tronquer($ticket['corps'], 60)) ?></td>
                                <td><?= h($ticket['adresse_lieu']) ?></td>
                                <td><?= h(labelPriorite($ticket['priorite'])) ?></td>
                                <td><span class="badge"><?= h(labelStatut($ticket['statut'])) ?></span></td>
                                <td><?= h($ticket['technicien_nom'] ?? 'Non assigné') ?></td>
                                <td><?= h($ticket['date_creation']) ?></td>
                                <td>
                                    <a href="index.php?action=edit_ticket&id=<?= h($ticket['id']) ?>">Modifier</a>
                                    <a href="index.php?action=manage_tech_tickets&delete=<?= h($ticket['id']) ?>"
                                       onclick="return confirm('Supprimer ce ticket ?')"
                                       style="color:#b00;margin-left:8px">Supprimer</a>
                                </td>
                            </tr>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</body>
</html>