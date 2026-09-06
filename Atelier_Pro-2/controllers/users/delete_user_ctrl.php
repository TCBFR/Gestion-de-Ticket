<?php
// controllers/users/delete_user_ctrl.php

require_once '../config/config.php';

// Sécurité : seul un admin peut supprimer
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php?action=home');
    exit;
}

// Récupération et validation de l'id
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php?action=list_users');
    exit;
}

// Empêcher l'admin de se supprimer lui-même
if ($id === (int) $_SESSION['user_id']) {
    header('Location: index.php?action=list_users&erreur=auto_suppression');
    exit;
}

// Suppression en base
$stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = :id');
$stmt->execute([':id' => $id]);

header('Location: index.php?action=list_users&succes=supprime');
exit;