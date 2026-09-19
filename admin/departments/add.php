<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $status = isset($_POST["status"]) ? 1 : 0;

    if ($name === "") {

        $error = "Department name is required.";

    } else {

        // Check whether department already exists
        $checkSql = "SELECT id
                     FROM departments
                     WHERE name = ?
                     LIMIT 1";

        $checkStmt = $conn->prepare($checkSql);

        $checkStmt->bind_param("s", $name);

        $checkStmt->execute();

        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {

            $error = "This department already exists.";

        } else {

            // Insert department
            $sql = "INSERT INTO departments
                    (name, description, status)
                    VALUES (?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssi",
                $name,
                $description,
                $status
            );

            if ($stmt->execute()) {

                $stmt->close();
                $checkStmt->close();

                header(
                    "Location: index.php?success=Department added successfully"
                );

                exit;

            } else {

                $error = "Department could not be added.";
            }

            $stmt->close();
        }

        $checkStmt->close();
    }
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
                        Add Department
                    </h2>

                    <p class="text-muted mb-0">
                        Create a new hospital department
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
                                placeholder="Enter department name"
                                required
                                value="<?php
                                echo isset($_POST["name"])
                                    ? htmlspecialchars($_POST["name"])
                                    : "";
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
                                rows="4"
                                placeholder="Enter department description"><?php
                                echo isset($_POST["description"])
                                    ? htmlspecialchars($_POST["description"])
                                    : "";
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
                                checked
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
                            Save Department
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