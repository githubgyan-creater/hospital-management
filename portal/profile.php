<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["portal_patient_id"])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION["portal_patient_id"];

$stmt = $conn->prepare(
    "SELECT
        patient_code,
        name,
        date_of_birth,
        age,
        gender,
        phone,
        email,
        address,
        blood_group,
        emergency_contact,
        medical_history
     FROM patients
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();

    session_destroy();

    header("Location: login.php");
    exit();
}

$patient = $result->fetch_assoc();

$stmt->close();

require_once "../includes/header.php";

?>

<div class="container py-4">

    <!-- PAGE HEADER -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                My Profile
            </h2>

            <p class="text-muted mb-0">
                View your registered patient information.
            </p>
        </div>

        <a
            href="dashboard.php"
            class="btn btn-secondary"
        >
            Back to Dashboard
        </a>

    </div>


    <!-- PATIENT INFORMATION -->

    <div class="card shadow-sm border-0">

        <div class="card-body p-4">

            <div class="row">


                <!-- PATIENT ID -->

                <div class="col-md-6 mb-4">

                    <label class="fw-bold">
                        Patient ID
                    </label>

                    <div class="form-control bg-light">
                        <?php
                        echo htmlspecialchars(
                            $patient["patient_code"]
                        );
                        ?>
                    </div>

                </div>


                <!-- FULL NAME -->

                <div class="col-md-6 mb-4">

                    <label class="fw-bold">
                        Full Name
                    </label>

                    <div class="form-control bg-light">
                        <?php
                        echo htmlspecialchars(
                            $patient["name"]
                        );
                        ?>
                    </div>

                </div>


                <!-- DATE OF BIRTH -->

                <div class="col-md-6 mb-4">

                    <label class="fw-bold">
                        Date of Birth
                    </label>

                    <div class="form-control bg-light">

                        <?php

                        echo !empty($patient["date_of_birth"])
                            ? htmlspecialchars(
                                $patient["date_of_birth"]
                            )
                            : "Not provided";

                        ?>

                    </div>

                </div>


                <!-- AGE -->

                <div class="col-md-6 mb-4">

                    <label class="fw-bold">
                        Age
                    </label>

                    <div class="form-control bg-light">

                        <?php

                        if (!empty($patient["age"])) {

                            echo htmlspecialchars(
                                $patient["age"]
                            );

                        } elseif (
                            !empty($patient["date_of_birth"])
                        ) {

                            $dob = new DateTime(
                                $patient["date_of_birth"]
                            );

                            $today = new DateTime();

                            echo $today->diff($dob)->y;

                        } else {

                            echo "Not provided";

                        }

                        ?>

                    </div>

                </div>


                <!-- GENDER -->

                <div class="col-md-6 mb-4">

                    <label class="fw-bold">
                        Gender
                    </label>

                    <div class="form-control bg-light">

                        <?php
                        echo htmlspecialchars(
                            $patient["gender"]
                        );
                        ?>

                    </div>

                </div>


                <!-- PHONE -->

                <div class="col-md-6 mb-4">

                    <label class="fw-bold">
                        Phone Number
                    </label>

                    <div class="form-control bg-light">

                        <?php
                        echo htmlspecialchars(
                            $patient["phone"]
                        );
                        ?>

                    </div>

                </div>


                <!-- EMAIL -->

                <div class="col-md-6 mb-4">

                    <label class="fw-bold">
                        Email
                    </label>

                    <div class="form-control bg-light">

                        <?php

                        echo !empty($patient["email"])
                            ? htmlspecialchars(
                                $patient["email"]
                            )
                            : "Not provided";

                        ?>

                    </div>

                </div>


                <!-- BLOOD GROUP -->

                <div class="col-md-6 mb-4">

                    <label class="fw-bold">
                        Blood Group
                    </label>

                    <div class="form-control bg-light">

                        <?php

                        echo !empty($patient["blood_group"])
                            ? htmlspecialchars(
                                $patient["blood_group"]
                            )
                            : "Not provided";

                        ?>

                    </div>

                </div>


                <!-- EMERGENCY CONTACT -->

                <div class="col-md-6 mb-4">

                    <label class="fw-bold">
                        Emergency Contact
                    </label>

                    <div class="form-control bg-light">

                        <?php

                        echo !empty(
                            $patient["emergency_contact"]
                        )
                            ? htmlspecialchars(
                                $patient["emergency_contact"]
                            )
                            : "Not provided";

                        ?>

                    </div>

                </div>


                <!-- ADDRESS -->

                <div class="col-12 mb-4">

                    <label class="fw-bold">
                        Address
                    </label>

                    <div class="form-control bg-light">

                        <?php

                        echo !empty($patient["address"])
                            ? nl2br(
                                htmlspecialchars(
                                    $patient["address"]
                                )
                            )
                            : "Not provided";

                        ?>

                    </div>

                </div>


                <!-- MEDICAL HISTORY -->

                <div class="col-12 mb-2">

                    <label class="fw-bold">
                        Medical History
                    </label>

                    <div
                        class="form-control bg-light"
                        style="min-height: 100px;"
                    >

                        <?php

                        echo !empty(
                            $patient["medical_history"]
                        )
                            ? nl2br(
                                htmlspecialchars(
                                    $patient["medical_history"]
                                )
                            )
                            : "No medical history provided.";

                        ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- NAVIGATION -->

    <div class="mt-4">

        <a
            href="dashboard.php"
            class="btn btn-primary"
        >
            ← Dashboard
        </a>

    </div>

</div>

<?php

require_once "../includes/footer.php";

?>