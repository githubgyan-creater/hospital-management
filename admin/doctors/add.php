<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

$error = "";

// Add Doctor 


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $department_id = (int) $_POST["department_id"];
    $specialization = trim($_POST["specialization"]);
    $qualification = trim($_POST["qualification"]);
    $consultation_fee = trim($_POST["consultation_fee"]);
    $available_days = trim($_POST["available_days"]);
    $available_time = trim($_POST["available_time"]);
    $status = isset($_POST["status"]) ? 1 : 0;

    
    // Validation

    if (
        $name === "" ||
        $phone === "" ||
        $department_id <= 0 ||
        $specialization === "" ||
        $consultation_fee === ""
    ) {

        $error = "Please fill all required fields.";

    } elseif (
        $email !== "" &&
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {

        $error = "Please enter a valid email address.";

    } elseif (
        !is_numeric($consultation_fee) ||
        $consultation_fee < 0
    ) {

        $error = "Please enter a valid consultation fee.";

    } elseif (
        !preg_match("/^[0-9+\-\s()]{7,20}$/", $phone)
    ) {

        $error = "Please enter a valid phone number.";

    } else {

         
// Check Department


        $departmentCheck = "SELECT id
                            FROM departments
                            WHERE id = ?
                            AND status = 1
                            LIMIT 1";

        $departmentStmt = $conn->prepare($departmentCheck);

        $departmentStmt->bind_param(
            "i",
            $department_id
        );

        $departmentStmt->execute();

        $departmentResult = $departmentStmt->get_result();

        if ($departmentResult->num_rows !== 1) {

            $error = "Please select a valid active department.";

        } else {

        // Insert Doctor 


            $sql = "INSERT INTO doctors
                    (
                        name,
                        email,
                        phone,
                        department_id,
                        specialization,
                        qualification,
                        consultation_fee,
                        available_days,
                        available_time,
                        status
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $fee = (float) $consultation_fee;

            $stmt->bind_param(
                "sssissdssi",
                $name,
                $email,
                $phone,
                $department_id,
                $specialization,
                $qualification,
                $fee,
                $available_days,
                $available_time,
                $status
            );

            if ($stmt->execute()) {

                $stmt->close();
                $departmentStmt->close();

                header(
                    "Location: index.php?success=Doctor added successfully"
                );

                exit;

            } else {

                $error = "Doctor could not be added.";
            }

            $stmt->close();
        }

        $departmentStmt->close();
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
                        Add Doctor
                    </h2>

                    <p class="text-muted mb-0">
                        Register a new hospital doctor
                    </p>

                </div>

                <a href="index.php" class="btn btn-secondary">
                    Back to Doctors
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

                        <div class="row">

                            <!-- Doctor Name -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Doctor Name *
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    required
                                    value="<?php
                                    echo isset($_POST["name"])
                                        ? htmlspecialchars($_POST["name"])
                                        : "";
                                    ?>"
                                >

                            </div>


                            <!-- Email -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?php
                                    echo isset($_POST["email"])
                                        ? htmlspecialchars($_POST["email"])
                                        : "";
                                    ?>"
                                >

                            </div>


                            <!-- Phone -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Phone *
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    required
                                    value="<?php
                                    echo isset($_POST["phone"])
                                        ? htmlspecialchars($_POST["phone"])
                                        : "";
                                    ?>"
                                >

                            </div>


                            <!-- Department -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Department *
                                </label>

                                <select
                                    name="department_id"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        Select Department
                                    </option>

                                    <?php

                                    $departmentSql = "SELECT id, name
                                                      FROM departments
                                                      WHERE status = 1
                                                      ORDER BY name ASC";

                                    $departmentStmt = $conn->prepare(
                                        $departmentSql
                                    );

                                    $departmentStmt->execute();

                                    $departmentResult =
                                        $departmentStmt->get_result();

                                    while (
                                        $department =
                                        $departmentResult->fetch_assoc()
                                    ):

                                    ?>

                                        <option
                                            value="<?php echo $department["id"]; ?>"
                                            <?php
                                            echo (
                                                isset($_POST["department_id"]) &&
                                                $_POST["department_id"] ==
                                                $department["id"]
                                            )
                                                ? "selected"
                                                : "";
                                            ?>
                                        >
                                            <?php
                                            echo htmlspecialchars(
                                                $department["name"]
                                            );
                                            ?>
                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>


                            <!-- Specialization -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Specialization *
                                </label>

                                <input
                                    type="text"
                                    name="specialization"
                                    class="form-control"
                                    required
                                    placeholder="e.g. Cardiologist"
                                    value="<?php
                                    echo isset($_POST["specialization"])
                                        ? htmlspecialchars(
                                            $_POST["specialization"]
                                        )
                                        : "";
                                    ?>"
                                >

                            </div>


                            <!-- Qualification -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Qualification
                                </label>

                                <input
                                    type="text"
                                    name="qualification"
                                    class="form-control"
                                    placeholder="e.g. MBBS, MD"
                                    value="<?php
                                    echo isset($_POST["qualification"])
                                        ? htmlspecialchars(
                                            $_POST["qualification"]
                                        )
                                        : "";
                                    ?>"
                                >

                            </div>


                            <!-- Consultation Fee -->

                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Consultation Fee *
                                </label>

                                <input
                                    type="number"
                                    name="consultation_fee"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    required
                                    value="<?php
                                    echo isset($_POST["consultation_fee"])
                                        ? htmlspecialchars(
                                            $_POST["consultation_fee"]
                                        )
                                        : "";
                                    ?>"
                                >

                            </div>


                            <!-- Available Days -->

                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Available Days
                                </label>

                                <input
                                    type="text"
                                    name="available_days"
                                    class="form-control"
                                    placeholder="e.g. Mon, Wed, Fri"
                                    value="<?php
                                    echo isset($_POST["available_days"])
                                        ? htmlspecialchars(
                                            $_POST["available_days"]
                                        )
                                        : "";
                                    ?>"
                                >

                            </div>


                            <!-- Available Time -->

                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Available Time
                                </label>

                                <input
                                    type="text"
                                    name="available_time"
                                    class="form-control"
                                    placeholder="e.g. 10:00 AM - 2:00 PM"
                                    value="<?php
                                    echo isset($_POST["available_time"])
                                        ? htmlspecialchars(
                                            $_POST["available_time"]
                                        )
                                        : "";
                                    ?>"
                                >

                            </div>


                            <!-- Status -->

                            <div class="col-md-12 mb-4">

                                <div class="form-check">

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

                            </div>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary">
                            Save Doctor
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