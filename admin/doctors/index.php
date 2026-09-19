 <?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";
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

            <!-- Success Message -->
            <?php if (isset($_GET["success"])): ?>

                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET["success"]); ?>
                </div>

            <?php endif; ?>

            <!-- Error Message -->
            <?php if (isset($_GET["error"])): ?>

                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_GET["error"]); ?>
                </div>

            <?php endif; ?>


            <!-- Page Heading -->
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h2 class="fw-bold mb-1">
                        Doctors
                    </h2>

                    <p class="text-muted mb-0">
                        Manage hospital doctors
                    </p>
                </div>

                <a href="add.php" class="btn btn-primary">
                    + Add Doctor
                </a>

            </div>


            <!-- Doctor Table -->
            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-primary">

                                <tr>
                                    <th>Doctor ID</th>
                                    <th>Doctor Name</th>
                                    <th>Department</th>
                                    <th>Specialization</th>
                                    <th>Phone</th>
                                    <th>Consultation Fee</th>
                                    <th>Status</th>
                                    <th style="min-width: 270px;">Action</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php

                                $sql = "SELECT
                                            doctors.*,
                                            departments.name AS department_name
                                        FROM doctors
                                        LEFT JOIN departments
                                            ON doctors.department_id = departments.id
                                        ORDER BY doctors.id DESC";

                                $stmt = $conn->prepare($sql);

                                $stmt->execute();

                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {

                                    while ($doctor = $result->fetch_assoc()) {

                                ?>

                                        <tr>

                                            <!-- Doctor ID -->
                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $doctor["id"]
                                                );
                                                ?>
                                            </td>


                                            <!-- Doctor Name -->
                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $doctor["name"]
                                                );
                                                ?>
                                            </td>


                                            <!-- Department -->
                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $doctor["department_name"]
                                                    ?: "Not assigned"
                                                );
                                                ?>
                                            </td>


                                            <!-- Specialization -->
                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $doctor["specialization"]
                                                );
                                                ?>
                                            </td>


                                            <!-- Phone -->
                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $doctor["phone"]
                                                );
                                                ?>
                                            </td>


                                            <!-- Consultation Fee -->
                                            <td>
                                                ₹<?php
                                                echo number_format(
                                                    $doctor["consultation_fee"],
                                                    2
                                                );
                                                ?>
                                            </td>


                                            <!-- Status -->
                                            <td>

                                                <?php if ($doctor["status"] == 1): ?>

                                                    <span class="badge bg-success">
                                                        Active
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge bg-secondary">
                                                        Inactive
                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <!-- Action -->
                                            <td>

                                                <div class="d-flex flex-wrap gap-1">

                                                    <!-- View -->
                                                    <a
                                                        href="view.php?id=<?php echo $doctor["id"]; ?>"
                                                        class="btn btn-sm btn-info">
                                                        View
                                                    </a>


                                                    <!-- Edit -->
                                                    <a
                                                        href="edit.php?id=<?php echo $doctor["id"]; ?>"
                                                        class="btn btn-sm btn-warning">
                                                        Edit
                                                    </a>


                                                    <!-- Delete -->
                                                    <a
                                                        href="delete.php?id=<?php echo $doctor["id"]; ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this doctor?');">
                                                        Delete
                                                    </a>


                                                    <!-- Activate / Deactivate -->
                                                    <?php if ($doctor["status"] == 1): ?>

                                                        <a
                                                            href="toggle_status.php?id=<?php echo $doctor["id"]; ?>"
                                                            class="btn btn-sm btn-secondary">
                                                            Deactivate
                                                        </a>

                                                    <?php else: ?>

                                                        <a
                                                            href="toggle_status.php?id=<?php echo $doctor["id"]; ?>"
                                                            class="btn btn-sm btn-success">
                                                            Activate
                                                        </a>

                                                    <?php endif; ?>

                                                </div>

                                            </td>

                                        </tr>

                                <?php

                                    }

                                } else {

                                ?>

                                    <tr>

                                        <td
                                            colspan="8"
                                            class="text-center text-muted py-4">

                                            No doctors found.

                                        </td>

                                    </tr>

                                <?php

                                }

                                $stmt->close();

                                ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once "../../includes/footer.php";

?>