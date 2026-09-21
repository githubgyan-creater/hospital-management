 <?php

require_once "../includes/auth.php";

if ($_SESSION["user_role"] !== "doctor") {

    header("Location: ../index.php");
    exit;

}

require_once "../config/database.php";
require_once "../includes/header.php";

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->

        <div class="col-md-3 col-lg-2 p-0">

            <?php require_once "../includes/sidebar.php"; ?>

        </div>


        <!-- Main Content -->

        <div class="col-md-9 col-lg-10 p-4">

            <!-- Page Header -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Doctor Dashboard
                    </h2>

                    <p class="text-muted mb-0">

                        Welcome,
                        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>

                    </p>

                </div>


                <a
                    href="../logout.php"
                    class="btn btn-danger">

                    Logout

                </a>

            </div>


            <!-- Welcome Card -->

            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <h4 class="fw-bold">
                        Welcome to Doctor Panel
                    </h4>

                    <p class="text-muted mb-0">

                        Manage your appointments and patient
                        consultations from this panel.

                    </p>

                </div>

            </div>


            <!-- Dashboard Cards -->

            <div class="row">

            <!-- My Profile Card -->

<div class="col-md-6 mb-4">

    <div class="card shadow-sm h-100">

        <div class="card-body">

            <h4 class="fw-bold">
                My Profile
            </h4>

            <p class="text-muted">

                View and update your doctor profile details.

            </p>

            <a
                href="profile/index.php"
                class="btn btn-info">

                View My Profile

            </a>

        </div>

    </div>

</div> 

                <!-- Appointments Card -->

                <div class="col-md-6 mb-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <h4 class="fw-bold">
                                My Appointments
                            </h4>

                            <p class="text-muted">

                                View your assigned patient
                                appointments and appointment details.

                            </p>

                            <a
                                href="appointments/index.php"
                                class="btn btn-primary">

                                View Appointments

                            </a>

                        </div>

                    </div>

                </div>


                <!-- Consultations Card -->

                <div class="col-md-6 mb-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <h4 class="fw-bold">
                                My Consultations
                            </h4>

                            <p class="text-muted">

                                View and manage consultation
                                records for your patients.

                            </p>

                            <a
                                href="consultations/index.php"
                                class="btn btn-success">

                                View Consultations

                            </a>

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