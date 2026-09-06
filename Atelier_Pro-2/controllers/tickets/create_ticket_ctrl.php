<?php
// controllers/tickets/create_ticket_ctrl.php
// BUG CORRIGÉ : ini_set + session_start déplacés dans config.php

require_once '../../config/config.php';
require_once '../../config/function.php';

requireRole('secretaire');

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre       = trim($_POST['titre']        ?? '');
    $corps       = trim($_POST['corps']        ?? '');
    $adresse     = trim($_POST['adresse_lieu'] ?? '');
    $priorite    = $_POST['priorite']          ?? 'medium';
    $categorieId = $_POST['categorie_id']      ?? '';

    if (empty($titre) || empty($corps) || empty($adresse)) {
        $erreur = 'Le titre, la description et l\'adresse sont obligatoires.';

    } elseif (!in_array($priorite, ['low', 'medium', 'hard'], true)) {
        $erreur = 'La priorité sélectionnée n\'est pas valide.';

    } elseif (empty($categorieId) || !ctype_digit((string) $categorieId)) {
        $erreur = 'Veuillez choisir une catégorie valide.';

    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tickets (titre, corps, adresse_lieu, priorite, categorie_id, statut)
            VALUES (?, ?, ?, ?, ?, 'en attente')
        ");
        $stmt->execute([$titre, $corps, $adresse, $priorite, (int) $categorieId]);

        $succes = 'Le ticket « ' . h($titre) . ' » a été créé avec succès.';
        $_POST  = [];
    }
}

$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();