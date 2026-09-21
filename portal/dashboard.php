 <?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["portal_patient_id"])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION["portal_patient_id"];

$patient = null;
$appointment_count = 0;
$token_count = 0;
$report_count = 0;
$followup_count = 0;


/* =========================
   GET PATIENT PROFILE
========================= */

$stmt = $conn->prepare(
    "SELECT
        id,
        patient_code,
        name,
        date_of_birth,
        age,
        gender,
        phone,
        email,
        address,
        blood_group,
        emergency_contact,
        medical_history
     FROM patients
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $patient = $result->fetch_assoc();
}

$stmt->close();

if (!$patient) {
    session_destroy();
    header("Location: login.php");
    exit();
}


/* =========================
   APPOINTMENT COUNT
========================= */

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM appointments
     WHERE patient_id = ?"
);

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$appointment_count = (int) $row["total"];

$stmt->close();


/* =========================
   TOKEN COUNT
========================= */

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM tokens
     WHERE patient_id = ?"
);

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$token_count = (int) $row["total"];

$stmt->close();


/* =========================
   REPORT COUNT
========================= */

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM medical_reports
     WHERE patient_id = ?"
);

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$report_count = (int) $row["total"];

$stmt->close();


/* =========================
   FOLLOW-UP COUNT
========================= */

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM followups
     WHERE patient_id = ?
     AND status = 'Pending'"
);

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$followup_count = (int) $row["total"];

$stmt->close();


require_once "../includes/header.php";

?>

<div class="container py-4">


    <!-- =========================
         WELCOME SECTION
    ========================== -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body p-4">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h2 class="fw-bold mb-2">
                        Welcome, <?php echo htmlspecialchars($patient["name"]); ?>
                    </h2>

                    <p class="text-muted mb-1">
                        Patient ID:
                        <strong>
                            <?php echo htmlspecialchars($patient["patient_code"]); ?>
                        </strong>
                    </p>

                    <p class="text-muted mb-0">
                        Welcome to your Tender Palm Super Speciality Hospital Patient Portal.
                    </p>

                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <a
                        href="logout.php"
                        class="btn btn-outline-danger">
                        Logout
                    </a>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================
         QUICK STATISTICS
    ========================== -->

    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Appointments
                    </h6>

                    <h2 class="fw-bold text-primary">
                        <?php echo $appointment_count; ?>
                    </h2>

                </div>

            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Tokens
                    </h6>

                    <h2 class="fw-bold text-success">
                        <?php echo $token_count; ?>
                    </h2>

                </div>

            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Reports
                    </h6>

                    <h2 class="fw-bold text-warning">
                        <?php echo $report_count; ?>
                    </h2>

                </div>

            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Follow-ups
                    </h6>

                    <h2 class="fw-bold text-danger">
                        <?php echo $followup_count; ?>
                    </h2>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================
         PATIENT SERVICES
    ========================== -->

    <h4 class="fw-bold mb-3">
        Patient Services
    </h4>


    <div class="row g-4">


        <!-- MY PROFILE -->

        <div class="col-md-6 col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body p-4 d-flex flex-column">

                    <h5 class="fw-bold">
                        My Profile
                    </h5>

                    <p class="text-muted">
                        View your registered patient information and personal details.
                    </p>

                    <div class="mt-auto pt-3">

                        <a
                            href="profile.php"
                            class="btn btn-primary">
                            View Profile
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- BOOK APPOINTMENT -->

        <div class="col-md-6 col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body p-4 d-flex flex-column">

                    <h5 class="fw-bold">
                        Book Appointment
                    </h5>

                    <p class="text-muted">
                        Select a department and doctor to request an appointment.
                    </p>

                    <div class="mt-auto pt-3">

                        <a
                            href="book_appointment.php"
                            class="btn btn-success">
                            Book Appointment
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- MY TOKEN -->

        <div class="col-md-6 col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body p-4 d-flex flex-column">

                    <h5 class="fw-bold">
                        My Token
                    </h5>

                    <p class="text-muted">
                        View your current OPD token and queue status.
                    </p>

                    <div class="mt-auto pt-3">

                        <a
                            href="my_token.php"
                            class="btn btn-warning">
                            View Token
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- APPOINTMENTS -->

        <div class="col-md-6 col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body p-4 d-flex flex-column">

                    <h5 class="fw-bold">
                        My Appointments
                    </h5>

                    <p class="text-muted">
                        View your upcoming and previous appointments.
                    </p>

                    <div class="mt-auto pt-3">

                        <a
                            href="appointments.php"
                            class="btn btn-info text-white">
                            View Appointments
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- MEDICAL REPORTS -->

        <div class="col-md-6 col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body p-4 d-flex flex-column">

                    <h5 class="fw-bold">
                        Medical Reports
                    </h5>

                    <p class="text-muted">
                        View reports uploaded by the hospital or laboratory.
                    </p>

                    <div class="mt-auto pt-3">

                        <a
                            href="reports.php"
                            class="btn btn-secondary">
                            View Reports
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- PATIENT HISTORY -->

        <div class="col-md-6 col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body p-4 d-flex flex-column">

                    <h5 class="fw-bold">
                        Patient History
                    </h5>

                    <p class="text-muted">
                        View your consultation and medical history.
                    </p>

                    <div class="mt-auto pt-3">

                        <a
                            href="history.php"
                            class="btn btn-dark">
                            View History
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- FOLLOW UPS -->

        <div class="col-md-6 col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body p-4 d-flex flex-column">

                    <h5 class="fw-bold">
                        Follow-ups
                    </h5>

                    <p class="text-muted">
                        View your upcoming follow-up appointments and instructions.
                    </p>

                    <div class="mt-auto pt-3">

                        <a
                            href="followups.php"
                            class="btn btn-outline-primary">
                            View Follow-ups
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- LOGOUT -->

        <div class="col-md-6 col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body p-4 d-flex flex-column">

                    <h5 class="fw-bold">
                        Logout
                    </h5>

                    <p class="text-muted">
                        Securely logout from your patient portal.
                    </p>

                    <div class="mt-auto pt-3">

                        <a
                            href="logout.php"
                            class="btn btn-outline-danger">
                            Logout
                        </a>

                    </div>

                </div>

            </div>

        </div>


    </div>


    <!-- =========================
         BASIC PATIENT INFORMATION
    ========================== -->

    <div class="card shadow-sm border-0 mt-4">

        <div class="card-body p-4">

            <h4 class="fw-bold mb-4">
                Patient Information
            </h4>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <strong>Patient ID</strong>

                    <div class="text-muted">
                        <?php echo htmlspecialchars($patient["patient_code"]); ?>
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Full Name</strong>

                    <div class="text-muted">
                        <?php echo htmlspecialchars($patient["name"]); ?>
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Gender</strong>

                    <div class="text-muted">
                        <?php echo htmlspecialchars($patient["gender"]); ?>
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Phone</strong>

                    <div class="text-muted">
                        <?php echo htmlspecialchars($patient["phone"]); ?>
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Email</strong>

                    <div class="text-muted">

                        <?php

                        echo !empty($patient["email"])
                            ? htmlspecialchars($patient["email"])
                            : "Not provided";

                        ?>

                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Blood Group</strong>

                    <div class="text-muted">

                        <?php

                        echo !empty($patient["blood_group"])
                            ? htmlspecialchars($patient["blood_group"])
                            : "Not provided";

                        ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<?php

require_once "../includes/footer.php";

?>