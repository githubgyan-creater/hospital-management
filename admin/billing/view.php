<?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

require_once "../../config/database.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}


/* Get bill details */

$stmt = $conn->prepare("
    SELECT
        billing.*,
        patients.patient_code,
        patients.name AS patient_name,
        patients.phone AS patient_phone
    FROM billing
    LEFT JOIN patients
        ON billing.patient_id = patients.id
    WHERE billing.id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

$bill = $result->fetch_assoc();


if (!$bill) {
    die("Bill not found.");
}


require_once "../../includes/header.php";

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->

        <div class="col-md-2 p-0">

            <?php require_once "../../includes/sidebar.php"; ?>

        </div>


        <!-- Main Content -->

        <div class="col-md-10">

            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>

                        <h2>Bill Details</h2>

                        <p class="text-muted">
                            View billing information
                        </p>

                    </div>

                    <a href="index.php" class="btn btn-secondary">
                        Back
                    </a>

                </div>


                <div class="card">

                    <div class="card-header bg-primary text-white">

                        <strong>
                            Billing Information
                        </strong>

                    </div>


                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <strong>Bill ID:</strong>

                                <p>
                                    <?php echo $bill["id"]; ?>
                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <strong>Bill Number:</strong>

                                <p>
                                    <?php echo htmlspecialchars($bill["bill_number"]); ?>
                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <strong>Patient ID:</strong>

                                <p>
                                    <?php echo htmlspecialchars($bill["patient_code"] ?? "-"); ?>
                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <strong>Patient Name:</strong>

                                <p>
                                    <?php echo htmlspecialchars($bill["patient_name"] ?? "-"); ?>
                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <strong>Patient Phone:</strong>

                                <p>
                                    <?php echo htmlspecialchars($bill["patient_phone"] ?? "-"); ?>
                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <strong>Bill Date:</strong>

                                <p>
                                    <?php echo htmlspecialchars($bill["bill_date"]); ?>
                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <strong>Amount:</strong>

                                <p>
                                    ₹<?php echo number_format($bill["amount"], 2); ?>
                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <strong>Payment Status:</strong>

                                <p>

                                    <?php if ($bill["payment_status"] === "Paid"): ?>

                                        <span class="badge bg-success">
                                            Paid
                                        </span>

                                    <?php elseif ($bill["payment_status"] === "Pending"): ?>

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-danger">
                                            Cancelled
                                        </span>

                                    <?php endif; ?>

                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <strong>Payment Method:</strong>

                                <p>
                                    <?php
                                    echo $bill["payment_method"]
                                        ? htmlspecialchars($bill["payment_method"])
                                        : "-";
                                    ?>
                                </p>

                            </div>


                            <div class="col-md-12 mb-3">

                                <strong>Description:</strong>

                                <p>
                                    <?php
                                    echo $bill["description"]
                                        ? nl2br(htmlspecialchars($bill["description"]))
                                        : "-";
                                    ?>
                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <strong>Created At:</strong>

                                <p>
                                    <?php echo htmlspecialchars($bill["created_at"]); ?>
                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <strong>Updated At:</strong>

                                <p>
                                    <?php echo htmlspecialchars($bill["updated_at"]); ?>
                                </p>

                            </div>

                        </div>


                        <hr>


                         <a
    href="edit.php?id=<?php echo $bill["id"]; ?>"
    class="btn btn-primary"
>
    Edit Bill
</a>

<a
    href="delete.php?id=<?php echo $bill["id"]; ?>"
    class="btn btn-danger"
    onclick="return confirm('Are you sure you want to delete this bill?');"
>
    Delete Bill
</a>

<a
    href="index.php"
    class="btn btn-secondary"
>
    Back to Billing
</a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>