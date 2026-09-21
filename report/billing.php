<?php

session_start();

if (
    !isset($_SESSION["user_role"]) ||
    !in_array($_SESSION["user_role"], ["admin", "receptionist"])
) {
    header("Location: ../login.php");
    exit();
}

require_once "../config/database.php";


/* Get billing records */

$query = "
    SELECT
        billing.*,

        patients.patient_code,
        patients.name AS patient_name

    FROM billing

    LEFT JOIN patients
        ON billing.patient_id = patients.id

    ORDER BY billing.bill_date DESC, billing.id DESC
";

$result = $conn->query($query);


require_once "../includes/header.php";

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->

        <div class="col-md-2 p-0">

            <?php require_once "../includes/sidebar.php"; ?>

        </div>


        <!-- Main Content -->

        <div class="col-md-10">

            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>

                        <h2>Billing Reports</h2>

                        <p class="text-muted">
                            View billing and payment information
                        </p>

                    </div>

                    <a
                        href="index.php"
                        class="btn btn-secondary"
                    >
                        Back to Reports
                    </a>

                </div>


                <div class="card">

                    <div class="card-header bg-primary text-white">

                        <strong>
                            Billing Report
                        </strong>

                    </div>


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

                                        <th>Description</th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php if ($result && $result->num_rows > 0): ?>

                                        <?php while ($bill = $result->fetch_assoc()): ?>

                                            <tr>

                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $bill["id"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $bill["bill_number"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>

                                                    <?php

                                                    echo htmlspecialchars(
                                                        ($bill["patient_code"] ?? "-")
                                                        . " - "
                                                        . ($bill["patient_name"] ?? "-")
                                                    );

                                                    ?>

                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $bill["bill_date"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>

                                                    ₹<?php

                                                    echo number_format(
                                                        (float)($bill["amount"] ?? 0),
                                                        2
                                                    );

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php
                                                    $status =
                                                        $bill["payment_status"] ?? "-";
                                                    ?>


                                                    <?php if ($status === "Paid"): ?>

                                                        <span class="badge bg-success">
                                                            Paid
                                                        </span>


                                                    <?php elseif ($status === "Pending"): ?>

                                                        <span class="badge bg-warning text-dark">
                                                            Pending
                                                        </span>


                                                    <?php elseif ($status === "Cancelled"): ?>

                                                        <span class="badge bg-danger">
                                                            Cancelled
                                                        </span>


                                                    <?php else: ?>

                                                        <span class="badge bg-secondary">
                                                            <?php
                                                            echo htmlspecialchars($status);
                                                            ?>
                                                        </span>

                                                    <?php endif; ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo !empty($bill["payment_method"])
                                                        ? htmlspecialchars($bill["payment_method"])
                                                        : "-";

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo !empty($bill["description"])
                                                        ? nl2br(
                                                            htmlspecialchars(
                                                                $bill["description"]
                                                            )
                                                        )
                                                        : "-";

                                                    ?>

                                                </td>

                                            </tr>

                                        <?php endwhile; ?>


                                    <?php else: ?>

                                        <tr>

                                            <td
                                                colspan="8"
                                                class="text-center"
                                            >
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