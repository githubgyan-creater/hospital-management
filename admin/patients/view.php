 <?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {

    header("Location: ../dashboard.php");
    exit;

}

require_once "../../config/database.php";


/* =========================
   CHECK PATIENT ID
========================= */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: index.php");
    exit;

}

$patientId = (int) $_GET["id"];


/* =========================
   GET PATIENT
========================= */

$sql = "SELECT *
        FROM patients
        WHERE id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $patientId);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: index.php");
    exit;

}


$patient = $result->fetch_assoc();

$stmt->close();


/* =========================
   AGE
========================= */

$displayAge = $patient["age"];

if (
    empty($displayAge)
    && !empty($patient["date_of_birth"])
) {

    $dob = new DateTime($patient["date_of_birth"]);
    $today = new DateTime();

    $displayAge = $today->diff($dob)->y;

}


/* =========================
   HEADER
========================= */

require_once "../../includes/header.php";

?>


<div class="container-fluid">

    <div class="row">


        <!-- =========================
             SIDEBAR
        ========================== -->

        <div class="col-md-3 col-lg-2 p-0">

            <?php require_once "../../includes/sidebar.php"; ?>

        </div>


        <!-- =========================
             MAIN CONTENT
        ========================== -->

        <div class="col-md-9 col-lg-10 p-4">


            <!-- Page Heading -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Patient Details
                    </h2>

                    <p class="text-muted mb-0">
                        View patient information
                    </p>

                </div>


                <a
                    href="index.php"
                    class="btn btn-secondary">

                    Back to Patients

                </a>

            </div>


            <!-- =========================
                 PATIENT INFORMATION CARD
            ========================== -->

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">


                    <!-- Patient Header -->

                    <div class="mb-4">

                        <h4 class="fw-bold mb-1">
                            <?php echo htmlspecialchars($patient["name"]); ?>
                        </h4>

                        <span class="badge bg-primary">
                            <?php echo htmlspecialchars($patient["patient_code"]); ?>
                        </span>

                    </div>


                    <hr>


                    <!-- =========================
                         BASIC INFORMATION
                    ========================== -->

                    <h5 class="fw-bold mb-3">
                        Basic Information
                    </h5>


                    <div class="row g-3">


                        <!-- Patient ID -->

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <div class="text-muted small mb-1">
                                    Patient ID
                                </div>

                                <div class="fw-semibold">
                                    <?php
                                    echo htmlspecialchars(
                                        $patient["patient_code"]
                                    );
                                    ?>
                                </div>

                            </div>

                        </div>


                        <!-- Patient Name -->

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <div class="text-muted small mb-1">
                                    Patient Name
                                </div>

                                <div class="fw-semibold">
                                    <?php
                                    echo htmlspecialchars(
                                        $patient["name"]
                                    );
                                    ?>
                                </div>

                            </div>

                        </div>


                        <!-- Age -->

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <div class="text-muted small mb-1">
                                    Age
                                </div>

                                <div class="fw-semibold">

                                    <?php

                                    echo !empty($displayAge)
                                        ? htmlspecialchars($displayAge) . " years"
                                        : "Not provided";

                                    ?>

                                </div>

                            </div>

                        </div>


                        <!-- Gender -->

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <div class="text-muted small mb-1">
                                    Gender
                                </div>

                                <div class="fw-semibold">

                                    <?php
                                    echo !empty($patient["gender"])
                                        ? htmlspecialchars($patient["gender"])
                                        : "Not provided";
                                    ?>

                                </div>

                            </div>

                        </div>


                        <!-- Date of Birth -->

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <div class="text-muted small mb-1">
                                    Date of Birth
                                </div>

                                <div class="fw-semibold">

                                    <?php

                                    echo !empty($patient["date_of_birth"])
                                        ? htmlspecialchars(
                                            $patient["date_of_birth"]
                                        )
                                        : "Not provided";

                                    ?>

                                </div>

                            </div>

                        </div>


                        <!-- Blood Group -->

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <div class="text-muted small mb-1">
                                    Blood Group
                                </div>

                                <div class="fw-semibold">

                                    <?php

                                    echo !empty($patient["blood_group"])
                                        ? htmlspecialchars(
                                            $patient["blood_group"]
                                        )
                                        : "Not provided";

                                    ?>

                                </div>

                            </div>

                        </div>


                        <!-- Phone -->

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <div class="text-muted small mb-1">
                                    Phone
                                </div>

                                <div class="fw-semibold">

                                    <?php
                                    echo !empty($patient["phone"])
                                        ? htmlspecialchars($patient["phone"])
                                        : "Not provided";
                                    ?>

                                </div>

                            </div>

                        </div>


                        <!-- Email -->

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <div class="text-muted small mb-1">
                                    Email
                                </div>

                                <div class="fw-semibold">

                                    <?php

                                    echo !empty($patient["email"])
                                        ? htmlspecialchars($patient["email"])
                                        : "Not provided";

                                    ?>

                                </div>

                            </div>

                        </div>


                        <!-- Emergency Contact -->

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <div class="text-muted small mb-1">
                                    Emergency Contact
                                </div>

                                <div class="fw-semibold">

                                    <?php

                                    echo !empty($patient["emergency_contact"])
                                        ? htmlspecialchars(
                                            $patient["emergency_contact"]
                                        )
                                        : "Not provided";

                                    ?>

                                </div>

                            </div>

                        </div>


                        <!-- Registration Date -->

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <div class="text-muted small mb-1">
                                    Registration Date
                                </div>

                                <div class="fw-semibold">

                                    <?php

                                    echo !empty($patient["created_at"])
                                        ? date(
                                            "d-m-Y",
                                            strtotime(
                                                $patient["created_at"]
                                            )
                                        )
                                        : "Not available";

                                    ?>

                                </div>

                            </div>

                        </div>


                        <!-- Last Updated -->

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <div class="text-muted small mb-1">
                                    Last Updated
                                </div>

                                <div class="fw-semibold">

                                    <?php

                                    echo !empty($patient["updated_at"])
                                        ? date(
                                            "d-m-Y",
                                            strtotime(
                                                $patient["updated_at"]
                                            )
                                        )
                                        : "Not available";

                                    ?>

                                </div>

                            </div>

                        </div>


                    </div>


                    <!-- =========================
                         ADDRESS
                    ========================== -->

                    <h5 class="fw-bold mt-4 mb-3">
                        Address
                    </h5>


                    <div class="border rounded p-3">

                        <div class="text-muted small mb-1">
                            Patient Address
                        </div>

                        <div class="fw-semibold">

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


                    <!-- =========================
                         MEDICAL HISTORY
                    ========================== -->

                    <h5 class="fw-bold mt-4 mb-3">
                        Medical History
                    </h5>


                    <div class="border rounded p-3">

                        <div class="text-muted small mb-1">
                            Medical History
                        </div>

                        <div class="fw-semibold">

                            <?php

                            echo !empty($patient["medical_history"])
                                ? nl2br(
                                    htmlspecialchars(
                                        $patient["medical_history"]
                                    )
                                )
                                : "Not provided";

                            ?>

                        </div>

                    </div>


                    <!-- =========================
                         ACTION BUTTONS
                    ========================== -->

                    <div class="mt-4 pt-3 border-top">

                        <a
                            href="edit.php?id=<?php echo $patient["id"]; ?>"
                            class="btn btn-warning">

                            Edit Patient

                        </a>
                         <a
        href="reset_password.php?id=<?php echo $patient["id"]; ?>"
        class="btn btn-primary ms-2">

        Reset Portal Password

    </a>


                        <a
                            href="index.php"
                            class="btn btn-secondary ms-2">

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