<?php
// public/views/users/list_users.php

require_once '../controllers/users/list_users_ctrl.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>

<div class="page-header">
    <h1>👥 Utilisateurs</h1>
    <div class="user-info">
        <?= h($_SESSION['user_name']) ?>
        <span class="badge"><?= h($_SESSION['user_role']) ?></span>
        &nbsp;|&nbsp;
        <a href="index.php?action=create_user">+ Nouvel utilisateur</a>
        &nbsp;|&nbsp;
        <a href="index.php?action=home">Dashboard</a>
    </div>
</div>

<?php if (empty($users)): ?>
    <p class="empty">Aucun utilisateur trouvé.</p>
<?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= h($user['nom']) ?></td>
                        <td><?= h($user['email']) ?></td>
                        <td><span class="badge"><?= h($user['role']) ?></span></td>
                        <td><?= h($user['created_at']) ?></td>
                        <td class="actions">

                            <!-- Bouton modifier -->
                            <a href="index.php?action=edit_user&id=<?= h($user['id']) ?>"
                               class="btn btn-secondary">✏️ Modifier</a>

                            <!-- Bouton supprimer avec confirmation -->
                            <a href="index.php?action=delete_user&id=<?= h($user['id']) ?>"
                               class="btn btn-danger"
                               onclick="return confirm('Supprimer « <?= h($user['nom']) ?> » ?')">
                               🗑️ Supprimer
                            </a>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

</body>
</html>