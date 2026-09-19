 <?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "doctor") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

/*
|--------------------------------------------------------------------------
| Find Doctor Profile Using Logged-in Email
|--------------------------------------------------------------------------
*/

$userEmail = $_SESSION["user_email"];

$doctorSql = "SELECT id
              FROM doctors
              WHERE email = ?
              LIMIT 1";

$doctorStmt = $conn->prepare($doctorSql);

$doctorStmt->bind_param("s", $userEmail);

$doctorStmt->execute();

$doctorResult = $doctorStmt->get_result();

if ($doctorResult->num_rows !== 1) {

    $doctorStmt->close();

    die("Doctor profile not found.");
}

$doctor = $doctorResult->fetch_assoc();

$doctorId = (int) $doctor["id"];

$doctorStmt->close();

$error = "";

/*
|--------------------------------------------------------------------------
| Check Appointment ID
|--------------------------------------------------------------------------
*/

if (
    !isset($_GET["appointment_id"]) ||
    !is_numeric($_GET["appointment_id"])
) {
    header("Location: ../appointments/index.php");
    exit;
}

$appointmentId = (int) $_GET["appointment_id"];

/*
|--------------------------------------------------------------------------
| Get Appointment
| Only this Doctor's appointment can be accessed
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            appointments.id,
            appointments.patient_id,
            appointments.doctor_id,
            appointments.appointment_date,
            appointments.appointment_time,
            appointments.status,
            patients.patient_code,
            patients.name AS patient_name
        FROM appointments

        INNER JOIN patients
            ON appointments.patient_id = patients.id

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

    header("Location: ../appointments/index.php");
    exit;
}

$appointment = $result->fetch_assoc();

$stmt->close();

/*
|--------------------------------------------------------------------------
| Save Consultation
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $symptoms = trim($_POST["symptoms"]);
    $diagnosis = trim($_POST["diagnosis"]);
    $consultation_notes = trim($_POST["consultation_notes"]);
    $advice = trim($_POST["advice"]);

    $follow_up_date = !empty($_POST["follow_up_date"])
        ? $_POST["follow_up_date"]
        : null;

    if (
        $symptoms === "" ||
        $diagnosis === "" ||
        $consultation_notes === ""
    ) {

        $error = "Please fill all required consultation fields.";

    } else {

        /*
        Check whether consultation already exists
        */

        $checkSql = "SELECT id
                     FROM consultations
                     WHERE appointment_id = ?
                     LIMIT 1";

        $checkStmt = $conn->prepare($checkSql);

        $checkStmt->bind_param(
            "i",
            $appointmentId
        );

        $checkStmt->execute();

        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {

            $error = "A consultation already exists for this appointment.";

        } else {

            /*
            Insert consultation
            */

            $sql = "INSERT INTO consultations
                    (
                        appointment_id,
                        patient_id,
                        doctor_id,
                        symptoms,
                        diagnosis,
                        consultation_notes,
                        advice,
                        follow_up_date
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $patientId = (int) $appointment["patient_id"];

            $stmt->bind_param(
                "iiisssss",
                $appointmentId,
                $patientId,
                $doctorId,
                $symptoms,
                $diagnosis,
                $consultation_notes,
                $advice,
                $follow_up_date
            );

            if ($stmt->execute()) {

                $stmt->close();
                $checkStmt->close();

                header(
                    "Location: ../appointments/view.php?id=" .
                    $appointmentId .
                    "&success=Consultation added successfully"
                );

                exit;

            } else {

                $error = "Consultation could not be saved.";
            }

            $stmt->close();
        }

        $checkStmt->close();
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

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Add Consultation
                    </h2>

                    <p class="text-muted mb-0">
                        Add consultation notes for the patient
                    </p>

                </div>

                <a
                    href="../appointments/view.php?id=<?php echo $appointmentId; ?>"
                    class="btn btn-secondary">
                    Back to Appointment
                </a>

            </div>


            <?php if ($error !== ""): ?>

                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>


            <!-- Appointment Summary -->

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

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $appointment["id"]
                                );
                                ?>
                            </div>

                        </div>

                        <div class="col-md-4 mb-3">

                            <strong>Patient ID</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $appointment["patient_code"]
                                );
                                ?>
                            </div>

                        </div>

                        <div class="col-md-4 mb-3">

                            <strong>Patient Name</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $appointment["patient_name"]
                                );
                                ?>
                            </div>

                        </div>

                        <div class="col-md-4 mb-3">

                            <strong>Appointment Date</strong>

                            <div>
                                <?php
                                echo date(
                                    "d-m-Y",
                                    strtotime(
                                        $appointment["appointment_date"]
                                    )
                                );
                                ?>
                            </div>

                        </div>

                        <div class="col-md-4 mb-3">

                            <strong>Appointment Time</strong>

                            <div>
                                <?php
                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $appointment["appointment_time"]
                                    )
                                );
                                ?>
                            </div>

                        </div>

                        <div class="col-md-4 mb-3">

                            <strong>Status</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $appointment["status"]
                                );
                                ?>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Consultation Form -->

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="fw-bold mb-0">
                        Consultation Notes
                    </h5>

                </div>

                <div class="card-body">

                    <form method="POST">

                        <!-- Symptoms -->

                        <div class="mb-3">

                            <label class="form-label">
                                Symptoms *
                            </label>

                            <textarea
                                name="symptoms"
                                class="form-control"
                                rows="4"
                                placeholder="Enter patient symptoms"
                                required><?php
                                echo isset($_POST["symptoms"])
                                    ? htmlspecialchars(
                                        $_POST["symptoms"]
                                    )
                                    : "";
                                ?></textarea>

                        </div>


                        <!-- Diagnosis -->

                        <div class="mb-3">

                            <label class="form-label">
                                Diagnosis *
                            </label>

                            <textarea
                                name="diagnosis"
                                class="form-control"
                                rows="4"
                                placeholder="Enter diagnosis"
                                required><?php
                                echo isset($_POST["diagnosis"])
                                    ? htmlspecialchars(
                                        $_POST["diagnosis"]
                                    )
                                    : "";
                                ?></textarea>

                        </div>


                        <!-- Consultation Notes -->

                        <div class="mb-3">

                            <label class="form-label">
                                Consultation Notes *
                            </label>

                            <textarea
                                name="consultation_notes"
                                class="form-control"
                                rows="5"
                                placeholder="Enter consultation notes"
                                required><?php
                                echo isset($_POST["consultation_notes"])
                                    ? htmlspecialchars(
                                        $_POST["consultation_notes"]
                                    )
                                    : "";
                                ?></textarea>

                        </div>


                        <!-- Advice -->

                        <div class="mb-3">

                            <label class="form-label">
                                Advice
                            </label>

                            <textarea
                                name="advice"
                                class="form-control"
                                rows="4"
                                placeholder="Enter advice"><?php
                                echo isset($_POST["advice"])
                                    ? htmlspecialchars(
                                        $_POST["advice"]
                                    )
                                    : "";
                                ?></textarea>

                        </div>


                        <!-- Follow-up -->

                        <div class="mb-4">

                            <label class="form-label">
                                Follow-up Date
                            </label>

                            <input
                                type="date"
                                name="follow_up_date"
                                class="form-control"
                                value="<?php
                                echo isset($_POST["follow_up_date"])
                                    ? htmlspecialchars(
                                        $_POST["follow_up_date"]
                                    )
                                    : "";
                                ?>"
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary">
                            Save Consultation
                        </button>

                        <a
                            href="../appointments/view.php?id=<?php echo $appointmentId; ?>"
                            class="btn btn-secondary">
                            Cancel
                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once "../../includes/footer.php";

?>