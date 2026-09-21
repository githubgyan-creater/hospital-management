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
require_once "../includes/header.php";

$message = "";
$message_type = "";


/*
|--------------------------------------------------------------------------
| Get Medical Reports
|--------------------------------------------------------------------------
*/

$sql = "
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

        d.name AS doctor_name

    FROM medical_reports mr

    INNER JOIN patients p
        ON mr.patient_id = p.id

    LEFT JOIN doctors d
        ON mr.doctor_id = d.id

    ORDER BY
        CASE
            WHEN mr.status = 'PENDING' THEN 1
            WHEN mr.status = 'UPLOADED' THEN 2
            ELSE 3
        END,
        mr.id DESC
";

$result = $conn->query($sql);

?>

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
                        Medical Reports
                    </h2>

                    <p class="text-muted mb-0">
                        Manage patient medical reports.
                    </p>

                </div>

                <a
                    href="../receptionist/dashboard.php"
                    class="btn btn-secondary"
                >
                    Back to Dashboard
                </a>

            </div>


            <!-- Reports Table -->

            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-dark">

                                <tr>

                                    <th>#</th>

                                    <th>Patient</th>

                                    <th>Patient Code</th>

                                    <th>Doctor</th>

                                    <th>Report Type</th>

                                    <th>Report Title</th>

                                    <th>Status</th>

                                    <th>Action</th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php if ($result && $result->num_rows > 0): ?>

                                    <?php $count = 1; ?>

                                    <?php while ($report = $result->fetch_assoc()): ?>

                                        <tr>

                                            <!-- Number -->

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


                                            <!-- Doctor -->

                                            <td>

                                                <?php

                                                echo $report["doctor_name"]
                                                    ? htmlspecialchars($report["doctor_name"])
                                                    : "Not Assigned";

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

                                                <?php if ($report["status"] === "PENDING"): ?>

                                                    <a
                                                        href="upload.php?id=<?php echo $report["id"]; ?>"
                                                        class="btn btn-primary btn-sm"
                                                    >
                                                        Upload Report
                                                    </a>

                                                <?php elseif (
                                                    $report["status"] === "UPLOADED"
                                                    &&
                                                    !empty($report["report_file"])
                                                ): ?>

                                                    <a
                                                        href="uploads/<?php echo rawurlencode($report["report_file"]); ?>"
                                                        target="_blank"
                                                        class="btn btn-success btn-sm"
                                                    >
                                                        View Report
                                                    </a>

                                                <?php else: ?>

                                                    <span class="text-muted">
                                                        No Action
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

                                                No medical reports are currently
                                                available.

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

require_once "../includes/footer.php";

?>