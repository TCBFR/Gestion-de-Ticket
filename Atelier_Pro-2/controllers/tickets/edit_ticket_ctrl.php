<?php
// controllers/tickets/edit_ticket_ctrl.php

require_once '../config/config.php';
require_once '../config/function.php';

if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['secretaire', 'user'], true)) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? '';
if (!$id || !ctype_digit((string) $id)) {
    header('Location: index.php?action=list_tickets');
    exit;
}
$id = (int) $id;

$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ?');
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header('Location: index.php?action=list_tickets');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre    = trim($_POST['titre']        ?? '');
    $corps    = trim($_POST['corps']        ?? '');
    $adresse  = trim($_POST['adresse_lieu'] ?? '');
    $priorite = $_POST['priorite']          ?? 'medium';
    $statut   = $_POST['statut']            ?? 'en attente';
    $catId    = !empty($_POST['categorie_id']) ? (int) $_POST['categorie_id'] : null;

    if (empty($titre) || empty($corps) || empty($adresse)) {
        $erreur = 'Le titre, la description et l\'adresse sont obligatoires.';
    } elseif (!in_array($priorite, ['low', 'medium', 'hard'], true)) {
        $erreur = 'Priorité invalide.';
    } elseif (!in_array($statut, ['en cours', 'cloture'], true)) {
        $erreur = 'Statut invalide.';
    } else {
        $stmt = $pdo->prepare('
            UPDATE tickets
            SET titre        = ?,
                corps        = ?,
                adresse_lieu = ?,
                priorite     = ?,
                statut       = ?,
                categorie_id = ?
            WHERE id = ?
        ');
        $stmt->execute([$titre, $corps, $adresse, $priorite, $statut, $catId, $id]);

        // Toutes les redirections passent par le routeur
        $retour = $_SESSION['user_role'] === 'user'
            ? 'index.php?action=manage_tech_tickets'
            : 'index.php?action=list_tickets';

        header('Location: ' . $retour);
        exit;
    }
}

$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();

$val = [
    'titre'        => $_POST['titre']        ?? $ticket['titre'],
    'corps'        => $_POST['corps']        ?? $ticket['corps'],
    'adresse_lieu' => $_POST['adresse_lieu'] ?? $ticket['adresse_lieu'],
    'priorite'     => $_POST['priorite']     ?? $ticket['priorite'],
    'statut'       => $_POST['statut']       ?? $ticket['statut'],
    'categorie_id' => $_POST['categorie_id'] ?? $ticket['categorie_id'],
];