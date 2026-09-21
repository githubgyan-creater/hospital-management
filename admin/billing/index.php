<?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

require_once "../../config/database.php";

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

                        <h2>Billing</h2>

                        <p class="text-muted">
                            Manage hospital bills
                        </p>

                    </div>

                </div>


                <?php

                $query = "
                    SELECT
                        billing.id,
                        billing.bill_number,
                        billing.bill_date,
                        billing.amount,
                        billing.payment_status,
                        billing.payment_method,
                        patients.name AS patient_name
                    FROM billing
                    LEFT JOIN patients
                        ON billing.patient_id = patients.id
                    ORDER BY billing.id DESC
                ";

                $result = $conn->query($query);

                ?>


                <div class="card">

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover">

                                <thead class="table-primary">

                                    <tr>

                                        <th>Bill ID</th>

                                        <th>Bill Number</th>

                                        <th>Patient</th>

                                        <th>Bill Date</th>

                                        <th>Amount</th>

                                        <th>Payment Status</th>

                                        <th>Payment Method</th>

                                        <th>Action</th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php if ($result && $result->num_rows > 0): ?>

                                        <?php while ($bill = $result->fetch_assoc()): ?>

                                            <tr>

                                                <td>
                                                    <?php echo $bill["id"]; ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($bill["bill_number"]); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($bill["patient_name"]); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($bill["bill_date"]); ?>
                                                </td>

                                                <td>
                                                    ₹<?php echo number_format($bill["amount"], 2); ?>
                                                </td>

                                                <td>

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

                                                </td>

                                                <td>
                                                    <?php
                                                    echo $bill["payment_method"]
                                                        ? htmlspecialchars($bill["payment_method"])
                                                        : "-";
                                                    ?>
                                                </td>

                                                <td>

                                                     <a
    href="view.php?id=<?php echo $bill["id"]; ?>"
    class="btn btn-info btn-sm"
>
    View
</a>

                                                </td>

                                            </tr>

                                        <?php endwhile; ?>

                                    <?php else: ?>

                                        <tr>

                                            <td colspan="8" class="text-center">

                                                No billing records found.

                                            </td>

                                        </tr>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>