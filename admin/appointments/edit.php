<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

$error = "";

/*
|--------------------------------------------------------------------------
| Check Appointment ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$appointmentId = (int) $_GET["id"];

/*
|--------------------------------------------------------------------------
| Get Existing Appointment
|--------------------------------------------------------------------------
*/

$sql = "SELECT *
        FROM appointments
        WHERE id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointmentId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: index.php");
    exit;
}

$appointment = $result->fetch_assoc();

$stmt->close();

/*
|--------------------------------------------------------------------------
| Update Appointment
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $patient_id = (int) $_POST["patient_id"];
    $doctor_id = (int) $_POST["doctor_id"];
    $appointment_date = trim($_POST["appointment_date"]);
    $appointment_time = trim($_POST["appointment_time"]);
    $reason = trim($_POST["reason"]);
    $notes = trim($_POST["notes"]);
    $status = trim($_POST["status"]);

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    $allowedStatuses = [
        "Pending",
        "Confirmed",
        "Completed",
        "Cancelled"
    ];

    if (
        $patient_id <= 0 ||
        $doctor_id <= 0 ||
        $appointment_date === "" ||
        $appointment_time === ""
    ) {

        $error = "Please fill all required fields.";

    } elseif (!in_array($status, $allowedStatuses, true)) {

        $error = "Please select a valid appointment status.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Check Patient
        |--------------------------------------------------------------------------
        */

        $patientSql = "SELECT id
                       FROM patients
                       WHERE id = ?
                       LIMIT 1";

        $patientStmt = $conn->prepare($patientSql);

        $patientStmt->bind_param(
            "i",
            $patient_id
        );

        $patientStmt->execute();

        $patientResult = $patientStmt->get_result();

        if ($patientResult->num_rows !== 1) {

            $error = "Please select a valid patient.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | Check Doctor
            |--------------------------------------------------------------------------
            */

            $doctorSql = "SELECT id
                          FROM doctors
                          WHERE id = ?
                          LIMIT 1";

            $doctorStmt = $conn->prepare($doctorSql);

            $doctorStmt->bind_param(
                "i",
                $doctor_id
            );

            $doctorStmt->execute();

            $doctorResult = $doctorStmt->get_result();

            if ($doctorResult->num_rows !== 1) {

                $error = "Please select a valid doctor.";

            } else {

                /*
                |--------------------------------------------------------------------------
                | Check Duplicate Appointment
                |--------------------------------------------------------------------------
                */

                $duplicateSql = "SELECT id
                                 FROM appointments
                                 WHERE doctor_id = ?
                                 AND appointment_date = ?
                                 AND appointment_time = ?
                                 AND id != ?
                                 LIMIT 1";

                $duplicateStmt = $conn->prepare($duplicateSql);

                $duplicateStmt->bind_param(
                    "issi",
                    $doctor_id,
                    $appointment_date,
                    $appointment_time,
                    $appointmentId
                );

                $duplicateStmt->execute();

                $duplicateResult =
                    $duplicateStmt->get_result();

                if ($duplicateResult->num_rows > 0) {

                    $error =
                        "This doctor is already booked for the selected date and time.";

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Update Appointment
                    |--------------------------------------------------------------------------
                    */

                    $sql = "UPDATE appointments
                            SET
                                patient_id = ?,
                                doctor_id = ?,
                                appointment_date = ?,
                                appointment_time = ?,
                                reason = ?,
                                notes = ?,
                                status = ?
                            WHERE id = ?";

                    $stmt = $conn->prepare($sql);

                    $stmt->bind_param(
                        "iisssssi",
                        $patient_id,
                        $doctor_id,
                        $appointment_date,
                        $appointment_time,
                        $reason,
                        $notes,
                        $status,
                        $appointmentId
                    );

                    if ($stmt->execute()) {

                        $stmt->close();
                        $duplicateStmt->close();
                        $doctorStmt->close();
                        $patientStmt->close();

                        header(
                            "Location: index.php?success=Appointment updated successfully"
                        );

                        exit;

                    } else {

                        $error =
                            "Appointment could not be updated.";
                    }

                    $stmt->close();
                }

                $duplicateStmt->close();
            }

            $doctorStmt->close();
        }

        $patientStmt->close();
    }

    /*
    |--------------------------------------------------------------------------
    | Keep Entered Values After Error
    |--------------------------------------------------------------------------
    */

    $appointment["patient_id"] = $patient_id;
    $appointment["doctor_id"] = $doctor_id;
    $appointment["appointment_date"] = $appointment_date;
    $appointment["appointment_time"] = $appointment_time;
    $appointment["reason"] = $reason;
    $appointment["notes"] = $notes;
    $appointment["status"] = $status;
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
                        Edit Appointment
                    </h2>

                    <p class="text-muted mb-0">
                        Update appointment information
                    </p>

                </div>

                <a
                    href="index.php"
                    class="btn btn-secondary">
                    Back to Appointments
                </a>

            </div>


            <?php if ($error !== ""): ?>

                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>


            <div class="card shadow-sm">

                <div class="card-body">

                    <form method="POST">

                        <div class="row">

                            <!-- Patient -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Patient *
                                </label>

                                <select
                                    name="patient_id"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        Select Patient
                                    </option>

                                    <?php

                                    $patientSql =
                                        "SELECT id, patient_code, name
                                         FROM patients
                                         ORDER BY name ASC";

                                    $patientListStmt =
                                        $conn->prepare($patientSql);

                                    $patientListStmt->execute();

                                    $patientListResult =
                                        $patientListStmt->get_result();

                                    while (
                                        $patient =
                                        $patientListResult->fetch_assoc()
                                    ):

                                    ?>

                                        <option
                                            value="<?php echo $patient["id"]; ?>"
                                            <?php
                                            echo (
                                                $appointment["patient_id"] ==
                                                $patient["id"]
                                            )
                                                ? "selected"
                                                : "";
                                            ?>
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $patient["patient_code"] .
                                                " - " .
                                                $patient["name"]
                                            );
                                            ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>


                            <!-- Doctor -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Doctor *
                                </label>

                                <select
                                    name="doctor_id"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        Select Doctor
                                    </option>

                                    <?php

                                    $doctorSql =
                                        "SELECT
                                            doctors.id,
                                            doctors.name,
                                            doctors.specialization,
                                            departments.name
                                                AS department_name
                                         FROM doctors
                                         LEFT JOIN departments
                                            ON doctors.department_id =
                                               departments.id
                                         ORDER BY doctors.name ASC";

                                    $doctorListStmt =
                                        $conn->prepare($doctorSql);

                                    $doctorListStmt->execute();

                                    $doctorListResult =
                                        $doctorListStmt->get_result();

                                    while (
                                        $doctor =
                                        $doctorListResult->fetch_assoc()
                                    ):

                                    ?>

                                        <option
                                            value="<?php echo $doctor["id"]; ?>"
                                            <?php
                                            echo (
                                                $appointment["doctor_id"] ==
                                                $doctor["id"]
                                            )
                                                ? "selected"
                                                : "";
                                            ?>
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $doctor["name"] .
                                                " - " .
                                                $doctor["specialization"] .
                                                " (" .
                                                $doctor["department_name"] .
                                                ")"
                                            );
                                            ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>


                            <!-- Appointment Date -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Appointment Date *
                                </label>

                                <input
                                    type="date"
                                    name="appointment_date"
                                    class="form-control"
                                    required
                                    value="<?php
                                    echo htmlspecialchars(
                                        $appointment["appointment_date"]
                                    );
                                    ?>"
                                >

                            </div>


                            <!-- Appointment Time -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Appointment Time *
                                </label>

                                <input
                                    type="time"
                                    name="appointment_time"
                                    class="form-control"
                                    required
                                    value="<?php
                                    echo htmlspecialchars(
                                        $appointment["appointment_time"]
                                    );
                                    ?>"
                                >

                            </div>


                            <!-- Status -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Status *
                                </label>

                                <select
                                    name="status"
                                    class="form-select"
                                    required>

                                    <?php

                                    $statuses = [
                                        "Pending",
                                        "Confirmed",
                                        "Completed",
                                        "Cancelled"
                                    ];

                                    foreach ($statuses as $statusOption):

                                    ?>

                                        <option
                                            value="<?php echo $statusOption; ?>"
                                            <?php
                                            echo (
                                                $appointment["status"] ==
                                                $statusOption
                                            )
                                                ? "selected"
                                                : "";
                                            ?>
                                        >
                                            <?php echo $statusOption; ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <!-- Reason -->

                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Reason
                                </label>

                                <textarea
                                    name="reason"
                                    class="form-control"
                                    rows="3"><?php
                                    echo htmlspecialchars(
                                        $appointment["reason"] ?? ""
                                    );
                                    ?></textarea>

                            </div>


                            <!-- Notes -->

                            <div class="col-md-12 mb-4">

                                <label class="form-label">
                                    Notes
                                </label>

                                <textarea
                                    name="notes"
                                    class="form-control"
                                    rows="3"><?php
                                    echo htmlspecialchars(
                                        $appointment["notes"] ?? ""
                                    );
                                    ?></textarea>

                            </div>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary">
                            Update Appointment
                        </button>

                        <a
                            href="index.php"
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