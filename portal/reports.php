<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["portal_patient_id"])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION["portal_patient_id"];

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
        d.name AS doctor_name
    FROM medical_reports mr
    LEFT JOIN doctors d
        ON mr.doctor_id = d.id
    WHERE mr.patient_id = ?
    ORDER BY mr.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();

$result = $stmt->get_result();

require_once "../includes/header.php";

?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Medical Reports
            </h2>

            <p class="text-muted mb-0">
                View your medical and laboratory reports.
            </p>
        </div>

        <a
            href="dashboard.php"
            class="btn btn-secondary">
            Back to Dashboard
        </a>

    </div>


    <?php if ($result->num_rows > 0): ?>

        <div class="row g-4">

            <?php while ($report = $result->fetch_assoc()): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body p-4">

                            <h5 class="fw-bold">
                                <?php
                                echo htmlspecialchars(
                                    $report["report_title"]
                                );
                                ?>
                            </h5>

                            <p class="text-muted mb-2">

                                <strong>Report Type:</strong>

                                <?php
                                echo htmlspecialchars(
                                    $report["report_type"]
                                );
                                ?>

                            </p>


                            <?php if (!empty($report["doctor_name"])): ?>

                                <p class="text-muted mb-2">

                                    <strong>Doctor:</strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $report["doctor_name"]
                                    );
                                    ?>

                                </p>

                            <?php endif; ?>


                            <p class="text-muted mb-2">

                                <strong>Status:</strong>

                                <span class="badge bg-info text-dark">

                                    <?php
                                    echo htmlspecialchars(
                                        $report["status"]
                                    );
                                    ?>

                                </span>

                            </p>


                            <?php if (!empty($report["report_description"])): ?>

                                <p class="text-muted">

                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $report["report_description"]
                                        )
                                    );
                                    ?>

                                </p>

                            <?php endif; ?>


                            <?php if (!empty($report["report_file"])): ?>

                                <a
                                    href="../reports/uploads/<?php echo rawurlencode($report["report_file"]); ?>"
                                    target="_blank"
                                    class="btn btn-primary">

                                    View Report

                                </a>

                            <?php else: ?>

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    disabled>

                                    Report File Not Available

                                </button>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-info">

            No medical reports are available yet.

        </div>

    <?php endif; ?>

</div>


<?php

$stmt->close();

require_once "../includes/footer.php";

?>