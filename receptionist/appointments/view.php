<?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "receptionist") {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/database.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$stmt = $conn->prepare("
    SELECT 
        appointments.*,
        patients.patient_code,
        patients.name AS patient_name,
        patients.phone AS patient_phone,
        doctors.name AS doctor_name
    FROM appointments
    LEFT JOIN patients ON appointments.patient_id = patients.id
    LEFT JOIN doctors ON appointments.doctor_id = doctors.id
    WHERE appointments.id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$appointment = $result->fetch_assoc();

if (!$appointment) {
    die("Appointment not found.");
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

                    <h2>Appointment Details</h2>

                    <a href="index.php" class="btn btn-secondary">
                        Back to Appointments
                    </a>

                </div>

                <div class="card">

                    <div class="card-header bg-dark text-white">
                        <strong>Appointment Information</strong>
                    </div>

                    <div class="card-body">

                        <table class="table table-bordered table-striped">

                            <tr>
                                <th width="30%">Appointment ID</th>
                                <td>
                                    <?php echo htmlspecialchars($appointment["id"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Patient ID</th>
                                <td>
                                    <?php echo htmlspecialchars($appointment["patient_code"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Patient Name</th>
                                <td>
                                    <?php echo htmlspecialchars($appointment["patient_name"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Patient Phone</th>
                                <td>
                                    <?php echo htmlspecialchars($appointment["patient_phone"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Doctor</th>
                                <td>
                                    <?php echo htmlspecialchars($appointment["doctor_name"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Appointment Date</th>
                                <td>
                                    <?php echo htmlspecialchars($appointment["appointment_date"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Appointment Time</th>
                                <td>
                                    <?php echo htmlspecialchars($appointment["appointment_time"]); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>
                                    <?php echo htmlspecialchars($appointment["status"]); ?>
                                </td>
                            </tr>

                        </table>

                        <div class="mt-3">

                            <a href="edit.php?id=<?php echo $appointment["id"]; ?>"
   class="btn btn-primary">
    Edit Appointment
</a>

<a href="delete.php?id=<?php echo $appointment["id"]; ?>"
   class="btn btn-danger"
   onclick="return confirm('Are you sure you want to delete this appointment?');">
    Delete Appointment
</a>
<a href="index.php"
   class="btn btn-secondary">
    Back
</a>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>