<?php

session_start();

/* Allow Admin and Receptionist to access reports */

if (
    !isset($_SESSION["user_role"]) ||
    !in_array($_SESSION["user_role"], ["admin", "receptionist"])
) {
    header("Location: ../login.php");
    exit();
}

require_once "../config/database.php";


/* Get patient records */

$query = "SELECT * FROM patients ORDER BY id DESC";

$result = $conn->query($query);


require_once "../includes/header.php";

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->

        <div class="col-md-2 p-0">

            <?php require_once "../includes/sidebar.php"; ?>

        </div>


        <!-- Main Content -->

        <div class="col-md-10">

            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>

                        <h2>Patient Reports</h2>

                        <p class="text-muted">
                            View registered patient information
                        </p>

                    </div>

                    <a
                        href="index.php"
                        class="btn btn-secondary"
                    >
                        Back to Reports
                    </a>

                </div>


                <div class="card">

                    <div class="card-header bg-primary text-white">

                        <strong>
                            Patient Report
                        </strong>

                    </div>


                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover">

                                <thead class="table-primary">

                                    <tr>

                                        <th>Patient ID</th>

                                        <th>Patient Code</th>

                                        <th>Name</th>

                                        <th>Gender</th>

                                        <th>Date of Birth</th>

                                        <th>Phone</th>

                                        <th>Email</th>

                                        <th>Created At</th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php if ($result && $result->num_rows > 0): ?>

                                        <?php while ($patient = $result->fetch_assoc()): ?>

                                            <tr>

                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $patient["id"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $patient["patient_code"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $patient["name"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $patient["gender"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $patient["date_of_birth"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $patient["phone"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $patient["email"] ?? "-"
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $patient["created_at"] ?? "-"
                                                    );
                                                    ?>
                                                </td>

                                            </tr>

                                        <?php endwhile; ?>

                                    <?php else: ?>

                                        <tr>

                                            <td
                                                colspan="8"
                                                class="text-center"
                                            >
                                                No patient records found.
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

</div>