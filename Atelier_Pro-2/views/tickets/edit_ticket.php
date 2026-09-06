<?php
// public/views/tickets/edit_ticket.php
require_once '../controllers/tickets/edit_ticket_ctrl.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le ticket #<?= h($id) ?></title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>

<div class="page-header">
    <h1>✏️ Modifier le ticket #<?= h($id) ?></h1>
    <div class="user-info">
        <?= h($_SESSION['user_name']) ?>
        <span class="badge"><?= h($_SESSION['user_role']) ?></span>
        &nbsp;|&nbsp;
        <?php if ($_SESSION['user_role'] === 'user'): ?>
            <a href="index.php?action=manage_tech_tickets">← Retour à la liste</a>
        <?php else: ?>
            <a href="index.php?action=list_tickets">← Retour à la liste</a>
        <?php endif; ?>
        &nbsp;|&nbsp; <a href="index.php?action=home">Dashboard</a>
    </div>
</div>

<?php if ($erreur): ?>
    <div class="error"><?= h($erreur) ?></div>
<?php endif; ?>

<div class="form-card">
    <form method="post">

        <div class="champ">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" required
                   value="<?= h($val['titre']) ?>">
        </div>

        <div class="champ">
            <label for="corps">Description *</label>
            <textarea id="corps" name="corps" rows="5" required><?= h($val['corps']) ?></textarea>
        </div>

        <div class="champ">
            <label for="adresse_lieu">Adresse / lieu *</label>
            <input type="text" id="adresse_lieu" name="adresse_lieu" required
                   value="<?= h($val['adresse_lieu']) ?>">
        </div>

        <div class="form-row">
            <div class="champ">
                <label for="priorite">Priorité</label>
                <select id="priorite" name="priorite">
                    <?php foreach (['low' => '🟢 Basse', 'medium' => '🟡 Moyenne', 'hard' => '🔴 Haute'] as $v => $label): ?>
                        <option value="<?= $v ?>" <?= $val['priorite'] === $v ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="champ">
                <label for="statut">Statut</label>
                <select id="statut" name="statut">
                    <?php foreach (['en cours' => 'En cours', 'cloture' => 'Clôturé'] as $v => $label): ?>
                        <option value="<?= $v ?>" <?= $val['statut'] === $v ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="champ">
                <label for="categorie_id">Catégorie</label>
                <select id="categorie_id" name="categorie_id">
                    <option value="">— Aucune —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= h($cat['id']) ?>" <?= $val['categorie_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= h($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit">💾 Enregistrer</button>
            <?php if ($_SESSION['user_role'] === 'user'): ?>
                <a href="index.php?action=manage_tech_tickets" class="btn btn-secondary">Annuler</a>
            <?php else: ?>
                <a href="index.php?action=list_tickets" class="btn btn-secondary">Annuler</a>
            <?php endif; ?>
        </div>

    </form>
</div>

</body>
</html>