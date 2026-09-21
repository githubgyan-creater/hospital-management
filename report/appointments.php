<?php

session_start();

if (
    !isset($_SESSION["user_role"]) ||
    !in_array($_SESSION["user_role"], ["admin", "receptionist"])
) {
    header("Location: ../login.php");
    exit();
}

require_once "../config/database.php";


/* Get appointment records */

$query = "
    SELECT
        appointments.*,
        patients.patient_code,
        patients.name AS patient_name,
        doctors.name AS doctor_name,
        departments.name AS department_name
    FROM appointments

    LEFT JOIN patients
        ON appointments.patient_id = patients.id

    LEFT JOIN doctors
        ON appointments.doctor_id = doctors.id
 LEFT JOIN departments
    ON doctors.department_id = departments.id

    ORDER BY appointments.appointment_date DESC
";

$result = $conn->query($query);


require_once "../includes/header.php";

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->

        <div class="col-md-2 p-0">

            <?php require_once "../includes/sidebar.php"; ?>

        </div>


        <!-- Main Content -->

        <div class="col-md-10">

            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>

                        <h2>Appointment Reports</h2>

                        <p class="text-muted">
                            View appointment records and scheduling information
                        </p>

                    </div>

                    <a
                        href="index.php"
                        class="btn btn-secondary"
                    >
                        Back to Reports
                    </a>

                </div>


                <div class="card">

                    <div class="card-header bg-primary text-white">

                        <strong>
                            Appointment Report
                        </strong>

                    </div>


                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover">

                                <thead class="table-primary">

                                    <tr>

                                        <th>Appointment ID</th>

                                        <th>Patient</th>

                                        <th>Doctor</th>

                                        <th>Department</th>

                                        <th>Date</th>

                                        <th>Time</th>

                                        <th>Status</th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php if ($result && $result->num_rows > 0): ?>

                                        <?php while ($appointment = $result->fetch_assoc()): ?>

                                            <tr>

                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $appointment["id"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        ($appointment["patient_code"] ?? "-")
                                                        . " - "
                                                        . ($appointment["patient_name"] ?? "-")
                                                    );
                                                    ?>

                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $appointment["doctor_name"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $appointment["department_name"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $appointment["appointment_date"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $appointment["appointment_time"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>

                                                    <?php
                                                    $status =
                                                        $appointment["status"] ?? "-";
                                                    ?>

                                                    <?php if ($status === "Confirmed"): ?>

                                                        <span class="badge bg-success">
                                                            Confirmed
                                                        </span>

                                                    <?php elseif ($status === "Pending"): ?>

                                                        <span class="badge bg-warning text-dark">
                                                            Pending
                                                        </span>

                                                    <?php elseif ($status === "Cancelled"): ?>

                                                        <span class="badge bg-danger">
                                                            Cancelled
                                                        </span>

                                                    <?php elseif ($status === "Completed"): ?>

                                                        <span class="badge bg-primary">
                                                            Completed
                                                        </span>

                                                    <?php else: ?>

                                                        <span class="badge bg-secondary">
                                                            <?php
                                                            echo htmlspecialchars($status);
                                                            ?>
                                                        </span>

                                                    <?php endif; ?>

                                                </td>

                                            </tr>

                                        <?php endwhile; ?>

                                    <?php else: ?>

                                        <tr>

                                            <td
                                                colspan="7"
                                                class="text-center"
                                            >
                                                No appointment records found.
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