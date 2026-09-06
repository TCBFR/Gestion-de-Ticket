<?php
// controllers/tickets/list_tickets_ctrl.php

require_once '../../config/config.php';
require_once '../../config/function.php';

requireRole('secretaire');

// ════════════════════════════════════════════════════
//  CLASSE : gère les tickets agents municipaux
// ════════════════════════════════════════════════════
class GestionTicketsAgents
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ── Méthode : supprimer un ticket ─────────────────
    public function supprimer(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM tickets WHERE id = ?');
        $stmt->execute([$id]);
    }

    // ── Méthode : changer le statut d'un ticket ───────
    // Retourne true si OK, false si statut invalide
    public function changerStatut(int $ticketId, string $statut): bool
    {
        $statutsValides = ['en attente', 'en cours', 'cloture'];

        if (!in_array($statut, $statutsValides, true)) {
            return false;
        }

        $stmt = $this->pdo->prepare('
            UPDATE tickets t
            JOIN categories c ON t.categorie_id = c.id
            SET t.statut = ?
            WHERE t.id = ? AND c.nom = "Agent municipal"
        ');
        $stmt->execute([$statut, $ticketId]);

        return true;
    }

    // ── Méthode : récupérer tous les tickets agents ───
    public function tousLesAgents(): array
    {
        return $this->pdo->query("
            SELECT t.id, t.titre, t.corps, t.adresse_lieu, t.priorite, t.statut,
                   t.date_creation, u.nom AS assign_nom, c.nom AS categorie_nom
            FROM tickets t
            JOIN categories c ON t.categorie_id = c.id
            LEFT JOIN utilisateurs u ON t.assign_to = u.id
            WHERE c.nom = 'Agent municipal'
            ORDER BY t.date_creation DESC
        ")->fetchAll();
    }
}

// ════════════════════════════════════════════════════
//  UTILISATION DE LA CLASSE
// ════════════════════════════════════════════════════

$gestion = new GestionTicketsAgents($pdo);

// Suppression
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $gestion->supprimer((int) $_GET['delete']);
    header('Location: list_tickets.php');
    exit;
}

// Changement de statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['statut'])) {
    $gestion->changerStatut((int) $_POST['ticket_id'], $_POST['statut']);
    header('Location: list_tickets.php');
    exit;
}

// Données pour la vue
$tickets = $gestion->tousLesAgents();