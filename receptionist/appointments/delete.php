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

/* Check appointment exists */
$stmt = $conn->prepare("SELECT id FROM appointments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Appointment not found.");
}

/* Delete appointment */
$delete = $conn->prepare("DELETE FROM appointments WHERE id = ?");
$delete->bind_param("i", $id);

if ($delete->execute()) {
    header("Location: index.php");
    exit();
} else {
    die("Error deleting appointment.");
}
?>