<?php
// controllers/manage_tech_tickets_ctrl.php

if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once '../config/config.php';
require_once '../config/function.php';

// ════════════════════════════════════════════════════
//  CLASSE : gère les tickets techniciens
//  Une classe = un "objet" qui regroupe des actions
//  liées à un même sujet
// ════════════════════════════════════════════════════
class GestionTicketsTech
{
    // ── Propriété ─────────────────────────────────────
    // Une propriété = une variable qui appartient à la classe
    private PDO $pdo;

    // ── Constructeur ──────────────────────────────────
    // Appelé automatiquement quand on crée l'objet
    // On lui passe $pdo pour qu'il puisse parler à la BDD
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

    // ── Méthode : modifier un ticket ──────────────────
    // Retourne un message d'erreur ou une chaîne vide si OK
    public function modifier(array $data): string
    {
        $titre    = trim($data['titre']        ?? '');
        $corps    = trim($data['corps']        ?? '');
        $adresse  = trim($data['adresse_lieu'] ?? '');
        $priorite = $data['priorite']          ?? 'medium';
        $statut   = $data['statut']            ?? 'en attente';
        $catId    = !empty($data['categorie_id']) ? (int) $data['categorie_id'] : null;
        $id       = (int) ($data['id']         ?? 0);

        // Validation
        if (empty($titre) || empty($corps) || empty($adresse)) {
            return 'Le titre, la description et l\'adresse sont obligatoires.';
        }
        if (!in_array($priorite, ['low', 'medium', 'hard'], true)) {
            return 'Priorité invalide.';
        }
        if (!in_array($statut, ['en attente', 'en cours', 'termine', 'cloture'], true)) {
            return 'Statut invalide.';
        }

        // Mise à jour BDD
        $stmt = $this->pdo->prepare('
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

        return ''; // pas d'erreur
    }

    // ── Méthode : récupérer un ticket par son ID ──────
    public function trouver(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tickets WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ── Méthode : récupérer tous les tickets technicien
    public function tousLesTechniciens(): array
    {
        return $this->pdo->query("
            SELECT t.id, t.titre, t.corps, t.adresse_lieu, t.priorite, t.statut,
                   t.date_creation, u.nom AS technicien_nom
            FROM tickets t
            JOIN categories c ON t.categorie_id = c.id
            LEFT JOIN utilisateurs u ON t.assign_to = u.id
            WHERE c.nom = 'Technicien'
            ORDER BY t.date_creation DESC
        ")->fetchAll();
    }

    // ── Méthode : récupérer toutes les catégories ─────
    public function categories(): array
    {
        return $this->pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
    }
}

// ════════════════════════════════════════════════════
//  UTILISATION DE LA CLASSE
//  On crée l'objet, puis on appelle ses méthodes
// ════════════════════════════════════════════════════

// Vérification du rôle
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['secretaire', 'user'], true)) {
    header('Location: index.php');
    exit;
}

// On crée l'objet en lui passant $pdo
$gestion = new GestionTicketsTech($pdo);

// Suppression
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $gestion->supprimer((int) $_GET['delete']);
    header('Location: index.php?action=manage_tech_tickets');
    exit;
}

// Modification
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && ctype_digit($_POST['id'])) {
    $erreur = $gestion->modifier($_POST);
    if ($erreur === '') {
        header('Location: index.php?action=manage_tech_tickets');
        exit;
    }
}

// Chargement du ticket à éditer
$ticket_edit = null;
$val         = [];

if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $ticket_edit = $gestion->trouver((int) $_GET['edit']);

    if ($ticket_edit) {
        $val = [
            'titre'        => $_POST['titre']        ?? $ticket_edit['titre'],
            'corps'        => $_POST['corps']        ?? $ticket_edit['corps'],
            'adresse_lieu' => $_POST['adresse_lieu'] ?? $ticket_edit['adresse_lieu'],
            'priorite'     => $_POST['priorite']     ?? $ticket_edit['priorite'],
            'statut'       => $_POST['statut']       ?? $ticket_edit['statut'],
            'categorie_id' => $_POST['categorie_id'] ?? $ticket_edit['categorie_id'],
        ];
    }
}

// Données pour la vue
$categories = $gestion->categories();
$tickets    = $gestion->tousLesTechniciens();