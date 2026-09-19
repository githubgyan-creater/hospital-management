<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

 
//  Check Appointment ID and New Status


if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"]) ||
    !isset($_GET["status"])
) {
    header("Location: index.php");
    exit;
}

$appointmentId = (int) $_GET["id"];
$newStatus = trim($_GET["status"]);

//  Allowed Statuses

$allowedStatuses = [
    "Pending",
    "Confirmed",
    "Completed",
    "Cancelled"
];

if (!in_array($newStatus, $allowedStatuses, true)) {
    header("Location: index.php?error=Invalid appointment status");
    exit;
}

//  Get Current Status


$sql = "SELECT status
        FROM appointments
        WHERE id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointmentId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: index.php?error=Appointment not found");
    exit;
}

$appointment = $result->fetch_assoc();

$stmt->close();

$currentStatus = $appointment["status"];

//  Check Allowed Transition

$allowedTransitions = [
    "Pending" => [
        "Confirmed",
        "Cancelled"
    ],

    "Confirmed" => [
        "Completed",
        "Cancelled"
    ],

    "Completed" => [],

    "Cancelled" => []
];

if (
    !in_array(
        $newStatus,
        $allowedTransitions[$currentStatus],
        true
    )
) {

    header(
        "Location: index.php?error=This status change is not allowed"
    );

    exit;
}

//  Update Status


$sql = "UPDATE appointments
        SET status = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "si",
    $newStatus,
    $appointmentId
);

if ($stmt->execute()) {

    $stmt->close();

    header(
        "Location: index.php?success=Appointment status updated successfully"
    );

    exit;

} else {

    $stmt->close();

    header(
        "Location: index.php?error=Appointment status could not be updated"
    );

    exit;
}

?>