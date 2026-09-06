<?php
// controllers/read_ticket_ctrl.php

require_once '../config/config.php';
require_once '../config/function.php';

requireRole(['technicien', 'agent municipal', 'admin']);

$userId = (int) $_SESSION['user_id'];
$role   = $_SESSION['user_role'];

// Récupération et validation de l'ID passé en GET
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: my_tickets.php');
    exit;
}

$ticketId = (int) $_GET['id'];

// Les admins voient tous les tickets ; les techniciens/agents ne voient que les leurs
// Suppression du JOIN users (table inexistante)
if ($role === 'admin') {
    $stmt = $pdo->prepare('
        SELECT t.id, t.titre, t.corps, t.adresse_lieu, t.priorite, t.statut,
               t.date_creation, c.nom AS categorie_nom
        FROM tickets t
        LEFT JOIN categories c ON t.categorie_id = c.id
        WHERE t.id = ?
    ');
    $stmt->execute([$ticketId]);
} else {
    $stmt = $pdo->prepare('
        SELECT t.id, t.titre, t.corps, t.adresse_lieu, t.priorite, t.statut,
               t.date_creation, c.nom AS categorie_nom
        FROM tickets t
        LEFT JOIN categories c ON t.categorie_id = c.id
        WHERE t.id = ? AND t.assign_to = ?
    ');
    $stmt->execute([$ticketId, $userId]);
}

$ticket = $stmt->fetch();

// Ticket introuvable ou accès non autorisé
if (!$ticket) {
    header('Location: my_tickets.php');
    exit;
}