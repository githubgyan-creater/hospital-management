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


/* Get consultation records */

$query = "
    SELECT
        consultations.*,

        appointments.appointment_date,
        appointments.appointment_time,
        appointments.status AS appointment_status,

        patients.patient_code,
        patients.name AS patient_name,

        doctors.name AS doctor_name

    FROM consultations

    LEFT JOIN appointments
        ON consultations.appointment_id = appointments.id

    LEFT JOIN patients
        ON appointments.patient_id = patients.id

    LEFT JOIN doctors
        ON appointments.doctor_id = doctors.id

    ORDER BY consultations.id DESC
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

                        <h2>Consultation Reports</h2>

                        <p class="text-muted">
                            View consultation notes and clinical information
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
                            Consultation Report
                        </strong>

                    </div>


                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover">

                                <thead class="table-primary">

                                    <tr>

                                        <th>Consultation ID</th>

                                        <th>Patient</th>

                                        <th>Doctor</th>

                                        <th>Appointment Date</th>

                                        <th>Symptoms</th>

                                        <th>Diagnosis</th>

                                        <th>Advice</th>

                                        <th>Follow-up Date</th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php if ($result && $result->num_rows > 0): ?>

                                        <?php while ($consultation = $result->fetch_assoc()): ?>

                                            <tr>

                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $consultation["id"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>

                                                    <?php

                                                    echo htmlspecialchars(
                                                        ($consultation["patient_code"] ?? "-")
                                                        . " - "
                                                        . ($consultation["patient_name"] ?? "-")
                                                    );

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo htmlspecialchars(
                                                        $consultation["doctor_name"] ?? "-"
                                                    );

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo htmlspecialchars(
                                                        $consultation["appointment_date"] ?? "-"
                                                    );

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo nl2br(
                                                        htmlspecialchars(
                                                            $consultation["symptoms"] ?? "-"
                                                        )
                                                    );

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo nl2br(
                                                        htmlspecialchars(
                                                            $consultation["diagnosis"] ?? "-"
                                                        )
                                                    );

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo nl2br(
                                                        htmlspecialchars(
                                                            $consultation["advice"] ?? "-"
                                                        )
                                                    );

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo htmlspecialchars(
                                                        $consultation["follow_up_date"] ?? "-"
                                                    );

                                                    ?>

                                                </td>

                                            </tr>

                                        <?php endwhile; ?>

                                    <?php else: ?>

                                        <tr>

                                            <td
                                                colspan="8"
                                                class="text-center"
                                            >
                                                No consultation records found.
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