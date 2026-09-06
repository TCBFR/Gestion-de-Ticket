<?php
// controllers/users/edit_user_ctrl.php

require_once '../config/config.php';
require_once '../config/function.php';

requireRole('admin');

$erreur = '';
$succes = '';

// ── Récupération de l'ID dans l'URL ──────────────────────────────────────────
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id === 0) {
    header('Location: index.php?action=list_users');
    exit;
}

// ── Chargement de l'utilisateur depuis la BDD ─────────────────────────────────
$stmt = $pdo->prepare('SELECT id, nom, email, role FROM utilisateurs WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

// Si l'utilisateur n'existe pas, on redirige
if (!$user) {
    header('Location: index.php?action=list_users');
    exit;
}

$rolesValides = ['admin', 'secretaire', 'technicien', 'agent municipal', 'user'];

// ── Traitement du formulaire ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom   = trim($_POST['nom']   ?? '');
    $email = trim($_POST['email'] ?? '');
    $role  = trim($_POST['role']  ?? '');

    // Validation
    if (empty($nom) || empty($email) || empty($role)) {
        $erreur = 'Tous les champs sont requis.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";

    } elseif (!in_array($role, $rolesValides, true)) {
        $erreur = "Le rôle sélectionné n'est pas valide.";

    } else {
        // Mise à jour en BDD
        try {
            $stmt = $pdo->prepare('
                UPDATE utilisateurs
                SET nom = ?, email = ?, role = ?
                WHERE id = ?
            ');
            $stmt->execute([$nom, $email, $role, $id]);

            $succes = "L'utilisateur « {$nom} » a été modifié avec succès.";

            // On recharge l'utilisateur pour afficher les nouvelles valeurs
            $user = ['id' => $id, 'nom' => $nom, 'email' => $email, 'role' => $role];

        } catch (PDOException $e) {
            // Code 23000 = email déjà utilisé
            if ($e->getCode() === '23000') {
                $erreur = 'Cet email est déjà utilisé par un autre compte.';
            } else {
                $erreur = 'Une erreur est survenue, veuillez réessayer.';
            }
        }
    }
}