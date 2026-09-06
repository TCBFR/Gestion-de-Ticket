<?php
// controllers/my_tickets_agent_ctrl.php

require_once '../config/config.php';
require_once '../config/function.php';

requireRole(['agent municipal']);

// ──────────────────────────────────────────────
// Classe de gestion des tickets agent municipal
// ──────────────────────────────────────────────
class AgentTickets
{
    public function __construct(private PDO $pdo, private int $userId) {}

    /** Met à jour le statut d'un ticket assigné à l'agent */
    public function updateStatut(int $ticketId, string $statut): void
    {
        if (!in_array($statut, ['termine'], true)) return;

        $this->pdo
            ->prepare('UPDATE tickets SET statut = ? WHERE id = ? AND assign_to = ?')
            ->execute([$statut, $ticketId, $this->userId]);
    }

    /** Retourne les tickets assignés à l'agent (hors clôturés) */
    public function getTickets(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT t.id, t.titre, t.corps, t.adresse_lieu, t.priorite, t.statut,
                   t.date_creation, c.nom AS categorie_nom, u.nom AS technicien_nom
            FROM   tickets t
            LEFT JOIN categories  c ON t.categorie_id = c.id
            LEFT JOIN utilisateurs u ON t.assign_to    = u.id
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
$manager = new AgentTickets($pdo, (int) $_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['statut'])) {
    $manager->updateStatut((int) $_POST['ticket_id'], $_POST['statut']);
    header('Location: index.php?action=my_tickets_agent');
    exit;
}

$tickets = $manager->getTickets();