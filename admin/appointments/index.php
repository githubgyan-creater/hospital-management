 <?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";
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

            <!-- Success Message -->
            <?php if (isset($_GET["success"])): ?>

                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET["success"]); ?>
                </div>

            <?php endif; ?>

            <!-- Error Message -->
            <?php if (isset($_GET["error"])): ?>

                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_GET["error"]); ?>
                </div>

            <?php endif; ?>


            <!-- Page Heading -->
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Appointments
                    </h2>

                    <p class="text-muted mb-0">
                        Manage hospital appointments
                    </p>

                </div>

                <a href="add.php" class="btn btn-primary">
                    + Book Appointment
                </a>

            </div>


            <!-- Appointment Table -->
            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-primary">

                                <tr>

                                    <th>Appointment ID</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th style="min-width: 250px;">
                                        Action
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php

                                $sql = "SELECT
                                            appointments.*,
                                            patients.name AS patient_name,
                                            doctors.name AS doctor_name,
                                            departments.name AS department_name
                                        FROM appointments

                                        INNER JOIN patients
                                            ON appointments.patient_id = patients.id

                                        INNER JOIN doctors
                                            ON appointments.doctor_id = doctors.id

                                        INNER JOIN departments
                                            ON doctors.department_id = departments.id

                                        ORDER BY
                                            appointments.appointment_date DESC,
                                            appointments.appointment_time DESC";

                                $stmt = $conn->prepare($sql);

                                $stmt->execute();

                                $result = $stmt->get_result();


                                if ($result->num_rows > 0) {

                                    while ($appointment = $result->fetch_assoc()) {

                                ?>

                                        <tr>

                                            <!-- Appointment ID -->
                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $appointment["id"]
                                                );
                                                ?>
                                            </td>


                                            <!-- Patient -->
                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $appointment["patient_name"]
                                                );
                                                ?>
                                            </td>


                                            <!-- Doctor -->
                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $appointment["doctor_name"]
                                                );
                                                ?>
                                            </td>


                                            <!-- Department -->
                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $appointment["department_name"]
                                                );
                                                ?>
                                            </td>


                                            <!-- Date -->
                                            <td>
                                                <?php
                                                echo date(
                                                    "d-m-Y",
                                                    strtotime(
                                                        $appointment["appointment_date"]
                                                    )
                                                );
                                                ?>
                                            </td>


                                            <!-- Time -->
                                            <td>
                                                <?php
                                                echo date(
                                                    "h:i A",
                                                    strtotime(
                                                        $appointment["appointment_time"]
                                                    )
                                                );
                                                ?>
                                            </td>


                                            <!-- Reason -->
                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $appointment["reason"]
                                                    ?: "Not provided"
                                                );
                                                ?>
                                            </td>


                                            <!-- Status -->
                                            <td>

                                                <?php

                                                $status =
                                                    $appointment["status"];

                                                if ($status === "Pending") {

                                                    echo '<span class="badge bg-warning text-dark">Pending</span>';

                                                } elseif ($status === "Confirmed") {

                                                    echo '<span class="badge bg-primary">Confirmed</span>';

                                                } elseif ($status === "Completed") {

                                                    echo '<span class="badge bg-success">Completed</span>';

                                                } elseif ($status === "Cancelled") {

                                                    echo '<span class="badge bg-danger">Cancelled</span>';

                                                }

                                                ?>

                                            </td>


                                            <!-- Actions -->
                                            <td>

    <div class="d-flex flex-wrap gap-1">

        <a
            href="view.php?id=<?php echo $appointment["id"]; ?>"
            class="btn btn-sm btn-info">
            View
        </a>

        <a
            href="edit.php?id=<?php echo $appointment["id"]; ?>"
            class="btn btn-sm btn-warning">
            Edit
        </a>


        <?php if ($appointment["status"] === "Pending"): ?>

            <a
                href="update_status.php?id=<?php echo $appointment["id"]; ?>&status=Confirmed"
                class="btn btn-sm btn-primary">
                Confirm
            </a>

            <a
                href="update_status.php?id=<?php echo $appointment["id"]; ?>&status=Cancelled"
                class="btn btn-sm btn-danger"
                onclick="return confirm('Cancel this appointment?');">
                Cancel
            </a>


        <?php elseif ($appointment["status"] === "Confirmed"): ?>

            <a
                href="update_status.php?id=<?php echo $appointment["id"]; ?>&status=Completed"
                class="btn btn-sm btn-success">
                Complete
            </a>

            <a
                href="update_status.php?id=<?php echo $appointment["id"]; ?>&status=Cancelled"
                class="btn btn-sm btn-danger"
                onclick="return confirm('Cancel this appointment?');">
                Cancel
            </a>

        <?php endif; ?>

    </div>

</td>

                                        </tr>

                                <?php

                                    }

                                } else {

                                ?>

                                    <tr>

                                        <td
                                            colspan="9"
                                            class="text-center text-muted py-4">

                                            No appointments found.

                                        </td>

                                    </tr>

                                <?php

                                }

                                $stmt->close();

                                ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<?php

require_once "../../includes/footer.php";

?>