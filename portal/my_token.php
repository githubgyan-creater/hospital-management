<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["portal_patient_id"])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION["portal_patient_id"];

$tokens = [];


/* =========================
   GET PATIENT TOKENS
========================= */

$stmt = $conn->prepare(
    "SELECT
        t.id,
        t.token_number,
        t.token_date,
        t.status,
        t.priority,
        t.called_at,
        t.consultation_started_at,
        t.completed_at,
        d.name AS doctor_name,
        dep.name AS department_name
     FROM tokens t
     INNER JOIN doctors d
        ON t.doctor_id = d.id
     INNER JOIN departments dep
        ON t.department_id = dep.id
     WHERE t.patient_id = ?
     ORDER BY
        t.token_date DESC,
        t.id DESC"
);

$stmt->bind_param(
    "i",
    $patient_id
);

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $tokens[] = $row;
}

$stmt->close();


require_once "../includes/header.php";

?>

<div class="container py-4">

    <!-- =========================
         HEADER
    ========================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                My Token
            </h2>

            <p class="text-muted mb-0">
                View your OPD token and queue status.
            </p>

        </div>

        <a
            href="dashboard.php"
            class="btn btn-secondary"
        >
            Back to Dashboard
        </a>

    </div>


    <?php if (count($tokens) === 0): ?>

        <!-- =========================
             NO TOKEN
        ========================== -->

        <div class="card shadow-sm border-0">

            <div class="card-body text-center p-5">

                <h5 class="fw-bold">
                    No Token Found
                </h5>

                <p class="text-muted mb-4">
                    You do not have any token yet.
                </p>

                <a
                    href="book_appointment.php"
                    class="btn btn-primary"
                >
                    Book Appointment
                </a>

            </div>

        </div>

    <?php else: ?>


        <!-- =========================
             TOKEN CARDS
        ========================== -->

        <div class="row g-4">

            <?php foreach (
                $tokens
                as $token
            ): ?>

                <?php

                $status =
                    strtoupper(
                        $token["status"]
                    );

                $status_class =
                    "bg-secondary";

                if (
                    $status === "WAITING"
                ) {

                    $status_class =
                        "bg-warning text-dark";

                } elseif (
                    $status === "CALLED"
                ) {

                    $status_class =
                        "bg-primary";

                } elseif (
                    $status === "IN CONSULTATION"
                ) {

                    $status_class =
                        "bg-info text-dark";

                } elseif (
                    $status === "REPORT PENDING"
                ) {

                    $status_class =
                        "bg-warning text-dark";

                } elseif (
                    $status === "REPORT UPLOADED"
                ) {

                    $status_class =
                        "bg-success";

                } elseif (
                    $status === "FOLLOW UP"
                ) {

                    $status_class =
                        "bg-info text-dark";

                } elseif (
                    $status === "COMPLETED"
                ) {

                    $status_class =
                        "bg-success";

                }

                ?>


                <div class="col-md-6 col-lg-4">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body p-4">


                            <!-- TOKEN NUMBER -->

                            <div class="text-center mb-4">

                                <small class="text-muted">
                                    Token Number
                                </small>

                                <h1 class="fw-bold text-primary mb-2">

                                    <?php
                                    echo htmlspecialchars(
                                        $token["token_number"]
                                    );
                                    ?>

                                </h1>

                                <span
                                    class="badge <?php
                                    echo $status_class;
                                    ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $token["status"]
                                    );
                                    ?>

                                </span>

                            </div>


                            <hr>


                            <!-- DEPARTMENT -->

                            <p class="mb-2">

                                <strong>
                                    Department:
                                </strong>

                                <br>

                                <span class="text-muted">

                                    <?php
                                    echo htmlspecialchars(
                                        $token["department_name"]
                                    );
                                    ?>

                                </span>

                            </p>


                            <!-- DOCTOR -->

                            <p class="mb-2">

                                <strong>
                                    Doctor:
                                </strong>

                                <br>

                                <span class="text-muted">

                                    <?php
                                    echo htmlspecialchars(
                                        $token["doctor_name"]
                                    );
                                    ?>

                                </span>

                            </p>


                            <!-- DATE -->

                            <p class="mb-2">

                                <strong>
                                    Token Date:
                                </strong>

                                <br>

                                <span class="text-muted">

                                    <?php

                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $token["token_date"]
                                        )
                                    );

                                    ?>

                                </span>

                            </p>


                            <!-- PRIORITY -->

                            <p class="mb-2">

                                <strong>
                                    Priority:
                                </strong>

                                <br>

                                <span class="text-muted">

                                    <?php
                                    echo htmlspecialchars(
                                        $token["priority"]
                                    );
                                    ?>

                                </span>

                            </p>


                            <!-- CALLED TIME -->

                            <?php if (
                                !empty(
                                    $token["called_at"]
                                )
                            ): ?>

                                <p class="mb-2">

                                    <strong>
                                        Called At:
                                    </strong>

                                    <br>

                                    <span class="text-muted">

                                        <?php

                                        echo date(
                                            "d M Y, h:i A",
                                            strtotime(
                                                $token["called_at"]
                                            )
                                        );

                                        ?>

                                    </span>

                                </p>

                            <?php endif; ?>


                            <!-- CONSULTATION START -->

                            <?php if (
                                !empty(
                                    $token[
                                        "consultation_started_at"
                                    ]
                                )
                            ): ?>

                                <p class="mb-2">

                                    <strong>
                                        Consultation Started:
                                    </strong>

                                    <br>

                                    <span class="text-muted">

                                        <?php

                                        echo date(
                                            "d M Y, h:i A",
                                            strtotime(
                                                $token[
                                                    "consultation_started_at"
                                                ]
                                            )
                                        );

                                        ?>

                                    </span>

                                </p>

                            <?php endif; ?>


                            <!-- COMPLETED -->

                            <?php if (
                                !empty(
                                    $token["completed_at"]
                                )
                            ): ?>

                                <p class="mb-0">

                                    <strong>
                                        Completed At:
                                    </strong>

                                    <br>

                                    <span class="text-muted">

                                        <?php

                                        echo date(
                                            "d M Y, h:i A",
                                            strtotime(
                                                $token["completed_at"]
                                            )
                                        );

                                        ?>

                                    </span>

                                </p>

                            <?php endif; ?>


                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <!-- =========================
         BACK BUTTON
    ========================== -->

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