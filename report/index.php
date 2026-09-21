<?php

session_start();

/* Allow Admin and Receptionist to access reports */

if (
    !isset($_SESSION["user_role"]) ||
    !in_array($_SESSION["user_role"], ["admin", "receptionist"])
) {
    header("Location: ../login.php");
    exit();
}

require_once "../config/database.php";

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

                <h2>Reports</h2>

                <p class="text-muted">
                    View and manage hospital reports
                </p>


                <div class="row mt-4">


                    <!-- Patient Reports -->

                    <div class="col-md-4 mb-4">

                        <div class="card h-100">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Patient Reports
                                </h5>

                                <p class="card-text">
                                    View patient-related information
                                    and records.
                                </p>

                                <a
                                    href="patients.php"
                                    class="btn btn-primary"
                                >
                                    Patient Reports
                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- Appointment Reports -->

                    <div class="col-md-4 mb-4">

                        <div class="card h-100">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Appointment Reports
                                </h5>

                                <p class="card-text">
                                    View appointment records,
                                    doctors and departments.
                                </p>

                                <a
                                    href="appointments.php"
                                    class="btn btn-primary"
                                >
                                    Appointment Reports
                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- Consultation Reports -->

                    <div class="col-md-4 mb-4">

                        <div class="card h-100">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Consultation Reports
                                </h5>

                                <p class="card-text">
                                    View consultation notes,
                                    diagnosis and advice.
                                </p>

                                <a
                                    href="consultations.php"
                                    class="btn btn-primary"
                                >
                                    Consultation Reports
                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- Billing Reports -->

                    <div class="col-md-4 mb-4">

                        <div class="card h-100">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Billing Reports
                                </h5>

                                <p class="card-text">
                                    View billing, payment status
                                    and payment methods.
                                </p>

                                <a
                                    href="billing.php"
                                    class="btn btn-primary"
                                >
                                    Billing Reports
                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- Invoice Reports -->

                    <div class="col-md-4 mb-4">

                        <div class="card h-100">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Invoice Reports
                                </h5>

                                <p class="card-text">
                                    View invoice and payment
                                    information.
                                </p>

                                <a
                                    href="invoices.php"
                                    class="btn btn-primary"
                                >
                                    Invoice Reports
                                </a>

                            </div>

                        </div>

                    </div>


                </div>

            </div>

        </div>

    </div>

</div>