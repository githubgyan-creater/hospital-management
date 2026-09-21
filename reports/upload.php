<?php

require_once "../includes/auth.php";

if (
    $_SESSION["user_role"] !== "receptionist" &&
    $_SESSION["user_role"] !== "admin"
) {
    header("Location: ../index.php");
    exit;
}

require_once "../config/database.php";

$report_id = (int)($_GET["id"] ?? 0);

if ($report_id <= 0) {
    die("Invalid report ID.");
}

$message = "";
$message_type = "";


/*
|--------------------------------------------------------------------------
| Get Report Details
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        mr.id,
        mr.patient_id,
        mr.doctor_id,
        mr.appointment_id,
        mr.token_id,
        mr.report_type,
        mr.report_title,
        mr.report_description,
        mr.report_file,
        mr.status,

        p.patient_code,
        p.name AS patient_name,

        d.name AS doctor_name

    FROM medical_reports mr

    INNER JOIN patients p
        ON mr.patient_id = p.id

    LEFT JOIN doctors d
        ON mr.doctor_id = d.id

    WHERE mr.id = ?

    LIMIT 1
");

$stmt->bind_param("i", $report_id);

$stmt->execute();

$report = $stmt->get_result()->fetch_assoc();

$stmt->close();


if (!$report) {
    die("Medical report not found.");
}


/*
|--------------------------------------------------------------------------
| Upload Report
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_FILES["report_file"])) {

        $message = "Please select a report file.";
        $message_type = "danger";

    } else {

        $file = $_FILES["report_file"];


        /*
        |--------------------------------------------------------------------------
        | Check Upload Error
        |--------------------------------------------------------------------------
        */

        if ($file["error"] !== UPLOAD_ERR_OK) {

            $message = "File upload failed.";
            $message_type = "danger";

        } else {

            /*
            |--------------------------------------------------------------------------
            | Allowed File Types
            |--------------------------------------------------------------------------
            */

            $allowed_extensions = [
                "pdf",
                "jpg",
                "jpeg",
                "png"
            ];

            $original_name = $file["name"];

            $extension = strtolower(
                pathinfo($original_name, PATHINFO_EXTENSION)
            );


            /*
            |--------------------------------------------------------------------------
            | Check Extension
            |--------------------------------------------------------------------------
            */

            if (!in_array($extension, $allowed_extensions, true)) {

                $message = "Only PDF, JPG, JPEG and PNG files are allowed.";
                $message_type = "danger";

            } elseif ($file["size"] > 5 * 1024 * 1024) {

                /*
                |--------------------------------------------------------------------------
                | Maximum 5 MB
                |--------------------------------------------------------------------------
                */

                $message = "File size must not exceed 5 MB.";
                $message_type = "danger";

            } else {

                /*
                |--------------------------------------------------------------------------
                | Create Upload Folder
                |--------------------------------------------------------------------------
                */

                $upload_directory = __DIR__ . "/uploads/";

                if (!is_dir($upload_directory)) {

                    mkdir(
                        $upload_directory,
                        0755,
                        true
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Generate Unique File Name
                |--------------------------------------------------------------------------
                */

                $new_file_name =
                    "report_" .
                    $report_id .
                    "_" .
                    time() .
                    "." .
                    $extension;


                $destination =
                    $upload_directory .
                    $new_file_name;


                /*
                |--------------------------------------------------------------------------
                | Move Uploaded File
                |--------------------------------------------------------------------------
                */

                if (move_uploaded_file(
                    $file["tmp_name"],
                    $destination
                )) {


                    /*
                    |--------------------------------------------------------------------------
                    | Update Medical Report
                    |--------------------------------------------------------------------------
                    */

                    $stmt = $conn->prepare("
                        UPDATE medical_reports
                        SET
                            report_file = ?,
                            status = 'UPLOADED',
                            uploaded_at = NOW()
                        WHERE id = ?
                    ");

                    $stmt->bind_param(
                        "si",
                        $new_file_name,
                        $report_id
                    );


                    if ($stmt->execute()) {

                        $stmt->close();


                        /*
                        |--------------------------------------------------------------------------
                        | Update Token Status
                        |--------------------------------------------------------------------------
                        */

                        if (!empty($report["token_id"])) {

                            $token_id = (int)$report["token_id"];

                            $stmt = $conn->prepare("
                                UPDATE tokens
                                SET status = 'REPORT UPLOADED'
                                WHERE id = ?
                            ");

                            $stmt->bind_param(
                                "i",
                                $token_id
                            );

                            $stmt->execute();

                            $stmt->close();
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Success
                        |--------------------------------------------------------------------------
                        */

                        header(
                            "Location: index.php?uploaded=1"
                        );

                        exit;

                    } else {

                        $stmt->close();

                        /*
                         * Remove uploaded file if database update failed.
                         */

                        if (file_exists($destination)) {
                            unlink($destination);
                        }

                        $message =
                            "File uploaded but database update failed.";

                        $message_type = "danger";
                    }

                } else {

                    $message =
                        "Unable to save the uploaded file.";

                    $message_type = "danger";
                }
            }
        }
    }
}

?>

<?php require_once "../includes/header.php"; ?>


<div class="container-fluid">

    <div class="row">


        <!-- Sidebar -->

        <div class="col-md-3 col-lg-2 p-0">

            <?php require_once "../includes/sidebar.php"; ?>

        </div>


        <!-- Main Content -->

        <div class="col-md-9 col-lg-10 p-4">


            <!-- Page Header -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Upload Medical Report
                    </h2>

                    <p class="text-muted mb-0">
                        Upload the patient's completed medical report.
                    </p>

                </div>


                <a
                    href="index.php"
                    class="btn btn-secondary"
                >
                    Back to Reports
                </a>

            </div>


            <!-- Message -->

            <?php if ($message !== ""): ?>

                <div class="alert alert-<?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <!-- Report Information -->

            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="fw-bold mb-0">
                        Report Information
                    </h5>

                </div>


                <div class="card-body">

                    <div class="row">


                        <div class="col-md-4 mb-3">

                            <strong>
                                Patient
                            </strong>

                            <div>

                                <?php echo htmlspecialchars(
                                    $report["patient_name"]
                                ); ?>

                            </div>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>
                                Patient Code
                            </strong>

                            <div>

                                <?php echo htmlspecialchars(
                                    $report["patient_code"]
                                ); ?>

                            </div>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>
                                Doctor
                            </strong>

                            <div>

                                <?php

                                echo $report["doctor_name"]
                                    ? htmlspecialchars($report["doctor_name"])
                                    : "Not Assigned";

                                ?>

                            </div>

                        </div>


                        <div class="col-md-4 mb-3">

                            <strong>
                                Report Type
                            </strong>

                            <div>

                                <?php echo htmlspecialchars(
                                    $report["report_type"]
                                ); ?>

                            </div>

                        </div>


                        <div class="col-md-8 mb-3">

                            <strong>
                                Report Title
                            </strong>

                            <div>

                                <?php echo htmlspecialchars(
                                    $report["report_title"]
                                ); ?>

                            </div>

                        </div>


                        <div class="col-12">

                            <strong>
                                Description
                            </strong>

                            <div class="text-muted">

                                <?php

                                echo !empty($report["report_description"])
                                    ? nl2br(
                                        htmlspecialchars(
                                            $report["report_description"]
                                        )
                                    )
                                    : "No additional instructions.";

                                ?>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Upload Form -->

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="fw-bold mb-0">
                        Upload Report File
                    </h5>

                </div>


                <div class="card-body">

                    <?php if ($report["status"] === "UPLOADED"): ?>

                        <div class="alert alert-success">

                            This report has already been uploaded.

                        </div>


                        <?php if (!empty($report["report_file"])): ?>

                            <a
                                href="uploads/<?php echo rawurlencode($report["report_file"]); ?>"
                                target="_blank"
                                class="btn btn-success"
                            >
                                View Uploaded Report
                            </a>

                        <?php endif; ?>


                    <?php else: ?>


                        <form
                            method="POST"
                            enctype="multipart/form-data"
                        >


                            <div class="mb-4">

                                <label class="form-label fw-bold">

                                    Select Report File

                                </label>

                                <input
                                    type="file"
                                    name="report_file"
                                    class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                >

                                <div class="form-text">

                                    Allowed formats: PDF, JPG, JPEG, PNG.
                                    Maximum size: 5 MB.

                                </div>

                            </div>


                            <div class="d-flex gap-2">

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    Upload Report
                                </button>


                                <a
                                    href="index.php"
                                    class="btn btn-secondary"
                                >
                                    Cancel
                                </a>

                            </div>


                        </form>


                    <?php endif; ?>

                </div>

            </div>


        </div>

    </div>

</div>


<?php require_once "../includes/footer.php"; ?>