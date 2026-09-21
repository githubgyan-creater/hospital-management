<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "doctor") {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/database.php";
require_once "../../includes/header.php";


/*
|--------------------------------------------------------------------------
| Get Logged-in Doctor
|--------------------------------------------------------------------------
*/

$user_email = $_SESSION["user_email"] ?? "";

$stmt = $conn->prepare("
    SELECT id, name
    FROM doctors
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $user_email);
$stmt->execute();

$doctor = $stmt->get_result()->fetch_assoc();

$stmt->close();


if (!$doctor) {
    die("Doctor profile not found.");
}

$doctor_id = (int)$doctor["id"];


/*
|--------------------------------------------------------------------------
| Get Doctor's Medical Reports
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        mr.id,
        mr.report_type,
        mr.report_title,
        mr.report_description,
        mr.report_file,
        mr.status,
        mr.uploaded_at,
        mr.created_at,

        p.patient_code,
        p.name AS patient_name,
        p.phone,

        t.token_number

    FROM medical_reports mr

    INNER JOIN patients p
        ON mr.patient_id = p.id

    LEFT JOIN tokens t
        ON mr.token_id = t.id

    WHERE mr.doctor_id = ?

    ORDER BY mr.id DESC
");

$stmt->bind_param("i", $doctor_id);

$stmt->execute();

$reports = $stmt->get_result();

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


            <!-- Page Header -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Medical Reports
                    </h2>

                    <p class="text-muted mb-0">
                        View medical reports of your patients.
                    </p>

                </div>


                <a
                    href="../dashboard.php"
                    class="btn btn-secondary"
                >
                    Back to Dashboard
                </a>

            </div>


            <!-- Reports -->

            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-dark">

                                <tr>

                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Patient Code</th>
                                    <th>Token</th>
                                    <th>Report Type</th>
                                    <th>Report Title</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php if ($reports->num_rows > 0): ?>

                                    <?php $count = 1; ?>

                                    <?php while ($report = $reports->fetch_assoc()): ?>

                                        <tr>

                                            <td>
                                                <?php echo $count++; ?>
                                            </td>


                                            <!-- Patient -->

                                            <td>

                                                <strong>

                                                    <?php echo htmlspecialchars(
                                                        $report["patient_name"]
                                                    ); ?>

                                                </strong>

                                                <br>

                                                <small class="text-muted">

                                                    <?php echo htmlspecialchars(
                                                        $report["phone"]
                                                    ); ?>

                                                </small>

                                            </td>


                                            <!-- Patient Code -->

                                            <td>

                                                <?php echo htmlspecialchars(
                                                    $report["patient_code"]
                                                ); ?>

                                            </td>


                                            <!-- Token -->

                                            <td>

                                                <?php

                                                echo $report["token_number"]
                                                    ? htmlspecialchars($report["token_number"])
                                                    : "N/A";

                                                ?>

                                            </td>


                                            <!-- Report Type -->

                                            <td>

                                                <?php echo htmlspecialchars(
                                                    $report["report_type"]
                                                ); ?>

                                            </td>


                                            <!-- Report Title -->

                                            <td>

                                                <?php echo htmlspecialchars(
                                                    $report["report_title"]
                                                ); ?>

                                            </td>


                                            <!-- Status -->

                                            <td>

                                                <?php if ($report["status"] === "PENDING"): ?>

                                                    <span class="badge bg-warning text-dark">

                                                        PENDING

                                                    </span>

                                                <?php elseif ($report["status"] === "UPLOADED"): ?>

                                                    <span class="badge bg-success">

                                                        UPLOADED

                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge bg-secondary">

                                                        <?php echo htmlspecialchars(
                                                            $report["status"]
                                                        ); ?>

                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <!-- Action -->

                                            <td>

                                                <?php if (
                                                    $report["status"] === "UPLOADED"
                                                    &&
                                                    !empty($report["report_file"])
                                                ): ?>

                                                    <a
                                                        href="../../reports/uploads/<?php echo rawurlencode($report["report_file"]); ?>"
                                                        target="_blank"
                                                        class="btn btn-success btn-sm"
                                                    >
                                                        View Report
                                                    </a>

                                                <?php elseif ($report["status"] === "PENDING"): ?>

                                                    <span class="text-warning fw-bold">

                                                        Report Pending

                                                    </span>

                                                <?php else: ?>

                                                    <span class="text-muted">

                                                        No Report

                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td
                                            colspan="8"
                                            class="text-center py-5"
                                        >

                                            <h5 class="fw-bold">
                                                No Reports Found
                                            </h5>

                                            <p class="text-muted mb-0">
                                                No medical reports are available
                                                for your patients.
                                            </p>

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

require_once "../../includes/footer.php";

?>