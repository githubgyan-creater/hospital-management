 <?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "receptionist") {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/database.php";
require_once "../../includes/header.php";

$sql = "SELECT * FROM patients ORDER BY id DESC";
$result = $conn->query($sql);

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

                    <h2>Patients</h2>

                    <a href="add.php" class="btn btn-success">
                        + Add Patient
                    </a>

                </div>


                <div class="card">

                    <div class="card-header bg-dark text-white">

                        <strong>Patient List</strong>

                    </div>


                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped">

                                <thead class="table-dark">

                                    <tr>

                                        <th>ID</th>

                                        <th>Patient Code</th>

                                        <th>Name</th>

                                        <th>Age</th>

                                        <th>Gender</th>

                                        <th>Phone</th>

                                        <th>Action</th>

                                    </tr>

                                </thead>


                                <tbody>

                                <?php if ($result && $result->num_rows > 0): ?>

                                    <?php while ($patient = $result->fetch_assoc()): ?>

                                        <tr>

                                            <td>
                                                <?php echo $patient["id"]; ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($patient["patient_code"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($patient["name"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($patient["age"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($patient["gender"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($patient["phone"]); ?>
                                            </td>

                                            <td>

                                                <a
                                                    href="view.php?id=<?php echo $patient["id"]; ?>"
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    View
                                                </a>

                                                <a
                                                    href="edit.php?id=<?php echo $patient["id"]; ?>"
                                                    class="btn btn-sm btn-warning"
                                                >
                                                    Edit
                                                </a>

                                                <a
                                                    href="delete.php?id=<?php echo $patient["id"]; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this patient?');"
                                                >
                                                    Delete
                                                </a>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td colspan="7" class="text-center">

                                            No patients found.

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