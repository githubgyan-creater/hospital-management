<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "doctor") {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/database.php";


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

$user_email = $_SESSION["user_email"] ?? "";

$stmt = $conn->prepare("
    SELECT id, name
    FROM doctors
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $user_email);
$stmt->execute();

$doctor = $stmt->get_result()->fetch_assoc();

$stmt->close();


if (!$doctor) {
    die("Doctor profile not found.");
}

$doctor_id = (int)$doctor["id"];


/*
|--------------------------------------------------------------------------
| Get Appointment + Patient
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        a.id AS appointment_id,
        a.patient_id,

        p.patient_code,
        p.name AS patient_name,

        d.name AS doctor_name

    FROM appointments a

    INNER JOIN patients p
        ON a.patient_id = p.id

    INNER JOIN doctors d
        ON a.doctor_id = d.id

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
    die("Appointment not found.");
}


$message = "";
$message_type = "";


/*
|--------------------------------------------------------------------------
| Create Follow-up
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $follow_up_date = trim($_POST["follow_up_date"] ?? "");
    $reason = trim($_POST["reason"] ?? "");
    $notes = trim($_POST["notes"] ?? "");


    if ($follow_up_date === "") {

        $message = "Please select a follow-up date.";
        $message_type = "danger";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Insert Follow-up
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            INSERT INTO followups
            (
                patient_id,
                doctor_id,
                appointment_id,
                follow_up_date,
                reason,
                status,
                notes
            )
            VALUES (?, ?, ?, ?, ?, 'Pending', ?)
        ");

        $stmt->bind_param(
            "iiisss",
            $appointment["patient_id"],
            $doctor_id,
            $appointment_id,
            $follow_up_date,
            $reason,
            $notes
        );


        if ($stmt->execute()) {

            $followup_id = $stmt->insert_id;

            $stmt->close();


            /*
            |--------------------------------------------------------------------------
            | Update Token Status
            |--------------------------------------------------------------------------
            */

            $token_stmt = $conn->prepare("
                UPDATE tokens
                SET status = 'FOLLOW UP'
                WHERE appointment_id = ?
                AND doctor_id = ?
                AND token_date = CURDATE()
            ");

            $token_stmt->bind_param(
                "ii",
                $appointment_id,
                $doctor_id
            );

            $token_stmt->execute();

            $token_stmt->close();


            /*
            |--------------------------------------------------------------------------
            | Add Patient History
            |--------------------------------------------------------------------------
            */

            $history_title = "Follow-up Scheduled";

            $history_description =
                "Follow-up appointment scheduled for " .
                $follow_up_date .
                ". " .
                $reason;


            $history_stmt = $conn->prepare("
                INSERT INTO patient_history
                (
                    patient_id,
                    doctor_id,
                    appointment_id,
                    history_type,
                    title,
                    description,
                    history_date
                )
                VALUES (?, ?, ?, 'Follow Up', ?, ?, CURDATE())
            ");

            $history_stmt->bind_param(
                "iiiss",
                $appointment["patient_id"],
                $doctor_id,
                $appointment_id,
                $history_title,
                $history_description
            );

            $history_stmt->execute();

            $history_stmt->close();


            $message =
                "Follow-up scheduled successfully.";

            $message_type = "success";

        } else {

            $message =
                "Unable to create follow-up.";

            $message_type = "danger";

            $stmt->close();
        }
    }
}


require_once "../../includes/header.php";

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->

        <div class="col-md-3 col-lg-2 p-0">

            <?php require_once "../../includes/sidebar.php"; ?>

        </div>


        <!-- Main Content -->

        <div class="col-md-9 col-lg-10 p-4">


            <!-- Header -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Schedule Follow-up
                    </h2>

                    <p class="text-muted mb-0">
                        Schedule the patient's next appointment.
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

                        <div class="col-md-4">

                            <strong>
                                Patient
                            </strong>

                            <p>
                                <?php echo htmlspecialchars(
                                    $appointment["patient_name"]
                                ); ?>
                            </p>

                        </div>


                        <div class="col-md-4">

                            <strong>
                                Patient Code
                            </strong>

                            <p>
                                <?php echo htmlspecialchars(
                                    $appointment["patient_code"]
                                ); ?>
                            </p>

                        </div>


                        <div class="col-md-4">

                            <strong>
                                Doctor
                            </strong>

                            <p>
                                <?php echo htmlspecialchars(
                                    $appointment["doctor_name"]
                                ); ?>
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Follow-up Form -->

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="fw-bold mb-0">
                        Follow-up Details
                    </h5>

                </div>


                <div class="card-body">

                    <form method="POST">


                        <!-- Date -->

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Follow-up Date

                            </label>

                            <input
                                type="date"
                                name="follow_up_date"
                                class="form-control"
                                min="<?php echo date('Y-m-d'); ?>"
                                required
                            >

                        </div>


                        <!-- Reason -->

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Reason

                            </label>

                            <textarea
                                name="reason"
                                class="form-control"
                                rows="4"
                                placeholder="Enter reason for follow-up..."
                            ></textarea>

                        </div>


                        <!-- Notes -->

                        <div class="mb-4">

                            <label class="form-label fw-bold">

                                Notes

                            </label>

                            <textarea
                                name="notes"
                                class="form-control"
                                rows="4"
                                placeholder="Enter any additional instructions..."
                            ></textarea>

                        </div>


                        <!-- Buttons -->

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Schedule Follow-up
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