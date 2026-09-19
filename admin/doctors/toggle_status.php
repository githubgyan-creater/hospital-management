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

$doctorId = (int) $_GET["id"];


//  Get Current Status


$sql = "SELECT status
        FROM doctors
        WHERE id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $doctorId);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: index.php");
    exit;
}

$doctor = $result->fetch_assoc();

$stmt->close();

// Change Status

$newStatus = ($doctor["status"] == 1) ? 0 : 1;

$sql = "UPDATE doctors
        SET status = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $newStatus,
    $doctorId
);

if ($stmt->execute()) {

    $stmt->close();

    if ($newStatus == 1) {

        header(
            "Location: index.php?success=Doctor activated successfully"
        );

    } else {

        header(
            "Location: index.php?success=Doctor deactivated successfully"
        );
    }

    exit;

} else {

    $stmt->close();

    header(
        "Location: index.php?error=Doctor status could not be updated"
    );

    exit;
}

?>