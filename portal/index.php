 <?php

session_start();

require_once "../config/database.php";
require_once "../includes/header.php";

?>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-12">

            <div class="container py-5">

                <div class="row justify-content-center">

                    <div class="col-lg-8">

                        <div class="card shadow-sm border-0">

                            <div class="card-body p-4 p-md-5 text-center">

                                <h1 class="fw-bold mb-3">
                                    ABC Super Speciality Hospital
                                </h1>

                                <p class="text-muted mb-4">
                                    Patient Portal
                                </p>

                                <div class="alert alert-info">
                                    Welcome to the hospital patient portal.
                                    Please choose an option below to continue.
                                </div>


                                <!-- Patient Options -->

                                <div class="row g-3 mt-4">

                                    <!-- New Patient -->

                                    <div class="col-md-6">

                                        <div class="card h-100 border d-flex flex-column">

                                            <div class="card-body p-4 d-flex flex-column">

                                                <h4 class="fw-bold">
                                                    New Patient
                                                </h4>

                                                <p class="text-muted">
                                                    Register as a new patient
                                                    and create your patient profile.
                                                </p>

                                                <div class="mt-auto pt-3">

                                                    <a
                                                        href="register.php"
                                                        class="btn btn-primary w-100">
                                                        Register
                                                    </a>

                                                </div>

                                            </div>

                                        </div>

                                    </div>


                                    <!-- Existing Patient -->

                                    <div class="col-md-6">

                                        <div class="card h-100 border d-flex flex-column">

                                            <div class="card-body p-4 d-flex flex-column">

                                                <h4 class="fw-bold">
                                                    Existing Patient
                                                </h4>

                                                <p class="text-muted">
                                                    Login to your portal to view
                                                    appointments, tokens, reports & history.
                                                </p>

                                                <div class="mt-auto pt-3">

                                                    <a
                                                        href="login.php"
                                                        class="btn btn-success w-100">
                                                        Patient Login
                                                    </a>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <hr class="my-5">


                                <!-- Hospital Services -->

                                <div>

                                    <h5 class="fw-bold">
                                        Hospital Services
                                    </h5>

                                    <div class="row g-3 mt-2">

                                        <div class="col-6 col-md-3">

                                            <div class="border rounded p-3">

                                                <strong>
                                                    Appointments
                                                </strong>

                                            </div>

                                        </div>


                                        <div class="col-6 col-md-3">

                                            <div class="border rounded p-3">

                                                <strong>
                                                    Token Queue
                                                </strong>

                                            </div>

                                        </div>


                                        <div class="col-6 col-md-3">

                                            <div class="border rounded p-3">

                                                <strong>
                                                    Reports
                                                </strong>

                                            </div>

                                        </div>


                                        <div class="col-6 col-md-3">

                                            <div class="border rounded p-3">

                                                <strong>
                                                    Patient History
                                                </strong>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>


<?php

require_once "../includes/footer.php";

?>