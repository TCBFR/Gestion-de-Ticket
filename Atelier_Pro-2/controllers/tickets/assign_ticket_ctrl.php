<?php
// controllers/tickets/assign_ticket_ctrl.php
// BUG CORRIGÉ : ini_set + session_start déplacés dans config.php (évite la duplication)

require_once '../../config/config.php';
require_once '../../config/function.php';

requireRole(['technicien', 'agent municipal']);

$userId   = (int)    $_SESSION['user_id'];
$userRole = (string) $_SESSION['user_role'];
$userName = (string) $_SESSION['user_name'];

// Traitement : s'assigner un ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {

    $ticketId      = (int) $_POST['ticket_id'];
    $nouveauStatut = $_POST['statut'] ?? 'en cours';

    if (!in_array($nouveauStatut, ['en cours', 'termine'], true)) {
        $nouveauStatut = 'en cours';
    }

    // assign_to IS NULL : empêche de voler un ticket déjà pris
    $stmt = $pdo->prepare('UPDATE tickets SET assign_to = ?, statut = ? WHERE id = ? AND assign_to IS NULL');
    $stmt->execute([$userId, $nouveauStatut, $ticketId]);

    header('Location: ../my_tickets.php'); // BUG CORRIGÉ : chemin relatif explicite
    exit;
}

// Chargement des tickets non assignés selon la catégorie du rôle connecté
$categorieParRole = [
    'technicien'      => 'Technicien',
    'agent municipal' => 'Agent municipal',
];
$nomCategorie = $categorieParRole[$userRole];

$stmt = $pdo->prepare('
    SELECT t.id, t.titre, t.corps, t.adresse_lieu, t.priorite, t.date_creation
    FROM tickets t
    JOIN categories c ON t.categorie_id = c.id
    WHERE t.assign_to IS NULL AND c.nom = ?
    ORDER BY t.date_creation DESC
');
$stmt->execute([$nomCategorie]);
$tickets = $stmt->fetchAll();