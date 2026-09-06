<?php
// public/views/users/edit_user.php

require_once '../controllers/users/edit_user_ctrl.php';

$rolesDisponibles = [
    'admin'           => 'Admin',
    'secretaire'      => 'Secrétaire',
    'technicien'      => 'Technicien',
    'agent municipal' => 'Agent Municipal',
    'user'            => 'Utilisateur',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'utilisateur</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>

<div class="page-header">
    <h1>✏️ Modifier l'utilisateur</h1>
    <div class="user-info">
        <?= h($_SESSION['user_name']) ?>
        <span class="badge"><?= h($_SESSION['user_role']) ?></span>
        &nbsp;|&nbsp;
        <a href="index.php?action=list_users">← Retour à la liste</a>
        &nbsp;|&nbsp;
        <a href="index.php?action=home">Dashboard</a>
    </div>
</div>

<?php if (!empty($erreur)): ?>
    <div class="error">❌ <?= h($erreur) ?></div>
<?php endif; ?>

<?php if (!empty($succes)): ?>
    <div class="success">✅ <?= h($succes) ?></div>
<?php endif; ?>

<div class="form-card">
    <form method="post">

        <div class="champ">
            <label for="nom">Nom complet *</label>
            <input type="text" id="nom" name="nom"
                   value="<?= h($_POST['nom'] ?? $user['nom']) ?>"
                   placeholder="Ex : Marie Dupont" required>
        </div>

        <div class="champ">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email"
                   value="<?= h($_POST['email'] ?? $user['email']) ?>"
                   placeholder="marie@mairie.fr" required>
        </div>

        <div class="champ">
            <label for="role">Rôle *</label>
            <select id="role" name="role" required>
                <option value="">— Choisir un rôle —</option>
                <?php foreach ($rolesDisponibles as $valeur => $libelle): ?>
                    <option value="<?= h($valeur) ?>"
                        <?= ($user['role'] === $valeur) ? 'selected' : '' ?>>
                        <?= h($libelle) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit">💾 Enregistrer</button>
            <a href="index.php?action=list_users" class="btn btn-secondary">Annuler</a>
        </div>

    </form>
</div>

</body>
</html>