<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

// Check Doctor ID 



if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$doctorId = (int) $_GET["id"];

//  Get Doctor + Department

$sql = "SELECT
            doctors.*,
            departments.name AS department_name
        FROM doctors
        LEFT JOIN departments
            ON doctors.department_id = departments.id
        WHERE doctors.id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $doctorId);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: index.php");

    exit;
}

$doctor = $result->fetch_assoc();

$stmt->close();

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
                        Doctor Details
                    </h2>

                    <p class="text-muted mb-0">
                        View doctor information
                    </p>

                </div>

                <a href="index.php" class="btn btn-secondary">
                    Back to Doctors
                </a>

            </div>


            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="row">

                        <!-- Doctor ID -->

                        <div class="col-md-6 mb-3">

                            <strong>Doctor ID</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $doctor["id"]
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Doctor Name -->

                        <div class="col-md-6 mb-3">

                            <strong>Doctor Name</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $doctor["name"]
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Email -->

                        <div class="col-md-6 mb-3">

                            <strong>Email</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $doctor["email"] ?: "Not provided"
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Phone -->

                        <div class="col-md-6 mb-3">

                            <strong>Phone</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $doctor["phone"]
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Department -->

                        <div class="col-md-6 mb-3">

                            <strong>Department</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $doctor["department_name"]
                                    ?: "Not assigned"
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Specialization -->

                        <div class="col-md-6 mb-3">

                            <strong>Specialization</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $doctor["specialization"]
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Qualification -->

                        <div class="col-md-6 mb-3">

                            <strong>Qualification</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $doctor["qualification"]
                                    ?: "Not provided"
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Consultation Fee -->

                        <div class="col-md-6 mb-3">

                            <strong>Consultation Fee</strong>

                            <div>
                                ₹<?php
                                echo number_format(
                                    $doctor["consultation_fee"],
                                    2
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Available Days -->

                        <div class="col-md-6 mb-3">

                            <strong>Available Days</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $doctor["available_days"]
                                    ?: "Not provided"
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Available Time -->

                        <div class="col-md-6 mb-3">

                            <strong>Available Time</strong>

                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $doctor["available_time"]
                                    ?: "Not provided"
                                );
                                ?>
                            </div>

                        </div>


                        <!-- Status -->

                        <div class="col-md-6 mb-3">

                            <strong>Status</strong>

                            <div>

                                <?php if ($doctor["status"] == 1): ?>

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-secondary">
                                        Inactive
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>


                        <!-- Created Date -->

                        <div class="col-md-6 mb-3">

                            <strong>Created Date</strong>

                            <div>
                                <?php
                                echo date(
                                    "d-m-Y",
                                    strtotime(
                                        $doctor["created_at"]
                                    )
                                );
                                ?>
                            </div>

                        </div>

                    </div>


                    <div class="mt-3">

                        <a
                            href="edit.php?id=<?php echo $doctor["id"]; ?>"
                            class="btn btn-warning">
                            Edit Doctor
                        </a>

                        <a
                            href="index.php"
                            class="btn btn-secondary">
                            Back
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once "../../includes/footer.php";

?>