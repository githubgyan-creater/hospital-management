<?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "receptionist") {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/database.php";
require_once "../../includes/header.php";

/* Get appointments */
$sql = "
    SELECT 
        appointments.*,
        patients.name AS patient_name,
        doctors.name AS doctor_name
    FROM appointments
    LEFT JOIN patients ON appointments.patient_id = patients.id
    LEFT JOIN doctors ON appointments.doctor_id = doctors.id
    ORDER BY appointments.id DESC
";

$result = $conn->query($sql);

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

    <h2>Appointments</h2>

    <a href="add.php" class="btn btn-success">
        + Add Appointment
    </a>

</div>

                <div class="card">

                    <div class="card-header bg-dark text-white">
                        <strong>Appointment List</strong>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped">

                                <thead class="table-dark">

                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Appointment Date</th>
                                        <th>Appointment Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>

                                </thead>

                                <tbody>

                                <?php if ($result && $result->num_rows > 0): ?>

                                    <?php while ($appointment = $result->fetch_assoc()): ?>

                                        <tr>

                                            <td>
                                                <?php echo $appointment["id"]; ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($appointment["patient_name"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($appointment["doctor_name"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($appointment["appointment_date"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($appointment["appointment_time"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($appointment["status"]); ?>
                                            </td>
                                            <td>
    <a href="view.php?id=<?php echo $appointment["id"]; ?>"
       class="btn btn-sm btn-primary">
        View
    </a>
</td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="7" class="text-center">
                                            No appointments found.
                                        </td>
                                    </tr>

                                <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>