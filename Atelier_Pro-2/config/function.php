<?php
// config/function.php

function h(?string $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function tronquer(?string $t, int $max = 60): string
{
    $t = (string)$t;
    return mb_strlen($t) > $max ? mb_substr($t, 0, $max) . '…' : $t;
}

function labelPriorite(?string $p): string
{
    return match($p) {
        'low'    => '🟢 Basse',
        'medium' => '🟡 Moyenne',
        'hard'   => '🔴 Haute',
        default  => '—',
    };
}

function labelStatut(?string $s): string
{
    return match($s) {
        'en attente' => 'En attente',
        'en cours'   => 'En cours',
        'termine'    => 'Terminé',
        'cloture'    => 'Clôturé',
        default      => (string)$s,
    };
}

// Redirige vers login si pas connecté
function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

// Vérifie le rôle (string ou array)
function requireRole(string|array $roles): void
{
    requireLogin();
    if (!in_array($_SESSION['user_role'] ?? '', (array)$roles, true)) {
        http_response_code(403);
        exit('Accès refusé.');
    }
}