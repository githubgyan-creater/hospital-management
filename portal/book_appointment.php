<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["portal_patient_id"])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION["portal_patient_id"];

$message = "";
$message_type = "";

$selected_department = "";
$selected_doctor = "";
$appointment_date = "";
$appointment_time = "";
$reason = "";


/* =========================
   HANDLE APPOINTMENT BOOKING
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $selected_department = trim($_POST["department_id"] ?? "");
    $selected_doctor = trim($_POST["doctor_id"] ?? "");
    $appointment_date = trim($_POST["appointment_date"] ?? "");
    $appointment_time = trim($_POST["appointment_time"] ?? "");
    $reason = trim($_POST["reason"] ?? "");


    if (
        $selected_department === "" ||
        $selected_doctor === "" ||
        $appointment_date === "" ||
        $appointment_time === ""
    ) {

        $message = "Please fill all required fields.";
        $message_type = "danger";

    } elseif (
        !ctype_digit($selected_department) ||
        !ctype_digit($selected_doctor)
    ) {

        $message = "Invalid department or doctor.";
        $message_type = "danger";

    } elseif ($appointment_date < date("Y-m-d")) {

        $message = "Appointment date cannot be in the past.";
        $message_type = "danger";

    } else {

        $department_id = (int) $selected_department;
        $doctor_id = (int) $selected_doctor;


        /* =========================
           CHECK DEPARTMENT
        ========================== */

        $stmt = $conn->prepare(
            "SELECT id
             FROM departments
             WHERE id = ?
             AND status = 1
             LIMIT 1"
        );

        $stmt->bind_param(
            "i",
            $department_id
        );

        $stmt->execute();

        $department_result = $stmt->get_result();

        $department_exists =
            $department_result->num_rows === 1;

        $stmt->close();


        /* =========================
           CHECK DOCTOR
        ========================== */

        $doctor_exists = false;

        if ($department_exists) {

            $stmt = $conn->prepare(
                "SELECT id
                 FROM doctors
                 WHERE id = ?
                 AND department_id = ?
                 AND status = 1
                 LIMIT 1"
            );

            $stmt->bind_param(
                "ii",
                $doctor_id,
                $department_id
            );

            $stmt->execute();

            $doctor_result = $stmt->get_result();

            $doctor_exists =
                $doctor_result->num_rows === 1;

            $stmt->close();
        }


        if (!$department_exists) {

            $message =
                "Selected department is not available.";

            $message_type = "danger";

        } elseif (!$doctor_exists) {

            $message =
                "Selected doctor is not available for this department.";

            $message_type = "danger";

        } else {


            /* =========================
               INSERT APPOINTMENT
            ========================== */

            $stmt = $conn->prepare(
                "INSERT INTO appointments
                (
                    patient_id,
                    doctor_id,
                    appointment_date,
                    appointment_time,
                    reason,
                    status
                )
                VALUES (?, ?, ?, ?, ?, 'Pending')"
            );

            $stmt->bind_param(
                "iisss",
                $patient_id,
                $doctor_id,
                $appointment_date,
                $appointment_time,
                $reason
            );


            if ($stmt->execute()) {

                $appointment_id =
                    $stmt->insert_id;

                $message =
                    "Appointment booked successfully. Appointment ID: "
                    . $appointment_id;

                $message_type = "success";

                $selected_department = "";
                $selected_doctor = "";
                $appointment_date = "";
                $appointment_time = "";
                $reason = "";

            } else {

                $message =
                    "Unable to book appointment. Please try again.";

                $message_type = "danger";
            }

            $stmt->close();
        }
    }
}


/* =========================
   GET DEPARTMENTS
========================= */

$departments = [];

$result = $conn->query(
    "SELECT
        id,
        name
     FROM departments
     WHERE status = 1
     ORDER BY name ASC"
);

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $departments[] = $row;
    }
}


/* =========================
   GET DOCTORS
========================= */

$doctors = [];

$result = $conn->query(
    "SELECT
        d.id,
        d.name,
        d.department_id,
        d.specialization,
        d.consultation_fee,
        dep.name AS department_name
     FROM doctors d
     INNER JOIN departments dep
        ON d.department_id = dep.id
     WHERE d.status = 1
     ORDER BY d.name ASC"
);

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $doctors[] = $row;
    }
}


