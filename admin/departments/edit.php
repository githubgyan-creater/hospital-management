 <?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

$error = "";
 

// Check Department ID

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$departmentId = (int) $_GET["id"];

// Get Department  


$sql = "SELECT *
        FROM departments
        WHERE id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $departmentId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    header("Location: index.php");
    exit;
}

$department = $result->fetch_assoc();

$stmt->close();

//  Update Department

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $status = isset($_POST["status"]) ? 1 : 0;

    if ($name === "") {

        $error = "Department name is required.";

    } else {

        /*
        Check duplicate department name
        */

        $checkSql = "SELECT id
                     FROM departments
                     WHERE name = ?
                     AND id != ?
                     LIMIT 1";

        $checkStmt = $conn->prepare($checkSql);

        $checkStmt->bind_param(
            "si",
            $name,
            $departmentId
        );

        $checkStmt->execute();

        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {

            $error = "This department already exists.";

        } else {

            /*
            Update department
            */

            $sql = "UPDATE departments
                    SET
                        name = ?,
                        description = ?,
                        status = ?
                    WHERE id = ?";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssii",
                $name,
                $description,
                $status,
                $departmentId
            );

            if ($stmt->execute()) {

                $stmt->close();
                $checkStmt->close();

                header(
                    "Location: index.php?success=Department updated successfully"
                );

                exit;

            } else {

                $error = "Department could not be updated.";
            }

            $stmt->close();
        }

        $checkStmt->close();
    }

    // Keep entered values if there is an error

    $department["name"] = $name;
    $department["description"] = $description;
    $department["status"] = $status;
}

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
                        Edit Department
                    </h2>

                    <p class="text-muted mb-0">
                        Update department information
                    </p>

                </div>

                <a href="index.php" class="btn btn-secondary">
                    Back to Departments
                </a>

            </div>

            <?php if ($error !== ""): ?>

                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>

            <div class="card shadow-sm">

                <div class="card-body">

                    <form method="POST">

                        <!-- Department Name -->

                        <div class="mb-3">

                            <label class="form-label">
                                Department Name *
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                required
                                value="<?php
                                echo htmlspecialchars(
                                    $department["name"]
                                );
                                ?>"
                            >

                        </div>

                        <!-- Description -->

                        <div class="mb-3">

                            <label class="form-label">
                                Description
                            </label>

                            <textarea
                                name="description"
                                class="form-control"
                                rows="4"><?php
                                echo htmlspecialchars(
                                    $department["description"] ?? ""
                                );
                                ?></textarea>

                        </div>

                        <!-- Status -->

                        <div class="form-check mb-4">

                            <input
                                type="checkbox"
                                name="status"
                                value="1"
                                class="form-check-input"
                                id="status"
                                <?php
                                echo $department["status"] == 1
                                    ? "checked"
                                    : "";
                                ?>
                            >

                            <label
                                class="form-check-label"
                                for="status">

                                Active

                            </label>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary">
                            Update Department
                        </button>

                        <a
                            href="index.php"
                            class="btn btn-secondary">
                            Cancel
                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once "../../includes/footer.php";

?>