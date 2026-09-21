<?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "receptionist") {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/database.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

/* Delete bill */
$stmt = $conn->prepare("DELETE FROM billing WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php");
    exit();
} else {
    die("Error deleting bill.");
}