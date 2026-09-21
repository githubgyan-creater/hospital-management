 <?php

  

require_once "../config/database.php";
require_once "../includes/header.php";

if (!isset($_SESSION["portal_patient_id"])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION["portal_patient_id"];

$message = "";
$message_type = "";

/* =========================
   GENERATE TOKEN
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $appointment_id = (int)($_POST["appointment_id"] ?? 0);

    if ($appointment_id <= 0) {

        $message = "Invalid appointment.";
        $message_type = "danger";

    } else {

        /* Get appointment */

        $stmt = $conn->prepare("
            SELECT 
                a.id,
                a.appointment_date,
                a.appointment_time,
                a.status,
                a.doctor_id,
                d.department_id,
                d.name AS doctor_name,
                dep.name AS department_name
            FROM appointments a
            INNER JOIN doctors d 
                ON a.doctor_id = d.id
            INNER JOIN departments dep 
                ON d.department_id = dep.id
            WHERE a.id = ?
            AND a.patient_id = ?
            LIMIT 1
        ");

        $stmt->bind_param("ii", $appointment_id, $patient_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $appointment = $result->fetch_assoc();

        $stmt->close();

        if (!$appointment) {

            $message = "Appointment not found.";
            $message_type = "danger";

        } elseif ($appointment["status"] === "Cancelled") {

            $message = "Cancelled appointments cannot generate tokens.";
            $message_type = "danger";

        } elseif ($appointment["appointment_date"] < date("Y-m-d")) {

            $message = "Token cannot be generated for a past appointment.";
            $message_type = "danger";

        } else {

            /* Check whether token already exists */

            $stmt = $conn->prepare("
                SELECT id, token_number
                FROM tokens
                WHERE appointment_id = ?
                LIMIT 1
            ");

            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();

            $existing = $stmt->get_result()->fetch_assoc();

            $stmt->close();

            if ($existing) {

                $message = "Token already exists: " . $existing["token_number"];
                $message_type = "warning";

            } else {

                /* =========================
                   FIND NEXT TOKEN NUMBER
                ========================= */

                $token_date = $appointment["appointment_date"];

                $stmt = $conn->prepare("
                    SELECT token_number
                    FROM tokens
                    WHERE token_date = ?
                    ORDER BY id DESC
                    LIMIT 1
                ");

                $stmt->bind_param("s", $token_date);
                $stmt->execute();

                $last_token = $stmt->get_result()->fetch_assoc();

                $stmt->close();

                if ($last_token) {

                    /*
                     Example:
                     OPD-001
                     OPD-002
                     OPD-003
                    */

                    $last_number = (int)substr(
                        $last_token["token_number"],
                        4
                    );

                    $next_number = $last_number + 1;

                } else {

                    $next_number = 1;
                }

                $token_number = "OPD-" . str_pad(
                    $next_number,
                    3,
                    "0",
                    STR_PAD_LEFT
                );

                /* =========================
                   INSERT TOKEN
                ========================= */

                $stmt = $conn->prepare("
                    INSERT INTO tokens
                    (
                        token_number,
                        patient_id,
                        department_id,
                        doctor_id,
                        appointment_id,
                        token_date,
                        status,
                        priority
                    )
                    VALUES
                    (?, ?, ?, ?, ?, ?, 'WAITING', 'Normal')
                ");

                $stmt->bind_param(
                    "siiiis",
                    $token_number,
                    $patient_id,
                    $appointment["department_id"],
                    $appointment["doctor_id"],
                    $appointment_id,
                    $token_date
                );

                if ($stmt->execute()) {

                    $message = "Token generated successfully: " . $token_number;
                    $message_type = "success";

                } else {

                    $message = "Token could not be generated. Database Error: " . $stmt->error;
                    $message_type = "danger";
                }

                $stmt->close();
            }
        }
    }
}


/* =========================
   GET PATIENT APPOINTMENTS
========================= */

$stmt = $conn->prepare("
    SELECT
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.status,
        d.name AS doctor_name,
        dep.name AS department_name,
        t.id AS token_id,
        t.token_number,
        t.status AS token_status
    FROM appointments a

    INNER JOIN doctors d
        ON a.doctor_id = d.id

    INNER JOIN departments dep
        ON d.department_id = dep.id

    LEFT JOIN tokens t
        ON a.id = t.appointment_id

    WHERE a.patient_id = ?

    ORDER BY a.appointment_date DESC,
             a.appointment_time DESC
");

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$appointments = $stmt->get_result();

$stmt->close();

?>

<div class="container py-5">

    <div class="mb-4">

        <h2 class="fw-bold">
            Generate OPD Token
        </h2>

        <p class="text-muted">
            Select your appointment to generate your OPD token.
        </p>

    </div>


    <?php if ($message !== ""): ?>

        <div class="alert alert-<?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <?php if ($appointments->num_rows === 0): ?>

        <div class="alert alert-info">

            No appointments found.

            <br><br>

            Please book an appointment first.

        </div>

    <?php else: ?>


        <div class="row g-4">

            <?php while ($appointment = $appointments->fetch_assoc()): ?>

                <div class="col-md-6">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <h5 class="fw-bold">

                                <?php echo htmlspecialchars(
                                    $appointment["department_name"]
                                ); ?>

                            </h5>


                            <p class="mb-1">

                                <strong>Doctor:</strong>

                                <?php echo htmlspecialchars(
                                    $appointment["doctor_name"]
                                ); ?>

                            </p>


                            <p class="mb-1">

                                <strong>Date:</strong>

                                <?php echo htmlspecialchars(
                                    $appointment["appointment_date"]
                                ); ?>

                            </p>


                            <p class="mb-3">

                                <strong>Time:</strong>

                                <?php echo htmlspecialchars(
                                    $appointment["appointment_time"]
                                ); ?>

                            </p>


                            <?php if (!empty($appointment["token_id"])): ?>

                                <div class="alert alert-success">

                                    <strong>
                                        Token:
                                    </strong>

                                    <?php echo htmlspecialchars(
                                        $appointment["token_number"]
                                    ); ?>

                                    <br>

                                    <strong>
                                        Status:
                                    </strong>

                                    <?php echo htmlspecialchars(
                                        $appointment["token_status"]
                                    ); ?>

                                </div>


                                <a
                                    href="my_token.php"
                                    class="btn btn-primary"
                                >
                                    View My Token
                                </a>


                            <?php else: ?>


                                <?php if (
                                    $appointment["status"] !== "Cancelled"
                                    &&
                                    $appointment["appointment_date"] >= date("Y-m-d")
                                ): ?>

                                    <form
                                        method="POST"
                                    >

                                        <input
                                            type="hidden"
                                            name="appointment_id"
                                            value="<?php echo $appointment["id"]; ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-success"
                                        >
                                            Generate Token
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="badge bg-secondary">
                                        Token Not Available
                                    </span>

                                <?php endif; ?>


                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php endif; ?>

</div>

<?php require_once "../includes/footer.php"; ?>