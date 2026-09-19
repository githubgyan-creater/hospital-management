<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$patientId = (int) $_GET["id"];

$sql = "SELECT *
        FROM patients
        WHERE id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patientId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    header("Location: index.php");
    exit;
}

$patient = $result->fetch_assoc();

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
                    <h2 class="fw-bold">Patient Details</h2>
                    <p class="text-muted mb-0">
                        View patient information
                    </p>
                </div>

                <a href="index.php" class="btn btn-secondary">
                    Back to Patients
                </a>

            </div>

            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <strong>Patient ID</strong>
                            <div>
                                <?php echo htmlspecialchars($patient["patient_code"]); ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Patient Name</strong>
                            <div>
                                <?php echo htmlspecialchars($patient["name"]); ?>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <strong>Age</strong>
                            <div>
                                <?php echo htmlspecialchars($patient["age"]); ?>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <strong>Gender</strong>
                            <div>
                                <?php echo htmlspecialchars($patient["gender"]); ?>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <strong>Blood Group</strong>
                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $patient["blood_group"] ?: "Not provided"
                                );
                                ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Date of Birth</strong>
                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $patient["date_of_birth"] ?: "Not provided"
                                );
                                ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Phone</strong>
                            <div>
                                <?php echo htmlspecialchars($patient["phone"]); ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Email</strong>
                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $patient["email"] ?: "Not provided"
                                );
                                ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Emergency Contact</strong>
                            <div>
                                <?php
                                echo htmlspecialchars(
                                    $patient["emergency_contact"] ?: "Not provided"
                                );
                                ?>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <strong>Address</strong>
                            <div>
                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $patient["address"] ?: "Not provided"
                                    )
                                );
                                ?>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <strong>Medical History</strong>
                            <div>
                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $patient["medical_history"] ?: "Not provided"
                                    )
                                );
                                ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Registration Date</strong>
                            <div>
                                <?php
                                echo date(
                                    "d-m-Y",
                                    strtotime($patient["created_at"])
                                );
                                ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Last Updated</strong>
                            <div>
                                <?php
                                echo date(
                                    "d-m-Y",
                                    strtotime($patient["updated_at"])
                                );
                                ?>
                            </div>
                        </div>

                    </div>

                    <div class="mt-3">

                        <a
                            href="edit.php?id=<?php echo $patient["id"]; ?>"
                            class="btn btn-warning">
                            Edit Patient
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