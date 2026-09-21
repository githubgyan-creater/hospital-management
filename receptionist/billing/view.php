<?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "receptionist") {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/database.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

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

                    <h2>Bill Details</h2>

                    <a href="index.php" class="btn btn-secondary">
                        Back to Billing
                    </a>

                </div>

                <div class="card">

                    <div class="card-header bg-dark text-white">
                        <strong>Billing Information</strong>
                    </div>

                    <div class="card-body">

                        <table class="table table-bordered table-striped">

                            <tr>
                                <th width="30%">Bill ID</th>
                                <td>
                                    <?php echo htmlspecialchars($bill["id"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Bill Number</th>
                                <td>
                                    <?php echo htmlspecialchars($bill["bill_number"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Patient ID</th>
                                <td>
                                    <?php echo htmlspecialchars($bill["patient_code"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Patient Name</th>
                                <td>
                                    <?php echo htmlspecialchars($bill["patient_name"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Patient Phone</th>
                                <td>
                                    <?php echo htmlspecialchars($bill["patient_phone"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Bill Date</th>
                                <td>
                                    <?php echo htmlspecialchars($bill["bill_date"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Amount</th>
                                <td>
                                    ₹<?php echo number_format($bill["amount"], 2); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Payment Status</th>
                                <td>
                                    <?php echo htmlspecialchars($bill["payment_status"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Payment Method</th>
                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $bill["payment_method"] ?? "-"
                                    );
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Description</th>
                                <td>
                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $bill["description"] ?? "-"
                                        )
                                    );
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Created At</th>
                                <td>
                                    <?php
                                    echo date(
                                        "d-m-Y",
                                        strtotime($bill["created_at"])
                                    );
                                    ?>
                                </td>
                            </tr>

                        </table>

                        <div class="mt-3">
                            <a href="edit.php?id=<?php echo $bill["id"]; ?>"
   class="btn btn-primary">
    Edit Bill
</a>
<a href="delete.php?id=<?php echo $bill["id"]; ?>"
   class="btn btn-danger"
   onclick="return confirm('Are you sure you want to delete this bill?');">
    Delete Bill
</a>

                            <a href="index.php"
                               class="btn btn-secondary">
                                Back
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>