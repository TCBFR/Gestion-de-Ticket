<?php
require_once '../config/config.php';
require_once '../config/function.php';

// Seul l'admin peut créer un compte
requireRole('admin');

$erreur = '';
$succes = '';

// On ne fait rien si ce n'est pas un envoi de formulaire
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

// ── Récupération des champs ───────────────────────────────────────────────────
$nom   = trim($_POST['nom']   ?? '');
$email = trim($_POST['email'] ?? '');
$role  = trim($_POST['role']  ?? '');

// Mot de passe fixe pour tous les comptes (affiché dans la démo index.php)
$motDePasse      = 'role123';
$motDePasseHache = password_hash($motDePasse, PASSWORD_DEFAULT);

$rolesValides = ['admin', 'secretaire', 'technicien', 'agent municipal', 'user'];

// ── Validation ────────────────────────────────────────────────────────────────
if (empty($nom) || empty($email) || empty($role)) {
    $erreur = 'Tous les champs sont requis.';
    return;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreur = "L'adresse email n'est pas valide.";
    return;
}

if (!in_array($role, $rolesValides, true)) {
    $erreur = "Le rôle sélectionné n'est pas valide.";
    return;
}

// ── Insertion en BDD ──────────────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nom, $email, $motDePasseHache, $role]);

    $succes = "L'utilisateur « {$nom} » a été créé avec succès. Mot de passe : {$motDePasse}";
    $_POST  = [];

} catch (PDOException $e) {
    // Code 23000 = email déjà utilisé (doublon)
    if ($e->getCode() === '23000') {
        $erreur = 'Cet email est déjà utilisé par un autre compte.';
    } else {
        $erreur = 'Une erreur est survenue, veuillez réessayer.';
    }
}