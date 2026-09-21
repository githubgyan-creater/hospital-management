<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "doctor") {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/database.php";
require_once "../../includes/header.php";


/*
|--------------------------------------------------------------------------
| Get Appointment ID
|--------------------------------------------------------------------------
*/

$appointment_id = (int)($_GET["appointment_id"] ?? 0);

if ($appointment_id <= 0) {
    die("Invalid appointment.");
}


/*
|--------------------------------------------------------------------------
| Get Logged-in Doctor
|--------------------------------------------------------------------------
*/

$user_id = (int)$_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT email, name
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$stmt->close();


if (!$user) {
    die("User account not found.");
}


$doctor_email = $user["email"];


/*
|--------------------------------------------------------------------------
| Get Doctor ID
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id, name
    FROM doctors
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $doctor_email);
$stmt->execute();

$doctor = $stmt->get_result()->fetch_assoc();

$stmt->close();


if (!$doctor) {
    die("Doctor profile not found.");
}


$doctor_id = (int)$doctor["id"];


/*
|--------------------------------------------------------------------------
| Get Appointment + Patient + Token
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        a.id AS appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.status AS appointment_status,

        p.id AS patient_id,
        p.patient_code,
        p.name AS patient_name,

        t.id AS token_id,
        t.token_number,
        t.status AS token_status

    FROM appointments a

    INNER JOIN patients p
        ON a.patient_id = p.id

    LEFT JOIN tokens t
        ON a.id = t.appointment_id

    WHERE a.id = ?
    AND a.doctor_id = ?

    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $appointment_id,
    $doctor_id
);

$stmt->execute();

$appointment = $stmt->get_result()->fetch_assoc();

$stmt->close();


if (!$appointment) {
    die("Appointment not found or not assigned to this doctor.");
}


$message = "";
$message_type = "";


/*
|--------------------------------------------------------------------------
| Create Report Request
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $report_type = trim($_POST["report_type"] ?? "");
    $report_title = trim($_POST["report_title"] ?? "");
    $report_description = trim($_POST["report_description"] ?? "");


    if ($report_type === "" || $report_title === "") {

        $message = "Report type and report title are required.";
        $message_type = "danger";

    } else {

        /*
         * Check whether a pending report with the
         * same title already exists for this appointment.
         */

        $check = $conn->prepare("
            SELECT id
            FROM medical_reports
            WHERE appointment_id = ?
            AND report_title = ?
            AND status = 'PENDING'
            LIMIT 1
        ");

        $check->bind_param(
            "is",
            $appointment_id,
            $report_title
        );

        $check->execute();

        $existing = $check->get_result()->fetch_assoc();

        $check->close();


        if ($existing) {

            $message = "This report request already exists.";
            $message_type = "warning";

        } else {

            $token_id = !empty($appointment["token_id"])
                ? (int)$appointment["token_id"]
                : null;


            $stmt = $conn->prepare("
                INSERT INTO medical_reports
                (
                    patient_id,
                    doctor_id,
                    appointment_id,
                    token_id,
                    report_type,
                    report_title,
                    report_description,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDING')
            ");

            $stmt->bind_param(
                "iiiisss",
                $appointment["patient_id"],
                $doctor_id,
                $appointment_id,
                $token_id,
                $report_type,
                $report_title,
                $report_description
            );


            if ($stmt->execute()) {

                /*
                 * If token exists, change token status
                 * to REPORT PENDING.
                 */

                if ($token_id) {

                    $update = $conn->prepare("
                        UPDATE tokens
                        SET status = 'REPORT PENDING'
                        WHERE id = ?
                        AND doctor_id = ?
                    ");

                    $update->bind_param(
                        "ii",
                        $token_id,
                        $doctor_id
                    );

                    $update->execute();

                    $update->close();
                }


                $message = "Report request created successfully.";
                $message_type = "success";

            } else {

                $message = "Unable to create report request.";
                $message_type = "danger";
            }

            $stmt->close();
        }
    }
}

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->

        <div class="col-md-3 col-lg-2 p-0">

            <?php require_once "../../includes/sidebar.php"; ?>

        </div>


        <!-- Main Content -->

        <div class="col-md-9 col-lg-10 p-4">


            <!-- Page Header -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Request Medical Report
                    </h2>

                    <p class="text-muted mb-0">
                        Create a test or report request for the patient.
                    </p>

                </div>

                <a
                    href="../tokens/index.php"
                    class="btn btn-secondary"
                >
                    Back to Token Queue
                </a>

            </div>


            <!-- Message -->

            <?php if ($message !== ""): ?>

                <div class="alert alert-<?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


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

                            <strong>
                                Patient Name
                            </strong>

                            <div>
                                <?php echo htmlspecialchars(
                                    $appointment["patient_name"]
                                ); ?>
                            </div>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>
                                Patient Code
                            </strong>

                            <div>
                                <?php echo htmlspecialchars(
                                    $appointment["patient_code"]
                                ); ?>
                            </div>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>
                                Token
                            </strong>

                            <div>

                                <?php if ($appointment["token_number"]): ?>

                                    <?php echo htmlspecialchars(
                                        $appointment["token_number"]
                                    ); ?>

                                <?php else: ?>

                                    No Token

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Report Request Form -->

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="fw-bold mb-0">
                        Report Details
                    </h5>

                </div>


                <div class="card-body">

                    <form method="POST">


                        <!-- Report Type -->

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Report Type

                            </label>

                            <select
                                name="report_type"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Select Report Type
                                </option>

                                <option value="Blood Test">
                                    Blood Test
                                </option>

                                <option value="Urine Test">
                                    Urine Test
                                </option>

                                <option value="X-Ray">
                                    X-Ray
                                </option>

                                <option value="Ultrasound">
                                    Ultrasound
                                </option>

                                <option value="CT Scan">
                                    CT Scan
                                </option>

                                <option value="MRI">
                                    MRI
                                </option>

                                <option value="ECG">
                                    ECG
                                </option>

                                <option value="Biopsy">
                                    Biopsy
                                </option>

                                <option value="Other">
                                    Other
                                </option>

                            </select>

                        </div>


                        <!-- Report Title -->

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Report / Test Title

                            </label>

                            <input
                                type="text"
                                name="report_title"
                                class="form-control"
                                placeholder="Example: Complete Blood Count"
                                required
                            >

                        </div>


                        <!-- Description -->

                        <div class="mb-4">

                            <label class="form-label fw-bold">

                                Description / Instructions

                            </label>

                            <textarea
                                name="report_description"
                                class="form-control"
                                rows="5"
                                placeholder="Enter test instructions or additional information..."
                            ></textarea>

                        </div>


                        <!-- Buttons -->

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Create Report Request
                            </button>


                            <a
                                href="../tokens/index.php"
                                class="btn btn-secondary"
                            >
                                Cancel
                            </a>

                        </div>


                    </form>

                </div>

            </div>


        </div>

    </div>

</div>


<?php

require_once "../../includes/footer.php";

?>