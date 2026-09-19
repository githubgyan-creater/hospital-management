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
                        Departments
                    </h2>

                    <p class="text-muted mb-0">
                        Manage hospital departments
                    </p>

                </div>

                <a
                    href="add.php"
                    class="btn btn-primary">
                    + Add Department
                </a>

            </div>


            <!-- Department Table -->

            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-primary">

                                <tr>

                                    <th>ID</th>
                                    <th>Department Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                     <th style="min-width: 130px; white-space: nowrap;">
                                       Created Date
                                     </th>
                                    <th style="min-width: 300px;">
                                        Action
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php

                                $sql = "SELECT *
                                        FROM departments
                                        ORDER BY id DESC";

                                $stmt = $conn->prepare($sql);

                                $stmt->execute();

                                $result = $stmt->get_result();


                                if ($result->num_rows > 0) {

                                    while ($department = $result->fetch_assoc()) {

                                ?>

                                        <tr>

                                            <!-- ID -->

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $department["id"]
                                                );
                                                ?>
                                            </td>


                                            <!-- Department Name -->

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $department["name"]
                                                );
                                                ?>
                                            </td>


                                            <!-- Description -->

                                            <td>
                                                <?php

                                                echo htmlspecialchars(
                                                    $department["description"]
                                                    ?: "No description"
                                                );

                                                ?>
                                            </td>


                                            <!-- Status -->

                                            <td>

                                                <?php if ($department["status"] == 1): ?>

                                                    <span class="badge bg-success">
                                                        Active
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge bg-secondary">
                                                        Inactive
                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <!-- Created Date -->

                                             <td style="white-space: nowrap;">

    <?php

    echo date(
        "d-m-Y",
        strtotime(
            $department["created_at"]
        )
    );

    ?>

</td>


                                            <!-- Action -->

                                            <td>

                                                <div class="d-flex flex-wrap gap-1">

                                                    <!-- Edit -->

                                                    <a
                                                        href="edit.php?id=<?php echo $department["id"]; ?>"
                                                        class="btn btn-sm btn-warning">
                                                        Edit
                                                    </a>


                                                    <!-- Delete -->

                                                    <a
                                                        href="delete.php?id=<?php echo $department["id"]; ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this department?');">
                                                        Delete
                                                    </a>


                                                    <!-- Activate / Deactivate -->

                                                    <?php if ($department["status"] == 1): ?>

                                                        <a
                                                            href="toggle_status.php?id=<?php echo $department["id"]; ?>"
                                                            class="btn btn-sm btn-secondary">
                                                            Deactivate
                                                        </a>

                                                    <?php else: ?>

                                                        <a
                                                            href="toggle_status.php?id=<?php echo $department["id"]; ?>"
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
                                            colspan="6"
                                            class="text-center text-muted py-4">

                                            No departments found.

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