<?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "receptionist") {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/database.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($id <= 0) {
    die("Invalid patient ID.");
}


/* Check whether patient exists */

$stmt = $conn->prepare("SELECT id FROM patients WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Patient not found.");
}


/* Check related appointments */

$stmt = $conn->prepare("SELECT id FROM appointments WHERE patient_id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();

$appointment_result = $stmt->get_result();

if ($appointment_result->num_rows > 0) {
    die("This patient cannot be deleted because appointments are linked to this patient.");
}


/* Check related consultations */

$stmt = $conn->prepare("SELECT id FROM consultations WHERE patient_id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();

$consultation_result = $stmt->get_result();

if ($consultation_result->num_rows > 0) {
    die("This patient cannot be deleted because consultations are linked to this patient.");
}


/* Delete patient */

$stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: index.php");
    exit();

} else {

    die("Failed to delete patient.");

}

?>