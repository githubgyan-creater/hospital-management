 <?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "doctor") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";
require_once "../../includes/header.php";


/*
|--------------------------------------------------------------------------
| Find Doctor Profile
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

    echo "<div class='alert alert-danger m-4'>
            Doctor profile not found.
          </div>";

    exit;
}

$doctor = $doctorResult->fetch_assoc();

$doctorId = (int) $doctor["id"];

$doctorStmt->close();

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
                        My Appointments
                    </h2>

                    <p class="text-muted mb-0">
                        View your assigned patient appointments
                    </p>

                </div>

                <a
                    href="../dashboard.php"
                    class="btn btn-secondary">
                    Back to Dashboard
                </a>

            </div>


            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-primary">

                                <tr>
                                    <th>Appointment ID</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Phone</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php

                                $sql = "SELECT
                                            appointments.*,
                                            patients.patient_code,
                                            patients.name AS patient_name,
                                            patients.phone AS patient_phone,
                                            departments.name AS department_name
                                        FROM appointments

                                        INNER JOIN patients
                                            ON appointments.patient_id = patients.id

                                        INNER JOIN doctors
                                            ON appointments.doctor_id = doctors.id

                                        INNER JOIN departments
                                            ON doctors.department_id = departments.id

                                        WHERE appointments.doctor_id = ?

                                        ORDER BY
                                            appointments.appointment_date ASC,
                                            appointments.appointment_time ASC";

                                $stmt = $conn->prepare($sql);

                                $stmt->bind_param(
                                    "i",
                                    $doctorId
                                );

                                $stmt->execute();

                                $result = $stmt->get_result();


                                if ($result->num_rows > 0) {

                                    while ($appointment = $result->fetch_assoc()) {

                                ?>

                                        <tr>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $appointment["id"]
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $appointment["patient_code"]
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $appointment["patient_name"]
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $appointment["patient_phone"]
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $appointment["department_name"]
                                                );
                                                ?>
                                            </td>

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

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $appointment["reason"]
                                                    ?: "Not provided"
                                                );
                                                ?>
                                            </td>

                                            <td>

                                                <?php

                                                if (
                                                    $appointment["status"]
                                                    === "Pending"
                                                ) {

                                                    echo '<span class="badge bg-warning text-dark">Pending</span>';

                                                } elseif (
                                                    $appointment["status"]
                                                    === "Confirmed"
                                                ) {

                                                    echo '<span class="badge bg-primary">Confirmed</span>';

                                                } elseif (
                                                    $appointment["status"]
                                                    === "Completed"
                                                ) {

                                                    echo '<span class="badge bg-success">Completed</span>';

                                                } elseif (
                                                    $appointment["status"]
                                                    === "Cancelled"
                                                ) {

                                                    echo '<span class="badge bg-danger">Cancelled</span>';

                                                }

                                                ?>

                                            </td>

                                            <td>

                                                <a
                                                    href="view.php?id=<?php echo $appointment["id"]; ?>"
                                                    class="btn btn-sm btn-info">
                                                    View
                                                </a>

                                            </td>

                                        </tr>

                                <?php

                                    }

                                } else {

                                ?>

                                    <tr>

                                        <td
                                            colspan="10"
                                            class="text-center text-muted py-4">

                                            No appointments assigned to you.

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