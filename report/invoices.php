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


/* Get invoice records */

$query = "
    SELECT
        billing.*,

        patients.patient_code,
        patients.name AS patient_name,
        patients.phone AS patient_phone,
        patients.email AS patient_email

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

                        <h2>Invoice Reports</h2>

                        <p class="text-muted">
                            View invoice and payment information
                        </p>

                    </div>

                    <a
                        href="index.php"
                        class="btn btn-secondary"
                    >
                        Back to Reports
                    </a>

                </div>


                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($invoice = $result->fetch_assoc()): ?>

                        <div class="card mb-4">

                            <div class="card-header bg-primary text-white">

                                <div class="d-flex justify-content-between">

                                    <strong>
                                        HOSPITAL INVOICE
                                    </strong>

                                    <span>
                                        <?php
                                        echo htmlspecialchars(
                                            $invoice["bill_number"]
                                        );
                                        ?>
                                    </span>

                                </div>

                            </div>


                            <div class="card-body">

                                <div class="row mb-4">


                                    <!-- Patient Information -->

                                    <div class="col-md-6">

                                        <h5>
                                            Patient Information
                                        </h5>

                                        <hr>

                                        <p>
                                            <strong>Patient Code:</strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $invoice["patient_code"] ?? "-"
                                            );
                                            ?>
                                        </p>


                                        <p>
                                            <strong>Name:</strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $invoice["patient_name"] ?? "-"
                                            );
                                            ?>
                                        </p>


                                        <p>
                                            <strong>Phone:</strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $invoice["patient_phone"] ?? "-"
                                            );
                                            ?>
                                        </p>


                                        <p>
                                            <strong>Email:</strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $invoice["patient_email"] ?? "-"
                                            );
                                            ?>
                                        </p>

                                    </div>


                                    <!-- Invoice Information -->

                                    <div class="col-md-6">

                                        <h5>
                                            Invoice Information
                                        </h5>

                                        <hr>

                                        <p>
                                            <strong>Invoice Number:</strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $invoice["bill_number"]
                                            );
                                            ?>
                                        </p>


                                        <p>
                                            <strong>Invoice Date:</strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $invoice["bill_date"]
                                            );
                                            ?>
                                        </p>


                                        <p>
                                            <strong>Bill ID:</strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $invoice["id"]
                                            );
                                            ?>
                                        </p>

                                    </div>

                                </div>


                                <!-- Billing Details -->

                                <h5>
                                    Billing Details
                                </h5>

                                <div class="table-responsive">

                                    <table class="table table-bordered">

                                        <thead class="table-light">

                                            <tr>

                                                <th>Description</th>

                                                <th class="text-end">
                                                    Amount
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody>

                                            <tr>

                                                <td>

                                                    <?php

                                                    echo !empty(
                                                        $invoice["description"]
                                                    )
                                                        ? nl2br(
                                                            htmlspecialchars(
                                                                $invoice["description"]
                                                            )
                                                        )
                                                        : "Hospital Services";

                                                    ?>

                                                </td>


                                                <td class="text-end">

                                                    ₹<?php

                                                    echo number_format(
                                                        (float)$invoice["amount"],
                                                        2
                                                    );

                                                    ?>

                                                </td>

                                            </tr>


                                            <tr>

                                                <th class="text-end">
                                                    Total Amount
                                                </th>

                                                <th class="text-end">

                                                    ₹<?php

                                                    echo number_format(
                                                        (float)$invoice["amount"],
                                                        2
                                                    );

                                                    ?>

                                                </th>

                                            </tr>

                                        </tbody>

                                    </table>

                                </div>


                                <!-- Payment Information -->

                                <div class="row mt-3">

                                    <div class="col-md-6">

                                        <strong>
                                            Payment Method:
                                        </strong>

                                        <?php

                                        echo !empty(
                                            $invoice["payment_method"]
                                        )
                                            ? htmlspecialchars(
                                                $invoice["payment_method"]
                                            )
                                            : "-";

                                        ?>

                                    </div>


                                    <div class="col-md-6">

                                        <strong>
                                            Payment Status:
                                        </strong>


                                        <?php

                                        $status =
                                            $invoice["payment_status"] ?? "-";

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

                                    </div>

                                </div>


                            </div>

                        </div>

                    <?php endwhile; ?>


                <?php else: ?>

                    <div class="alert alert-info">

                        No invoice records found.

                    </div>

                <?php endif; ?>


            </div>

        </div>

    </div>

</div>