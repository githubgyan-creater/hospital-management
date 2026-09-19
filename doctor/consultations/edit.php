<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "doctor") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";


/*
|--------------------------------------------------------------------------
| Get Logged-in Doctor
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
| Get Consultation
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            id,
            appointment_id,
            symptoms,
            diagnosis,
            consultation_notes,
            advice,
            follow_up_date

        FROM consultations

        WHERE id = ?
        AND doctor_id = ?

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

    die("Consultation not found or you are not authorized to edit it.");
}

$consultation = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Update Consultation
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $symptoms = trim($_POST["symptoms"]);
    $diagnosis = trim($_POST["diagnosis"]);
    $consultation_notes = trim($_POST["consultation_notes"]);
    $advice = trim($_POST["advice"]);

    $follow_up_date = !empty($_POST["follow_up_date"])
        ? $_POST["follow_up_date"]
        : null;


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        $symptoms === "" ||
        $diagnosis === "" ||
        $consultation_notes === ""
    ) {

        $error = "Please fill all required fields.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Update Database
        |--------------------------------------------------------------------------
        */

        $updateSql = "UPDATE consultations

                      SET
                          symptoms = ?,
                          diagnosis = ?,
                          consultation_notes = ?,
                          advice = ?,
                          follow_up_date = ?

                      WHERE id = ?
                      AND doctor_id = ?";

        $updateStmt = $conn->prepare($updateSql);

        $updateStmt->bind_param(
            "sssssii",
            $symptoms,
            $diagnosis,
            $consultation_notes,
            $advice,
            $follow_up_date,
            $consultationId,
            $doctorId
        );


        if ($updateStmt->execute()) {

            $appointmentId = (int) $consultation["appointment_id"];

            $updateStmt->close();

            header(
                "Location: view.php?id=" .
                $consultationId .
                "&success=Consultation updated successfully"
            );

            exit;

        } else {

            $error = "Consultation could not be updated.";

        }

        $updateStmt->close();
    }
}


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
                        Edit Consultation
                    </h2>

                    <p class="text-muted mb-0">
                        Update consultation information
                    </p>

                </div>


                <a
                    href="view.php?id=<?php echo $consultationId; ?>"
                    class="btn btn-secondary">

                    Back to Consultation

                </a>

            </div>


            <?php if (isset($error)): ?>

                <div class="alert alert-danger">

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>

            <?php endif; ?>


            <!-- Consultation Form -->

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="fw-bold mb-0">
                        Consultation Information
                    </h5>

                </div>


                <div class="card-body">

                    <form method="POST">


                        <!-- Symptoms -->

                        <div class="mb-3">

                            <label class="form-label">
                                Symptoms *
                            </label>

                            <textarea
                                name="symptoms"
                                class="form-control"
                                rows="4"
                                required><?php

                                echo htmlspecialchars(
                                    isset($_POST["symptoms"])
                                        ? $_POST["symptoms"]
                                        : $consultation["symptoms"]
                                );

                                ?></textarea>

                        </div>


                        <!-- Diagnosis -->

                        <div class="mb-3">

                            <label class="form-label">
                                Diagnosis *
                            </label>

                            <textarea
                                name="diagnosis"
                                class="form-control"
                                rows="4"
                                required><?php

                                echo htmlspecialchars(
                                    isset($_POST["diagnosis"])
                                        ? $_POST["diagnosis"]
                                        : $consultation["diagnosis"]
                                );

                                ?></textarea>

                        </div>


                        <!-- Consultation Notes -->

                        <div class="mb-3">

                            <label class="form-label">
                                Consultation Notes *
                            </label>

                            <textarea
                                name="consultation_notes"
                                class="form-control"
                                rows="5"
                                required><?php

                                echo htmlspecialchars(
                                    isset($_POST["consultation_notes"])
                                        ? $_POST["consultation_notes"]
                                        : $consultation["consultation_notes"]
                                );

                                ?></textarea>

                        </div>


                        <!-- Advice -->

                        <div class="mb-3">

                            <label class="form-label">
                                Advice
                            </label>

                            <textarea
                                name="advice"
                                class="form-control"
                                rows="4"><?php

                                echo htmlspecialchars(
                                    isset($_POST["advice"])
                                        ? $_POST["advice"]
                                        : $consultation["advice"]
                                );

                                ?></textarea>

                        </div>


                        <!-- Follow-up Date -->

                        <div class="mb-4">

                            <label class="form-label">
                                Follow-up Date
                            </label>

                            <input
                                type="date"
                                name="follow_up_date"
                                class="form-control"

                                value="<?php

                                echo htmlspecialchars(
                                    isset($_POST["follow_up_date"])
                                        ? $_POST["follow_up_date"]
                                        : $consultation["follow_up_date"]
                                );

                                ?>">

                        </div>


                        <!-- Buttons -->

                        <button
                            type="submit"
                            class="btn btn-primary">

                            Update Consultation

                        </button>


                        <a
                            href="view.php?id=<?php echo $consultationId; ?>"
                            class="btn btn-secondary">

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