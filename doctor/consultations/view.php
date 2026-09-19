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
| Check Consultation ID
|--------------------------------------------------------------------------
*/

if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
) {
    header("Location: index.php");
    exit;
}

$consultationId = (int) $_GET["id"];


/*
|--------------------------------------------------------------------------
| Get Consultation Details
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
            patients.phone,
            patients.gender,
            patients.date_of_birth,

            appointments.appointment_date,
            appointments.appointment_time,
            appointments.reason,
            appointments.status

        FROM consultations

        INNER JOIN appointments
            ON consultations.appointment_id = appointments.id

        INNER JOIN patients
            ON appointments.patient_id = patients.id

        WHERE consultations.id = ?
        AND consultations.doctor_id = ?

        LIMIT 1";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $consultationId,
    $doctorId
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    $stmt->close();

    die("Consultation not found or you are not authorized to view it.");
}


$consultation = $result->fetch_assoc();

$stmt->close();

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
                        Consultation Details
                    </h2>

                    <p class="text-muted mb-0">
                        View consultation information
                    </p>

                </div>


                <a
                    href="index.php"
                    class="btn btn-secondary">
                    Back to Consultations
                </a>

            </div>


            <!-- Patient Information -->

            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="fw-bold mb-0">
                        Patient Information
                    </h5>

                </div>


                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <strong>Patient ID</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $consultation["patient_code"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Patient Name</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $consultation["patient_name"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Phone</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $consultation["phone"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Gender</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $consultation["gender"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Date of Birth</strong>

                            <p class="mb-0">

                                <?php

                                if (!empty(
                                    $consultation["date_of_birth"]
                                )) {

                                    echo date(
                                        "d-m-Y",
                                        strtotime(
                                            $consultation["date_of_birth"]
                                        )
                                    );

                                } else {

                                    echo "Not provided";

                                }

                                ?>

                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Appointment Information -->

            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="fw-bold mb-0">
                        Appointment Information
                    </h5>

                </div>


                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <strong>Appointment ID</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $consultation["appointment_id"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Appointment Date</strong>

                            <p class="mb-0">
                                <?php
                                echo date(
                                    "d-m-Y",
                                    strtotime(
                                        $consultation["appointment_date"]
                                    )
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Appointment Time</strong>

                            <p class="mb-0">
                                <?php
                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $consultation["appointment_time"]
                                    )
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>Status</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $consultation["status"]
                                );
                                ?>
                            </p>

                        </div>


                        <div class="col-md-8 mb-3">

                            <strong>Reason for Visit</strong>

                            <p class="mb-0">
                                <?php
                                echo htmlspecialchars(
                                    $consultation["reason"]
                                    ?: "Not provided"
                                );
                                ?>
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Consultation Information -->

            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="fw-bold mb-0">
                        Consultation Information
                    </h5>

                </div>


                <div class="card-body">

                    <div class="mb-4">

                        <strong>Symptoms</strong>

                        <div class="border rounded p-3 mt-2">

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $consultation["symptoms"]
                                )
                            );
                            ?>

                        </div>

                    </div>


                    <div class="mb-4">

                        <strong>Diagnosis</strong>

                        <div class="border rounded p-3 mt-2">

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $consultation["diagnosis"]
                                )
                            );
                            ?>

                        </div>

                    </div>


                    <div class="mb-4">

                        <strong>Consultation Notes</strong>

                        <div class="border rounded p-3 mt-2">

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $consultation["consultation_notes"]
                                )
                            );
                            ?>

                        </div>

                    </div>


                    <div class="mb-4">

                        <strong>Advice</strong>

                        <div class="border rounded p-3 mt-2">

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $consultation["advice"]
                                )
                            );
                            ?>

                        </div>

                    </div>


                    <div class="mb-3">

                        <strong>Follow-up Date</strong>

                        <p class="mt-2">

                            <?php

                            if (!empty(
                                $consultation["follow_up_date"]
                            )) {

                                echo date(
                                    "d-m-Y",
                                    strtotime(
                                        $consultation["follow_up_date"]
                                    )
                                );

                            } else {

                                echo "No follow-up date";

                            }

                            ?>

                        </p>

                    </div>

                </div>

            </div>


            <!-- Bottom Buttons -->

            <div>

                <a
                    href="index.php"
                    class="btn btn-secondary">
                    Back to Consultations
                </a>

                <a
                    href="../appointments/view.php?id=<?php echo $consultation["appointment_id"]; ?>"
                    class="btn btn-primary">
                    View Appointment
                </a>

            </div>

        </div>

    </div>

</div>


<?php

require_once "../../includes/footer.php";

?>