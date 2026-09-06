<?php
// public/views/tickets/read_ticket.php
require_once '../controllers/tickets/read_ticket_ctrl.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket<?= h($ticket['id']) ?> – <?= h($ticket['titre']) ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <div class="page-header">
        <h1>Ticket <?= h($ticket['id']) ?></h1>

        </div>
    </div>

    <div class="card">

        <div class="card-row">
            <span class="card-label">Titre</span>
            <span class="card-value"><?= h($ticket['titre']) ?></span>
        </div>

        <div class="card-row">
            <span class="card-label">Description</span>
            <span class="card-value"><?= nl2br(h($ticket['corps'])) ?></span>
        </div>

        <div class="card-row">
            <span class="card-label">Adresse</span>
            <span class="card-value"><?= h($ticket['adresse_lieu']) ?></span>
        </div>

        <div class="card-row">
            <span class="card-label">Priorité</span>
            <span class="card-value"><?= h(labelPriorite($ticket['priorite'])) ?></span>
        </div>

        <div class="card-row">
            <span class="card-label">Statut</span>
            <span class="card-value">
                <span class="badge"><?= h(labelStatut($ticket['statut'])) ?></span>
            </span>
        </div>

        <div class="card-row">
            <span class="card-label">Catégorie</span>
            <span class="card-value"><?= h($ticket['categorie_nom'] ?? '—') ?></span>
        </div>

        <div class="card-row">
            <span class="card-label">Date de création</span>
            <span class="card-value"><?= h($ticket['date_creation']) ?></span>
        </div>

    </div>

    <div class="actions">
        <a href="index.php?action=my_tickets" class="btn">← Retour à la liste</a>
    </div>

</body>
</html>