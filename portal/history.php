<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["portal_patient_id"])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION["portal_patient_id"];


/* =========================
   PATIENT HISTORY
========================= */

$sql = "
    SELECT
        ph.id,
        ph.history_type,
        ph.title,
        ph.description,
        ph.history_date,
        d.name AS doctor_name
    FROM patient_history ph
    LEFT JOIN doctors d
        ON ph.doctor_id = d.id
    WHERE ph.patient_id = ?
    ORDER BY ph.history_date DESC, ph.id DESC
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
                Patient History
            </h2>

            <p class="text-muted mb-0">
                View your medical and consultation history.
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

            <?php while ($history = $result->fetch_assoc()): ?>

                <div class="col-md-6">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between">

                                <h5 class="fw-bold">

                                    <?php
                                    echo htmlspecialchars(
                                        $history["title"]
                                    );
                                    ?>

                                </h5>


                                <span class="badge bg-primary">

                                    <?php
                                    echo htmlspecialchars(
                                        $history["history_type"]
                                    );
                                    ?>

                                </span>

                            </div>


                            <hr>


                            <p class="mb-2">

                                <strong>Date:</strong>

                                <?php
                                echo date(
                                    "d-m-Y",
                                    strtotime(
                                        $history["history_date"]
                                    )
                                );
                                ?>

                            </p>


                            <?php if (!empty($history["doctor_name"])): ?>

                                <p class="mb-2">

                                    <strong>Doctor:</strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $history["doctor_name"]
                                    );
                                    ?>

                                </p>

                            <?php endif; ?>


                            <?php if (!empty($history["description"])): ?>

                                <p class="text-muted mb-0">

                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $history["description"]
                                        )
                                    );
                                    ?>

                                </p>

                            <?php else: ?>

                                <p class="text-muted mb-0">
                                    No additional details available.
                                </p>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-info">

            No patient history is available yet.

        </div>

    <?php endif; ?>

</div>


<?php

$stmt->close();

require_once "../includes/footer.php";

?>