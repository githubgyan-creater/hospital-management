 <?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "doctor") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

require_once "../../includes/header.php";


//   Get Logged-in Doctor


$userEmail = $_SESSION["user_email"];

$doctorSql = "SELECT id, name, email
              FROM doctors
              WHERE email = ?
              LIMIT 1";

$doctorStmt = $conn->prepare($doctorSql);

$doctorStmt->bind_param("s", $userEmail);

$doctorStmt->execute();

$doctorResult = $doctorStmt->get_result();

if ($doctorResult->num_rows !== 1) {

    die("Doctor profile not found.");
}

$doctor = $doctorResult->fetch_assoc();

$doctorId = (int) $doctor["id"];

$doctorStmt->close();


//  Check Appointment ID


if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
) {

    header("Location: index.php");
    exit;
}

$appointmentId = (int) $_GET["id"];


//  Get Appointment


$sql = "SELECT
            appointments.id,
            appointments.appointment_date,
            appointments.appointment_time,
            appointments.reason,
            appointments.status,

            patients.patient_code,
            patients.name AS patient_name,
            patients.phone AS patient_phone,
            patients.gender,
            patients.date_of_birth,

            departments.name AS department_name,

            doctors.name AS doctor_name

        FROM appointments

        INNER JOIN patients
            ON appointments.patient_id = patients.id

        INNER JOIN doctors
            ON appointments.doctor_id = doctors.id

        LEFT JOIN departments
            ON doctors.department_id = departments.id

        WHERE appointments.id = ?
        AND appointments.doctor_id = ?

        LIMIT 1";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $appointmentId,
    $doctorId
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    $stmt->close();

    die("Appointment not found or you are not authorized to view it.");
}


$appointment = $result->fetch_assoc();

$stmt->close();

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->

        <div class="col-md-3 col-lg-2 p-0">

            <?php require_once "../../includes/sidebar.php"; ?>

        </div>


        <!-- Main Content -->

        <div class="col-md-9 col-lg-10 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Appointment Details
                    </h2>

                    <p class="text-muted mb-0">
                        View patient appointment information
                    </p>

                </div>

                <a
                    href="index.php"
                    class="btn btn-secondary">
                    Back to Appointments
                </a>

            </div>


            <!-- Appointment Information -->

            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="fw-bold mb-0">
                        Appointment Information
                    </h5>

                </div>


                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <strong>Appointment ID</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $appointment["id"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Date</strong>

                            <p class="mb-0">
                                <?php
                                echo date(
                                    "d-m-Y",
                                    strtotime(
                                        $appointment["appointment_date"]
                                    )
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Time</strong>

                            <p class="mb-0">
                                <?php
                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $appointment["appointment_time"]
                                    )
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Doctor</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $appointment["doctor_name"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Department</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $appointment["department_name"]
                                    ?? "Not assigned"
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Status</strong>

                            <p class="mb-0">

                                <?php

                                $status = $appointment["status"];

                                if ($status === "Pending") {

                                    echo '<span class="badge bg-warning text-dark">
                                            Pending
                                          </span>';

                                } elseif ($status === "Confirmed") {

                                    echo '<span class="badge bg-primary">
                                            Confirmed
                                          </span>';

                                } elseif ($status === "Completed") {

                                    echo '<span class="badge bg-success">
                                            Completed
                                          </span>';

                                } elseif ($status === "Cancelled") {

                                    echo '<span class="badge bg-danger">
                                            Cancelled
                                          </span>';

                                } else {

                                    echo '<span class="badge bg-secondary">'
                                        . htmlspecialchars($status)
                                        . '</span>';
                                }

                                ?>

                            </p>

                        </div>


                        <div class="col-md-12 mb-3">

                            <strong>Reason</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $appointment["reason"]
                                    ?: "Not provided"
                                );
                                ?>
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Patient Information -->

            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="fw-bold mb-0">
                        Patient Information
                    </h5>

                </div>


                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <strong>Patient ID</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $appointment["patient_code"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Patient Name</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $appointment["patient_name"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Phone</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $appointment["patient_phone"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Gender</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $appointment["gender"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Date of Birth</strong>

                            <p class="mb-0">
                                <?php
                                echo !empty(
                                    $appointment["date_of_birth"]
                                )
                                    ? date(
                                        "d-m-Y",
                                        strtotime(
                                            $appointment["date_of_birth"]
                                        )
                                    )
                                    : "Not provided";
                                ?>
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Actions -->

            <div class="card shadow-sm">

                <div class="card-body">

                    <a
                        href="index.php"
                        class="btn btn-secondary">
                        Back to Appointments
                    </a>

                    <a
                        href="../consultations/add.php?appointment_id=<?php echo $appointmentId; ?>"
                        class="btn btn-primary">
                        Add Consultation
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>


<?php

require_once "../../includes/footer.php";

?>