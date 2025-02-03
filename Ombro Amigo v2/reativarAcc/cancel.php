<?php
session_start();
include '../connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.html");
    exit();
}

// Destroi a sessão e leva para Pag Inicial
session_destroy();
header("Location: ../initial page/index.html");
exit();
?>
