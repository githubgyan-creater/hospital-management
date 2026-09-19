<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

//  Check Appointment ID


if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$appointmentId = (int) $_GET["id"];

 
// Get Appointment Details


$sql = "SELECT
            appointments.*,
            patients.patient_code,
            patients.name AS patient_name,
            patients.phone AS patient_phone,
            doctors.name AS doctor_name,
            doctors.specialization,
            departments.name AS department_name
        FROM appointments

        INNER JOIN patients
            ON appointments.patient_id = patients.id

        INNER JOIN doctors
            ON appointments.doctor_id = doctors.id

        INNER JOIN departments
            ON doctors.department_id = departments.id

        WHERE appointments.id = ?

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
                        Appointment Details
                    </h2>

                    <p class="text-muted mb-0">
                        View appointment information
                    </p>

                </div>

                <a href="index.php" class="btn btn-secondary">
                    Back to Appointments
                </a>

            </div>


            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="row">

                        <!-- Appointment ID -->

                        <div class="col-md-6 mb-3">

                            <strong>Appointment ID</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $appointment["id"]
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Patient ID -->

                        <div class="col-md-6 mb-3">

                            <strong>Patient ID</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $appointment["patient_code"]
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Patient Name -->

                        <div class="col-md-6 mb-3">

                            <strong>Patient Name</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $appointment["patient_name"]
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Patient Phone -->

                        <div class="col-md-6 mb-3">

                            <strong>Patient Phone</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $appointment["patient_phone"]
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Doctor -->

                        <div class="col-md-6 mb-3">

                            <strong>Doctor</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $appointment["doctor_name"]
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Specialization -->

                        <div class="col-md-6 mb-3">

                            <strong>Specialization</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $appointment["specialization"]
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Department -->

                        <div class="col-md-6 mb-3">

                            <strong>Department</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $appointment["department_name"]
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Appointment Date -->

                        <div class="col-md-6 mb-3">

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


                        <!-- Appointment Time -->

                        <div class="col-md-6 mb-3">

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


                        <!-- Status -->

                        <div class="col-md-6 mb-3">

                            <strong>Status</strong>

                            <div>

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

                            </div>

                        </div>


                        <!-- Reason -->

                        <div class="col-md-12 mb-3">

                            <strong>Reason</strong>

                            <div>
                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $appointment["reason"]
                                        ?: "Not provided"
                                    )
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Notes -->

                        <div class="col-md-12 mb-3">

                            <strong>Notes</strong>

                            <div>
                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $appointment["notes"]
                                        ?: "Not provided"
                                    )
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Created Date -->

                        <div class="col-md-6 mb-3">

                            <strong>Created Date</strong>

                            <div>
                                <?php
                                echo date(
                                    "d-m-Y h:i A",
                                    strtotime(
                                        $appointment["created_at"]
                                    )
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Updated Date -->

                        <div class="col-md-6 mb-3">

                            <strong>Last Updated</strong>

                            <div>
                                <?php
                                echo date(
                                    "d-m-Y h:i A",
                                    strtotime(
                                        $appointment["updated_at"]
                                    )
                                );
                                ?>
                            </div>

                        </div>

                    </div>


                    <div class="mt-3">

                        <a
                            href="edit.php?id=<?php echo $appointment["id"]; ?>"
                            class="btn btn-warning">
                            Edit Appointment
                        </a>

                        <a
                            href="index.php"
                            class="btn btn-secondary">
                            Back
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once "../../includes/footer.php";

?>