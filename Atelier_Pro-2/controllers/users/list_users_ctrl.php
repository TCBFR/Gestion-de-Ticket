<?php
// controllers/users/list_users_ctrl.php

require_once '../config/config.php';
require_once '../config/function.php';

requireRole('admin');

// ════════════════════════════════════════════════════
//  CLASSE : gère les utilisateurs
// ════════════════════════════════════════════════════
class GestionUtilisateurs
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ── Méthode : récupérer tous les utilisateurs ─────
    public function tous(): array
    {
        return $this->pdo->query('
            SELECT id, nom, email, role, created_at
            FROM utilisateurs
            ORDER BY role, nom
        ')->fetchAll();
    }
}

// ════════════════════════════════════════════════════
//  UTILISATION DE LA CLASSE
// ════════════════════════════════════════════════════

$gestion = new GestionUtilisateurs($pdo);

// Données pour la vue
$users = $gestion->tous();