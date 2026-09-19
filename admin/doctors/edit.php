<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

$error = "";

/*
|--------------------------------------------------------------------------
| Check Doctor ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$doctorId = (int) $_GET["id"];

/*
|--------------------------------------------------------------------------
| Get Doctor
|--------------------------------------------------------------------------
*/

$sql = "SELECT *
        FROM doctors
        WHERE id = ?
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

/*
|--------------------------------------------------------------------------
| Update Doctor
|--------------------------------------------------------------------------
*/

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

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

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

        /*
        |--------------------------------------------------------------------------
        | Check Department
        |--------------------------------------------------------------------------
        */

        $departmentSql = "SELECT id
                          FROM departments
                          WHERE id = ?
                          LIMIT 1";

        $departmentStmt = $conn->prepare($departmentSql);

        $departmentStmt->bind_param(
            "i",
            $department_id
        );

        $departmentStmt->execute();

        $departmentResult = $departmentStmt->get_result();

        if ($departmentResult->num_rows !== 1) {

            $error = "Please select a valid department.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | Update Doctor
            |--------------------------------------------------------------------------
            */

            $sql = "UPDATE doctors
                    SET
                        name = ?,
                        email = ?,
                        phone = ?,
                        department_id = ?,
                        specialization = ?,
                        qualification = ?,
                        consultation_fee = ?,
                        available_days = ?,
                        available_time = ?,
                        status = ?
                    WHERE id = ?";

            $stmt = $conn->prepare($sql);

            $fee = (float) $consultation_fee;

            $stmt->bind_param(
                "sssissdssii",
                $name,
                $email,
                $phone,
                $department_id,
                $specialization,
                $qualification,
                $fee,
                $available_days,
                $available_time,
                $status,
                $doctorId
            );

            if ($stmt->execute()) {

                $stmt->close();
                $departmentStmt->close();

                header(
                    "Location: index.php?success=Doctor updated successfully"
                );

                exit;

            } else {

                $error = "Doctor could not be updated.";
            }

            $stmt->close();
        }

        $departmentStmt->close();
    }

    /*
    |--------------------------------------------------------------------------
    | Keep entered values if validation fails
    |--------------------------------------------------------------------------
    */

    $doctor["name"] = $name;
    $doctor["email"] = $email;
    $doctor["phone"] = $phone;
    $doctor["department_id"] = $department_id;
    $doctor["specialization"] = $specialization;
    $doctor["qualification"] = $qualification;
    $doctor["consultation_fee"] = $consultation_fee;
    $doctor["available_days"] = $available_days;
    $doctor["available_time"] = $available_time;
    $doctor["status"] = $status;
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
                        Edit Doctor
                    </h2>

                    <p class="text-muted mb-0">
                        Update doctor information
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
                                    echo htmlspecialchars(
                                        $doctor["name"]
                                    );
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
                                    echo htmlspecialchars(
                                        $doctor["email"] ?? ""
                                    );
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
                                    echo htmlspecialchars(
                                        $doctor["phone"]
                                    );
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
                                                      ORDER BY name ASC";

                                    $departmentStmt =
                                        $conn->prepare($departmentSql);

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
                                                $doctor["department_id"] ==
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
                                    value="<?php
                                    echo htmlspecialchars(
                                        $doctor["specialization"]
                                    );
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
                                    value="<?php
                                    echo htmlspecialchars(
                                        $doctor["qualification"] ?? ""
                                    );
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
                                    echo htmlspecialchars(
                                        $doctor["consultation_fee"]
                                    );
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
                                    value="<?php
                                    echo htmlspecialchars(
                                        $doctor["available_days"] ?? ""
                                    );
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
                                    value="<?php
                                    echo htmlspecialchars(
                                        $doctor["available_time"] ?? ""
                                    );
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
                                        <?php
                                        echo $doctor["status"] == 1
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

                            </div>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary">
                            Update Doctor
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