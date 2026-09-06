<?php
// controllers/my_tickets_ctrl.php

require_once '../config/config.php';
require_once '../config/function.php';

requireRole(['technicien']);

// ──────────────────────────────────────────────
// Classe de gestion des tickets technicien
// ──────────────────────────────────────────────
class TechnicienTickets
{
    public function __construct(private PDO $pdo, private int $userId) {}

    /** Met à jour le statut d'un ticket assigné au technicien */
    public function updateStatut(int $ticketId, string $statut): void
    {
        if (!in_array($statut, ['en cours', 'termine'], true)) return;

        $this->pdo
            ->prepare('UPDATE tickets SET statut = ? WHERE id = ? AND assign_to = ?')
            ->execute([$statut, $ticketId, $this->userId]);
    }

    /** Retourne les tickets assignés au technicien (hors clôturés) */
    public function getTickets(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT t.id, t.titre, t.corps, t.adresse_lieu, t.priorite, t.statut,
                   t.date_creation, c.nom AS categorie_nom
            FROM   tickets t
            LEFT JOIN categories c ON t.categorie_id = c.id
            WHERE  t.assign_to = ?
              AND  t.statut IN ("en cours", "termine")
            ORDER BY t.date_creation DESC
        ');
        $stmt->execute([$this->userId]);
        return $stmt->fetchAll();
    }
}

// ──────────────────────────────────────────────
// Point d'entrée
// ──────────────────────────────────────────────
$manager = new TechnicienTickets($pdo, (int) $_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['statut'])) {
    $manager->updateStatut((int) $_POST['ticket_id'], $_POST['statut']);
    header('Location: index.php?action=my_tickets');
    exit;
}

$tickets = $manager->getTickets();