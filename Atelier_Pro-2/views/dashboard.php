<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/function.php';

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$nom  = $_SESSION['user_name'] ?? '';
$role = $_SESSION['user_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="card">

    <div class="card-header">
        <div>
            <h1><?= h($nom) ?></h1>
            <span class="badge"><?= h($role) ?></span>
        </div>
        <a href="index.php?action=logout" class="logout">Déconnexion →</a>
    </div>

    <hr>

    <?php if ($role === 'admin'): ?>

        <h2>Gestion des comptes</h2>
        <div class="menu">
            <a href="index.php?action=create_user"  class="btn-menu">➕ Créer un compte</a>
            <a href="index.php?action=list_users"   class="btn-menu">👥 Tous les comptes</a>
        </div>

    <?php elseif ($role === 'secretaire'): ?>

        <h2>Gestion des tickets</h2>
        <div class="menu">
            <a href="index.php?action=create_ticket" class="btn-menu">➕ Créer un ticket</a>
            <a href="index.php?action=list_tickets"  class="btn-menu">📋 Liste des tickets agents municipaux</a>
        </div>

    <?php elseif ($role === 'technicien'): ?>

        <h2>Mes tickets technicien</h2>
        <div class="menu">
            <a href="index.php?action=assign_ticket" class="btn-menu">✋ S'assigner un ticket</a>
            <a href="index.php?action=my_tickets"    class="btn-menu">📋 Mes tickets assignés</a>
        </div>

    <?php elseif ($role === 'agent municipal'): ?>

        <h2>Mes tickets agent municipal</h2>
        <div class="menu">
            <a href="index.php?action=assign_ticket" class="btn-menu">✋ S'assigner un ticket</a>
            <a href="index.php?action=my_tickets_agent" class="btn-menu">📋 Mes tickets assignés</a>
        </div>

    <?php elseif ($role === 'user'): ?>

        <h2>Suivi des tickets techniciens</h2>
        <div class="menu">
            <a href="index.php?action=manage_tech_tickets" class="btn-menu">🔧 Gérer les tickets techniciens</a>
        </div>

    <?php else: ?>

        <p class="no-action">Aucune action disponible pour ce rôle.</p>

    <?php endif; ?>

</div>

</body>
</html>