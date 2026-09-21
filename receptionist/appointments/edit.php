<?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "receptionist") {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/database.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

/* Get appointment */
$stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$appointment = $result->fetch_assoc();

if (!$appointment) {
    die("Appointment not found.");
}

/* Get patients */
$patients = $conn->query("SELECT id, patient_code, name FROM patients ORDER BY name ASC");

/* Get doctors */
$doctors = $conn->query("SELECT id, name FROM doctors ORDER BY name ASC");


/* Update appointment */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $patient_id = intval($_POST["patient_id"]);
    $doctor_id = intval($_POST["doctor_id"]);
    $appointment_date = $_POST["appointment_date"];
    $appointment_time = $_POST["appointment_time"];
    $status = $_POST["status"];

    $update = $conn->prepare("
        UPDATE appointments SET
        patient_id = ?,
        doctor_id = ?,
        appointment_date = ?,
        appointment_time = ?,
        status = ?
        WHERE id = ?
    ");

    $update->bind_param(
        "iisssi",
        $patient_id,
        $doctor_id,
        $appointment_date,
        $appointment_time,
        $status,
        $id
    );

    if ($update->execute()) {

        header("Location: view.php?id=" . $id);
        exit();

    } else {

        $error = "Error updating appointment.";

    }
}

require_once "../../includes/header.php";

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 p-0">
            <?php require_once "../../includes/sidebar.php"; ?>
        </div>


        <!-- Main Content -->
        <div class="col-md-10">

            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h2>Edit Appointment</h2>

                    <a href="view.php?id=<?php echo $appointment["id"]; ?>"
                       class="btn btn-secondary">

                        Back

                    </a>

                </div>


                <?php if (isset($error)): ?>

                    <div class="alert alert-danger">

                        <?php echo htmlspecialchars($error); ?>

                    </div>

                <?php endif; ?>


                <div class="card">

                    <div class="card-header bg-warning">

                        <strong>Edit Appointment Information</strong>

                    </div>


                    <div class="card-body">

                        <form method="POST">


                            <!-- Patient -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Patient
                                </label>

                                <select name="patient_id"
                                        class="form-control"
                                        required>

                                    <option value="">
                                        Select Patient
                                    </option>


                                    <?php while ($patient = $patients->fetch_assoc()): ?>

                                        <option
                                            value="<?php echo $patient["id"]; ?>"
                                            <?php
                                            if ($patient["id"] == $appointment["patient_id"]) {
                                                echo "selected";
                                            }
                                            ?>
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $patient["patient_code"]
                                                . " - "
                                                . $patient["name"]
                                            );

                                            ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>


                            <!-- Doctor -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Doctor
                                </label>

                                <select name="doctor_id"
                                        class="form-control"
                                        required>

                                    <option value="">
                                        Select Doctor
                                    </option>


                                    <?php while ($doctor = $doctors->fetch_assoc()): ?>

                                        <option
                                            value="<?php echo $doctor["id"]; ?>"
                                            <?php
                                            if ($doctor["id"] == $appointment["doctor_id"]) {
                                                echo "selected";
                                            }
                                            ?>
                                        >

                                            <?php echo htmlspecialchars($doctor["name"]); ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>


                            <!-- Date -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Appointment Date
                                </label>

                                <input type="date"
                                       name="appointment_date"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($appointment["appointment_date"]); ?>"
                                       required>

                            </div>


                            <!-- Time -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Appointment Time
                                </label>

                                <input type="time"
                                       name="appointment_time"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($appointment["appointment_time"]); ?>"
                                       required>

                            </div>


                            <!-- Status -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Status
                                </label>

                                <select name="status"
                                        class="form-control"
                                        required>

                                    <option value="Scheduled"
                                        <?php
                                        if ($appointment["status"] == "Scheduled") {
                                            echo "selected";
                                        }
                                        ?>>
                                        Scheduled
                                    </option>

                                    <option value="Completed"
                                        <?php
                                        if ($appointment["status"] == "Completed") {
                                            echo "selected";
                                        }
                                        ?>>
                                        Completed
                                    </option>

                                    <option value="Cancelled"
                                        <?php
                                        if ($appointment["status"] == "Cancelled") {
                                            echo "selected";
                                        }
                                        ?>>
                                        Cancelled
                                    </option>

                                </select>

                            </div>


                            <button type="submit"
                                    class="btn btn-primary">

                                Update Appointment

                            </button>


                            <a href="view.php?id=<?php echo $appointment["id"]; ?>"
                               class="btn btn-secondary">

                                Cancel

                            </a>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>