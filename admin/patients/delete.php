<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$patientId = (int) $_GET["id"];

$sql = "DELETE FROM patients WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $patientId);

if ($stmt->execute()) {

    $stmt->close();

    header("Location: index.php?success=Patient deleted successfully");
    exit;

} else {

    $stmt->close();

    header("Location: index.php?error=Patient could not be deleted");
    exit;
}

?>
