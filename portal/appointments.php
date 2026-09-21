<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["portal_patient_id"])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION["portal_patient_id"];

$appointments = [];


/* =========================
   GET PATIENT APPOINTMENTS
========================= */

$stmt = $conn->prepare(
    "SELECT
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.reason,
        a.notes,
        a.status,
        d.name AS doctor_name,
        d.specialization,
        dep.name AS department_name
     FROM appointments a
     INNER JOIN doctors d
        ON a.doctor_id = d.id
     INNER JOIN departments dep
        ON d.department_id = dep.id
     WHERE a.patient_id = ?
     ORDER BY
        a.appointment_date DESC,
        a.appointment_time DESC"
);

$stmt->bind_param(
    "i",
    $patient_id
);

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $appointments[] = $row;
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
                My Appointments
            </h2>

            <p class="text-muted mb-0">
                View your hospital appointments.
            </p>

        </div>

        <a
            href="dashboard.php"
            class="btn btn-secondary"
        >
            Back to Dashboard
        </a>

    </div>


    <!-- =========================
         BOOK NEW APPOINTMENT
    ========================== -->

    <div class="mb-4">

        <a
            href="book_appointment.php"
            class="btn btn-success"
        >
            + Book New Appointment
        </a>

    </div>


    <?php if (count($appointments) === 0): ?>

        <!-- =========================
             NO APPOINTMENTS
        ========================== -->

        <div class="card shadow-sm border-0">

            <div class="card-body text-center p-5">

                <h5 class="fw-bold">
                    No Appointments Found
                </h5>

                <p class="text-muted">
                    You have not booked any appointments yet.
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
             DESKTOP TABLE
        ========================== -->

        <div class="card shadow-sm border-0">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    Doctor
                                </th>

                                <th>
                                    Department
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Time
                                </th>

                                <th>
                                    Reason
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach (
                                $appointments
                                as $appointment
                            ): ?>

                                <tr>

                                    <td>

                                        <strong>
                                            #<?php
                                            echo (int)
                                                $appointment["id"];
                                            ?>
                                        </strong>

                                    </td>


                                    <td>

                                        <strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $appointment[
                                                    "doctor_name"
                                                ]
                                            );
                                            ?>

                                        </strong>

                                        <br>

                                        <small class="text-muted">

                                            <?php
                                            echo htmlspecialchars(
                                                $appointment[
                                                    "specialization"
                                                ]
                                            );
                                            ?>

                                        </small>

                                    </td>


                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $appointment[
                                                "department_name"
                                            ]
                                        );
                                        ?>

                                    </td>


                                    <td>

                                        <?php

                                        echo date(
                                            "d M Y",
                                            strtotime(
                                                $appointment[
                                                    "appointment_date"
                                                ]
                                            )
                                        );

                                        ?>

                                    </td>


                                    <td>

                                        <?php

                                        echo date(
                                            "h:i A",
                                            strtotime(
                                                $appointment[
                                                    "appointment_time"
                                                ]
                                            )
                                        );

                                        ?>

                                    </td>


                                    <td>

                                        <?php

                                        if (
                                            !empty(
                                                $appointment["reason"]
                                            )
                                        ) {

                                            echo htmlspecialchars(
                                                $appointment["reason"]
                                            );

                                        } else {

                                            echo '<span class="text-muted">
                                                Not provided
                                            </span>';

                                        }

                                        ?>

                                    </td>


                                    <td>

                                        <?php

                                        $status =
                                            $appointment["status"];

                                        $badge_class =
                                            "bg-secondary";

                                        if (
                                            $status === "Pending"
                                        ) {

                                            $badge_class =
                                                "bg-warning text-dark";

                                        } elseif (
                                            $status === "Completed"
                                        ) {

                                            $badge_class =
                                                "bg-success";

                                        } elseif (
                                            $status === "Cancelled"
                                        ) {

                                            $badge_class =
                                                "bg-danger";

                                        } elseif (
                                            $status === "Confirmed"
                                        ) {

                                            $badge_class =
                                                "bg-primary";

                                        }

                                        ?>

                                        <span
                                            class="badge <?php
                                            echo $badge_class;
                                            ?>"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $status
                                            );
                                            ?>

                                        </span>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <!-- =========================
             MOBILE-FRIENDLY CARDS
        ========================== -->

        <div class="d-md-none mt-3">

            <?php foreach (
                $appointments
                as $appointment
            ): ?>

                <?php

                $status =
                    $appointment["status"];

                $badge_class =
                    "bg-secondary";

                if (
                    $status === "Pending"
                ) {

                    $badge_class =
                        "bg-warning text-dark";

                } elseif (
                    $status === "Completed"
                ) {

                    $badge_class =
                        "bg-success";

                } elseif (
                    $status === "Cancelled"
                ) {

                    $badge_class =
                        "bg-danger";

                } elseif (
                    $status === "Confirmed"
                ) {

                    $badge_class =
                        "bg-primary";

                }

                ?>

                <div class="card shadow-sm border-0 mb-3">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <h5 class="fw-bold">

                                Appointment #

                                <?php
                                echo (int)
                                    $appointment["id"];
                                ?>

                            </h5>

                            <span
                                class="badge <?php
                                echo $badge_class;
                                ?>"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $status
                                );
                                ?>

                            </span>

                        </div>


                        <hr>


                        <p class="mb-2">

                            <strong>
                                Doctor:
                            </strong>

                            <?php
                            echo htmlspecialchars(
                                $appointment[
                                    "doctor_name"
                                ]
                            );
                            ?>

                        </p>


                        <p class="mb-2">

                            <strong>
                                Department:
                            </strong>

                            <?php
                            echo htmlspecialchars(
                                $appointment[
                                    "department_name"
                                ]
                            );
                            ?>

                        </p>


                        <p class="mb-2">

                            <strong>
                                Date:
                            </strong>

                            <?php

                            echo date(
                                "d M Y",
                                strtotime(
                                    $appointment[
                                        "appointment_date"
                                    ]
                                )
                            );

                            ?>

                        </p>


                        <p class="mb-2">

                            <strong>
                                Time:
                            </strong>

                            <?php

                            echo date(
                                "h:i A",
                                strtotime(
                                    $appointment[
                                        "appointment_time"
                                    ]
                                )
                            );

                            ?>

                        </p>


                        <p class="mb-0">

                            <strong>
                                Reason:
                            </strong>

                            <?php

                            echo !empty(
                                $appointment["reason"]
                            )
                                ? htmlspecialchars(
                                    $appointment["reason"]
                                )
                                : "Not provided";

                            ?>

                        </p>

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