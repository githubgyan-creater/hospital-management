 <?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "receptionist") {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/database.php";
require_once "../../includes/header.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    die("Patient not found.");
}

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->

        <div class="col-md-2 p-0">

            <?php require_once "../../includes/sidebar.php"; ?>

        </div>


        <!-- Main Content -->

        <div class="col-md-10">

            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h2>Patient Details</h2>

                    <a href="index.php" class="btn btn-secondary">
                        Back to Patients
                    </a>

                </div>


                <div class="card">

                    <div class="card-header bg-dark text-white">

                        <strong>Patient Information</strong>

                    </div>


                    <div class="card-body">

                        <table class="table table-bordered table-striped">

                            <tr>
                                <th width="30%">Patient ID</th>

                                <td>
                                    <?php echo htmlspecialchars($patient["patient_code"]); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Patient Name</th>

                                <td>
                                    <?php echo htmlspecialchars($patient["name"]); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Date of Birth</th>

                                <td>
                                    <?php echo htmlspecialchars($patient["date_of_birth"]); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Age</th>

                                <td>
                                    <?php echo htmlspecialchars($patient["age"]); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Gender</th>

                                <td>
                                    <?php echo htmlspecialchars($patient["gender"]); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Phone</th>

                                <td>
                                    <?php echo htmlspecialchars($patient["phone"]); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Email</th>

                                <td>
                                    <?php echo htmlspecialchars($patient["email"]); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Address</th>

                                <td>
                                    <?php echo htmlspecialchars($patient["address"]); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Blood Group</th>

                                <td>
                                    <?php echo htmlspecialchars($patient["blood_group"]); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Emergency Contact</th>

                                <td>
                                    <?php echo htmlspecialchars($patient["emergency_contact"]); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Medical History</th>

                                <td>
                                    <?php echo htmlspecialchars($patient["medical_history"]); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Registration Date</th>

                                <td>
                                    <?php echo date(
                                        "d-m-Y",
                                        strtotime($patient["created_at"])
                                    ); ?>
                                </td>
                            </tr>


                            <tr>
                                <th>Last Updated</th>

                                <td>
                                    <?php echo date(
                                        "d-m-Y",
                                        strtotime($patient["updated_at"])
                                    ); ?>
                                </td>
                            </tr>

                        </table>


                        <div class="mt-3">

                            <a
                                href="edit.php?id=<?php echo $patient["id"]; ?>"
                                class="btn btn-primary"
                            >
                                Edit Patient
                            </a>


                            <a
                                href="index.php"
                                class="btn btn-secondary"
                            >
                                Back
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>