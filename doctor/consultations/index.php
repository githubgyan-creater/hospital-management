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
| Get Logged-in Doctor
|--------------------------------------------------------------------------
*/

$userEmail = $_SESSION["user_email"];

$doctorSql = "SELECT id, name, email
              FROM doctors
              WHERE email = ?
              LIMIT 1";

$doctorStmt = $conn->prepare($doctorSql);
$doctorStmt->bind_param("s", $userEmail);
$doctorStmt->execute();

$doctorResult = $doctorStmt->get_result();

if ($doctorResult->num_rows !== 1) {
    die("Doctor profile not found.");
}

$doctor = $doctorResult->fetch_assoc();

$doctorId = (int) $doctor["id"];

$doctorStmt->close();


/*
|--------------------------------------------------------------------------
| Get Doctor's Consultations
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            consultations.id,
            consultations.appointment_id,
            consultations.symptoms,
            consultations.diagnosis,
            consultations.consultation_notes,
            consultations.advice,
            consultations.follow_up_date,
            consultations.created_at,

            patients.patient_code,
            patients.name AS patient_name,

            appointments.appointment_date,
            appointments.appointment_time

        FROM consultations

        INNER JOIN appointments
            ON consultations.appointment_id = appointments.id

        INNER JOIN patients
            ON appointments.patient_id = patients.id

        WHERE consultations.doctor_id = ?

        ORDER BY consultations.id DESC";


$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $doctorId);

$stmt->execute();

$result = $stmt->get_result();

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
                        My Consultations
                    </h2>

                    <p class="text-muted mb-0">
                        View consultations created by you
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

                                    <th>
                                        Consultation ID
                                    </th>

                                    <th>
                                        Appointment ID
                                    </th>

                                    <th>
                                        Patient ID
                                    </th>

                                    <th>
                                        Patient Name
                                    </th>

                                    <th>
                                        Diagnosis
                                    </th>

                                    <th>
                                        Appointment Date
                                    </th>

                                    <th>
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php if ($result->num_rows > 0): ?>

                                    <?php while ($row = $result->fetch_assoc()): ?>

                                        <tr>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $row["id"]
                                                );
                                                ?>
                                            </td>


                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $row["appointment_id"]
                                                );
                                                ?>
                                            </td>


                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $row["patient_code"]
                                                );
                                                ?>
                                            </td>


                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $row["patient_name"]
                                                );
                                                ?>
                                            </td>


                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $row["diagnosis"]
                                                );
                                                ?>
                                            </td>


                                            <td>
                                                <?php
                                                echo date(
                                                    "d-m-Y",
                                                    strtotime(
                                                        $row["appointment_date"]
                                                    )
                                                );
                                                ?>
                                            </td>


                                            <td>

                                                 <a href="view.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-info text-white">
                                                     View
                                                     </a>

                                                  <a href="edit.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-warning">
                                                        Edit
                                                  </a>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td
                                            colspan="7"
                                            class="text-center text-muted py-4">

                                            No consultations found.

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


<?php

$stmt->close();

require_once "../../includes/footer.php";

?>