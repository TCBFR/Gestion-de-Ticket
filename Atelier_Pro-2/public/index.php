<?php
// On démarre la session une seule fois ici
session_start();

// 1. Récupération de l'action demandée
$action = $_GET['action'] ?? 'home';

// 2. Traitement de la déconnexion
if ($action === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// 3. Système de routage : Si pas de session, on affiche le login
if (empty($_SESSION['user_id'])) {
    include '../views/index.php';
} 
// 4. Si l'utilisateur est connecté, on charge la page demandée
else {
    $pages = [
        'home'                => '../views/dashboard.php',
        'create_user'         => '../views/users/create_user.php',
        'list_users'          => '../views/users/list_users.php',
        'edit_user'           => '../views/users/edit_user.php',
        'delete_user'         => '../views/users/delete_user.php',
        'create_ticket'       => '../views/tickets/create_ticket.php',
        'list_tickets'        => '../views/tickets/list_tickets.php',
        'edit_ticket'         => '../views/tickets/edit_ticket.php',
        'assign_ticket'       => '../views/assign_ticket.php',
        'my_tickets'          => '../views/my_tickets.php',
        'manage_tech_tickets' => '../views/manage_tech_tickets.php',
        'read_ticket'         => '../views/tickets/read_ticket.php',
        'my_tickets_agent'    => '../views/my_tickets_agent.php',
    ];

    $file = $pages[$action] ?? '../views/dashboard.php';

    if (file_exists($file)) {
        include $file;
    } else {
        include '../views/dashboard.php';
    }
}