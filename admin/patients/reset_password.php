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

$stmt = $conn->prepare(
    "SELECT id, patient_code, name
     FROM patients
     WHERE id = ?
     LIMIT 1"
);

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
   RESET PASSWORD
========================= */

$error = "";
$success = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";


    /* Password validation */

    if (empty($newPassword) || empty($confirmPassword)) {

        $error = "Please enter the new password and confirm password.";

    } elseif (strlen($newPassword) < 6) {

        $error = "Password must be at least 6 characters.";

    } elseif ($newPassword !== $confirmPassword) {

        $error = "Passwords do not match.";

    } else {

        /* Create secure password hash */

        $hashedPassword = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );


        /* Update patient password */

        $stmt = $conn->prepare(
            "UPDATE patients
             SET portal_password = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "si",
            $hashedPassword,
            $patientId
        );


        if ($stmt->execute()) {

            $success = "Patient portal password has been reset successfully.";

        } else {

            $error = "Unable to reset password. Please try again.";

        }

        $stmt->close();

    }

}


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


            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Reset Patient Password
                    </h2>

                    <p class="text-muted mb-0">
                        Reset the patient's Patient Portal password.
                    </p>

                </div>


                <a
                    href="view.php?id=<?php echo $patientId; ?>"
                    class="btn btn-secondary">

                    Back to Patient

                </a>

            </div>


            <!-- =========================
                 PASSWORD CARD
            ========================== -->

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">


                    <!-- Patient Information -->

                    <div class="mb-4">

                        <h5 class="fw-bold">
                            Patient Information
                        </h5>

                        <p class="mb-1">
                            <strong>Patient ID:</strong>

                            <?php
                            echo htmlspecialchars(
                                $patient["patient_code"]
                            );
                            ?>

                        </p>

                        <p class="mb-0">

                            <strong>Patient Name:</strong>

                            <?php
                            echo htmlspecialchars(
                                $patient["name"]
                            );
                            ?>

                        </p>

                    </div>


                    <hr>


                    <!-- Success Message -->

                    <?php if (!empty($success)): ?>

                        <div class="alert alert-success">

                            <?php echo htmlspecialchars($success); ?>

                        </div>

                    <?php endif; ?>


                    <!-- Error Message -->

                    <?php if (!empty($error)): ?>

                        <div class="alert alert-danger">

                            <?php echo htmlspecialchars($error); ?>

                        </div>

                    <?php endif; ?>


                    <!-- Password Form -->

                    <form method="POST">


                        <div class="mb-3">

                            <label
                                for="new_password"
                                class="form-label fw-semibold">

                                New Password

                            </label>

                            <input
                                type="password"
                                name="new_password"
                                id="new_password"
                                class="form-control"
                                minlength="6"
                                required>

                            <small class="text-muted">
                                Password must be at least 6 characters.
                            </small>

                        </div>


                        <div class="mb-4">

                            <label
                                for="confirm_password"
                                class="form-label fw-semibold">

                                Confirm New Password

                            </label>

                            <input
                                type="password"
                                name="confirm_password"
                                id="confirm_password"
                                class="form-control"
                                minlength="6"
                                required>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary">

                            Reset Password

                        </button>


                        <a
                            href="view.php?id=<?php echo $patientId; ?>"
                            class="btn btn-secondary ms-2">

                            Cancel

                        </a>


                    </form>


                </div>

            </div>


        </div>

    </div>

</div>


<?php

require_once "../../includes/footer.php";

?>