 <?php

require_once "../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../index.php");
    exit;
}

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Get Dashboard Counts
|--------------------------------------------------------------------------
*/

$patientResult = $conn->query(
    "SELECT COUNT(*) AS total FROM patients"
);

$patientCount = $patientResult->fetch_assoc()["total"];


$doctorResult = $conn->query(
    "SELECT COUNT(*) AS total FROM doctors"
);

$doctorCount = $doctorResult->fetch_assoc()["total"];


$departmentResult = $conn->query(
    "SELECT COUNT(*) AS total FROM departments"
);

$departmentCount = $departmentResult->fetch_assoc()["total"];


$appointmentResult = $conn->query(
    "SELECT COUNT(*) AS total
     FROM appointments
     WHERE appointment_date = CURDATE()"
);

$todayAppointmentCount =
    $appointmentResult->fetch_assoc()["total"];


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
                        Welcome,
                        <?php
                        echo htmlspecialchars(
                            $_SESSION["user_name"]
                        );
                        ?>
                    </p>

                </div>

                <a
                    href="logout.php"
                    class="btn btn-danger">
                    Logout
                </a>

            </div>


            <!-- Dashboard Cards -->

            <div class="row g-4">


                <!-- Patients -->

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <h6 class="text-muted">
                                Total Patients
                            </h6>

                            <h2 class="fw-bold">
                                <?php echo $patientCount; ?>
                            </h2>

                        </div>

                    </div>

                </div>


                <!-- Doctors -->

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <h6 class="text-muted">
                                Total Doctors
                            </h6>

                            <h2 class="fw-bold">
                                <?php echo $doctorCount; ?>
                            </h2>

                        </div>

                    </div>

                </div>


                <!-- Departments -->

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <h6 class="text-muted">
                                Total Departments
                            </h6>

                            <h2 class="fw-bold">
                                <?php echo $departmentCount; ?>
                            </h2>

                        </div>

                    </div>

                </div>


                <!-- Today's Appointments -->

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <h6 class="text-muted">
                                Today's Appointments
                            </h6>

                            <h2 class="fw-bold">
                                <?php echo $todayAppointmentCount; ?>
                            </h2>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Welcome Section -->

            <div class="card shadow-sm mt-4">

                <div class="card-body">

                    <h4 class="fw-bold">
                        Welcome to the Admin Panel
                    </h4>

                    <p class="text-muted mb-0">
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