require_once "../includes/header.php";

?>

<div class="container py-4">

    <!-- =========================
         HEADER
    ========================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Book Appointment
            </h2>

            <p class="text-muted mb-0">
                Schedule an appointment with a hospital doctor.
            </p>

        </div>

        <a
            href="dashboard.php"
            class="btn btn-secondary"
        >
            Back to Dashboard
        </a>

    </div>


    <!-- =========================
         MESSAGE
    ========================== -->

    <?php if ($message !== ""): ?>

        <div class="alert alert-<?php echo $message_type; ?>">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


    <!-- =========================
         APPOINTMENT FORM
    ========================== -->

    <div class="card shadow-sm border-0">

        <div class="card-body p-4">

            <form method="POST">


                <!-- DEPARTMENT -->

                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Department
                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="department_id"
                        id="department_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Department
                        </option>

                        <?php foreach (
                            $departments
                            as $department
                        ): ?>

                            <option
                                value="<?php
                                echo $department["id"];
                                ?>"
                                <?php
                                echo (
                                    $selected_department ==
                                    $department["id"]
                                )
                                    ? "selected"
                                    : "";
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $department["name"]
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- DOCTOR -->

                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Doctor
                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="doctor_id"
                        id="doctor_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Doctor
                        </option>

                        <?php foreach (
                            $doctors
                            as $doctor
                        ): ?>

                            <option
                                value="<?php
                                echo $doctor["id"];
                                ?>"
                                data-department="<?php
                                echo $doctor["department_id"];
                                ?>"
                                <?php
                                echo (
                                    $selected_doctor ==
                                    $doctor["id"]
                                )
                                    ? "selected"
                                    : "";
                                ?>
                            >

                                <?php

                                echo htmlspecialchars(
                                    $doctor["name"]
                                );

                                echo " - ";

                                echo htmlspecialchars(
                                    $doctor["specialization"]
                                );

                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <small class="text-muted">
                        Only doctors from the selected department will be shown.
                    </small>

                </div>


                <!-- DATE -->

                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Appointment Date
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="date"
                        name="appointment_date"
                        class="form-control"
                        min="<?php echo date("Y-m-d"); ?>"
                        value="<?php
                        echo htmlspecialchars(
                            $appointment_date
                        );
                        ?>"
                        required
                    >

                </div>


                <!-- TIME -->

                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Appointment Time
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="time"
                        name="appointment_time"
                        class="form-control"
                        value="<?php
                        echo htmlspecialchars(
                            $appointment_time
                        );
                        ?>"
                        required
                    >

                </div>


                <!-- REASON -->

                <div class="mb-4">

                    <label class="form-label fw-bold">

                        Reason for Visit

                    </label>

                    <textarea
                        name="reason"
                        class="form-control"
                        rows="4"
                        placeholder="Enter your symptoms or reason for consultation"
                    ><?php
                    echo htmlspecialchars($reason);
                    ?></textarea>

                </div>


                <!-- BUTTONS -->

                <div class="d-flex justify-content-between">

                    <a
                        href="dashboard.php"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-success"
                    >
                        Book Appointment
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const departmentSelect =
            document.getElementById("department_id");

        const doctorSelect =
            document.getElementById("doctor_id");

        const doctorOptions =
            Array.from(
                doctorSelect.options
            );


        function filterDoctors() {

            const departmentId =
                departmentSelect.value;


            doctorSelect.innerHTML = "";

            const defaultOption =
                document.createElement("option");

            defaultOption.value = "";

            defaultOption.textContent =
                "Select Doctor";

            doctorSelect.appendChild(
                defaultOption
            );


            doctorOptions.forEach(
                function (option) {

                    if (!option.value) {
                        return;
                    }

                    const doctorDepartment =
                        option.getAttribute(
                            "data-department"
                        );


                    if (
                        departmentId === "" ||
                        doctorDepartment ===
                        departmentId
                    ) {

                        const newOption =
                            option.cloneNode(true);

                        doctorSelect.appendChild(
                            newOption
                        );
                    }

                }
            );

        }


        departmentSelect.addEventListener(
            "change",
            function () {

                filterDoctors();

            }
        );


        filterDoctors();

    }
);

</script>


<?php

require_once "../includes/footer.php";

?>