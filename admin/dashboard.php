<?php

session_start();

// Check Login 


if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

//  Check Admin Role

if ($_SESSION["user_role"] !== "admin") {
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

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h2 class="fw-bold mb-1">
                        Admin Dashboard
                    </h2>

                    <p class="text-muted mb-0">
                        Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
                    </p>
                </div>

                <a href="logout.php" class="btn btn-danger">
                    Logout
                </a>

            </div>

            <!-- Dashboard Cards -->

            <div class="row g-4">

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Total Patients
                            </h6>

                            <h2 class="fw-bold">
                                0
                            </h2>

                        </div>
                    </div>

                </div>

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Total Doctors
                            </h6>

                            <h2 class="fw-bold">
                                0
                            </h2>

                        </div>
                    </div>

                </div>

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Departments
                            </h6>

                            <h2 class="fw-bold">
                                0
                            </h2>

                        </div>
                    </div>

                </div>

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Today's Appointments
                            </h6>

                            <h2 class="fw-bold">
                                0
                            </h2>

                        </div>
                    </div>

                </div>

            </div>

            <!-- Welcome Section -->

            <div class="card shadow-sm border-0 mt-4">

                <div class="card-body">

                    <h4 class="fw-bold">
                        Welcome to the Admin Panel
                    </h4>

                    <p class="text-muted">
                        From this dashboard you will be able to manage
                        patients, doctors, departments, appointments,
                        billing, and reports.
                    </p>

                </div>

            </div>

        </div>

    </div>
</div>

<?php

require_once "../includes/footer.php";

?>