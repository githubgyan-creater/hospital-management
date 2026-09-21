<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["portal_patient_id"])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION["portal_patient_id"];


/* =========================
   FOLLOW-UPS
========================= */

$sql = "
    SELECT
        f.id,
        f.follow_up_date,
        f.reason,
        f.status,
        f.notes,
        d.name AS doctor_name
    FROM followups f
    LEFT JOIN doctors d
        ON f.doctor_id = d.id
    WHERE f.patient_id = ?
    ORDER BY f.follow_up_date ASC, f.id DESC
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $patient_id
);

$stmt->execute();

$result = $stmt->get_result();


require_once "../includes/header.php";

?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Follow-ups
            </h2>

            <p class="text-muted mb-0">
                View your upcoming follow-up appointments and instructions.
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

            <?php while ($followup = $result->fetch_assoc()): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body p-4">

                            <h5 class="fw-bold mb-3">
                                Follow-up Appointment
                            </h5>


                            <p class="mb-2">

                                <strong>Date:</strong>

                                <?php
                                echo date(
                                    "d-m-Y",
                                    strtotime(
                                        $followup["follow_up_date"]
                                    )
                                );
                                ?>

                            </p>


                            <?php if (!empty($followup["doctor_name"])): ?>

                                <p class="mb-2">

                                    <strong>Doctor:</strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $followup["doctor_name"]
                                    );
                                    ?>

                                </p>

                            <?php endif; ?>


                            <p class="mb-2">

                                <strong>Status:</strong>

                                <?php

                                $status = $followup["status"];

                                if ($status === "Completed") {

                                    $badgeClass = "bg-success";

                                } elseif ($status === "Cancelled") {

                                    $badgeClass = "bg-danger";

                                } else {

                                    $badgeClass = "bg-warning text-dark";

                                }

                                ?>

                                <span class="badge <?php echo $badgeClass; ?>">

                                    <?php
                                    echo htmlspecialchars($status);
                                    ?>

                                </span>

                            </p>


                            <?php if (!empty($followup["reason"])): ?>

                                <p class="mb-2">

                                    <strong>Reason:</strong>

                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $followup["reason"]
                                        )
                                    );
                                    ?>

                                </p>

                            <?php endif; ?>


                            <?php if (!empty($followup["notes"])): ?>

                                <p class="text-muted mb-0">

                                    <strong>Notes:</strong><br>

                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $followup["notes"]
                                        )
                                    );
                                    ?>

                                </p>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-info">

            No follow-up appointments are available.

        </div>

    <?php endif; ?>

</div>


<?php

$stmt->close();

require_once "../includes/footer.php";

?>