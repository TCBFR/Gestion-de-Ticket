<?php
// public/views/users/delete_user.php

require_once '../controllers/users/delete_user_ctrl.php';

// La suppression est gérée dans le contrôleur,
// on redirige ensuite vers la liste
header('Location: index.php?action=list_users');
exit;