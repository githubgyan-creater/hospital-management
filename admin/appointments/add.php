<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

$error = "";


//   Book Appointment


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $patient_id = (int) $_POST["patient_id"];
    $doctor_id = (int) $_POST["doctor_id"];
    $appointment_date = trim($_POST["appointment_date"]);
    $appointment_time = trim($_POST["appointment_time"]);
    $reason = trim($_POST["reason"]);
    $notes = trim($_POST["notes"]);


    // Required Field Validation


    if (
        $patient_id <= 0 ||
        $doctor_id <= 0 ||
        $appointment_date === "" ||
        $appointment_time === ""
    ) {

        $error = "Please fill all required fields.";

    } else {

        // Check Patient


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

            //  Check Doctor

            $doctorSql = "SELECT id
                          FROM doctors
                          WHERE id = ?
                          AND status = 1
                          LIMIT 1";

            $doctorStmt = $conn->prepare($doctorSql);

            $doctorStmt->bind_param(
                "i",
                $doctor_id
            );

            $doctorStmt->execute();

            $doctorResult = $doctorStmt->get_result();

            if ($doctorResult->num_rows !== 1) {

                $error = "Please select a valid active doctor.";

            } else {


            //  Check Duplicate Appointment


                $duplicateSql = "SELECT id
                                 FROM appointments
                                 WHERE doctor_id = ?
                                 AND appointment_date = ?
                                 AND appointment_time = ?
                                 LIMIT 1";

                $duplicateStmt = $conn->prepare(
                    $duplicateSql
                );

                $duplicateStmt->bind_param(
                    "iss",
                    $doctor_id,
                    $appointment_date,
                    $appointment_time
                );

                $duplicateStmt->execute();

                $duplicateResult =
                    $duplicateStmt->get_result();

                if ($duplicateResult->num_rows > 0) {

                    $error =
                        "This doctor is already booked for the selected date and time.";

                } else {

                    // Insert Appointment


                    $sql = "INSERT INTO appointments
                            (
                                patient_id,
                                doctor_id,
                                appointment_date,
                                appointment_time,
                                reason,
                                notes,
                                status
                            )
                            VALUES (?, ?, ?, ?, ?, ?, 'Pending')";

                    $stmt = $conn->prepare($sql);

                    $stmt->bind_param(
                        "iissss",
                        $patient_id,
                        $doctor_id,
                        $appointment_date,
                        $appointment_time,
                        $reason,
                        $notes
                    );

                    if ($stmt->execute()) {

                        $stmt->close();
                        $duplicateStmt->close();
                        $doctorStmt->close();
                        $patientStmt->close();

                        header(
                            "Location: index.php?success=Appointment booked successfully"
                        );

                        exit;

                    } else {

                        $error =
                            "Appointment could not be booked.";
                    }

                    $stmt->close();
                }

                $duplicateStmt->close();
            }

            $doctorStmt->close();
        }

        $patientStmt->close();
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
                        Book Appointment
                    </h2>

                    <p class="text-muted mb-0">
                        Create a new patient appointment
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
                                                isset($_POST["patient_id"]) &&
                                                $_POST["patient_id"] ==
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
                                         WHERE doctors.status = 1
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
                                                isset($_POST["doctor_id"]) &&
                                                $_POST["doctor_id"] ==
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
                                    min="<?php echo date("Y-m-d"); ?>"
                                    value="<?php
                                    echo isset(
                                        $_POST["appointment_date"]
                                    )
                                        ? htmlspecialchars(
                                            $_POST["appointment_date"]
                                        )
                                        : "";
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
                                    echo isset(
                                        $_POST["appointment_time"]
                                    )
                                        ? htmlspecialchars(
                                            $_POST["appointment_time"]
                                        )
                                        : "";
                                    ?>"
                                >

                            </div>


                            <!-- Reason -->

                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Reason
                                </label>

                                <textarea
                                    name="reason"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Enter reason for appointment"><?php
                                    echo isset($_POST["reason"])
                                        ? htmlspecialchars(
                                            $_POST["reason"]
                                        )
                                        : "";
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
                                    rows="3"
                                    placeholder="Enter additional notes"><?php
                                    echo isset($_POST["notes"])
                                        ? htmlspecialchars(
                                            $_POST["notes"]
                                        )
                                        : "";
                                    ?></textarea>

                            </div>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary">
                            Book Appointment
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