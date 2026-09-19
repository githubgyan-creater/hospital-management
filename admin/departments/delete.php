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

$departmentId = (int) $_GET["id"];

$sql = "DELETE FROM departments WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $departmentId);

if ($stmt->execute()) {

    $stmt->close();

    header("Location: index.php?success=Department deleted successfully");
    exit;

} else {

    $stmt->close();

    header("Location: index.php?error=Department could not be deleted");
    exit;
}

?>