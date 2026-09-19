<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";

// Check Patient ID  


if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$patientId = (int) $_GET["id"];

//  Get Patient

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

$error = "";

//  Update Patient

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $age = trim($_POST["age"]);
    $gender = trim($_POST["gender"]);

    $date_of_birth = !empty($_POST["date_of_birth"])
        ? $_POST["date_of_birth"]
        : null;

    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $address = trim($_POST["address"]);
    $blood_group = trim($_POST["blood_group"]);
    $emergency_contact = trim($_POST["emergency_contact"]);
    $medical_history = trim($_POST["medical_history"]);

    // Validation

    if (
        $name === "" ||
        $age === "" ||
        $gender === "" ||
        $phone === ""
    ) {

        $error = "Please fill all required fields.";

    } elseif (!is_numeric($age) || $age < 0 || $age > 120) {

        $error = "Please enter a valid age.";

    } elseif (!preg_match("/^[0-9+\-\s()]{7,20}$/", $phone)) {

        $error = "Please enter a valid phone number.";

    } elseif (
        $email !== "" &&
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {

        $error = "Please enter a valid email address.";

    } else {

        
// Update Query

        $sql = "UPDATE patients
                SET
                    name = ?,
                    date_of_birth = ?,
                    age = ?,
                    gender = ?,
                    phone = ?,
                    email = ?,
                    address = ?,
                    blood_group = ?,
                    emergency_contact = ?,
                    medical_history = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssisssssssi",
            $name,
            $date_of_birth,
            $age,
            $gender,
            $phone,
            $email,
            $address,
            $blood_group,
            $emergency_contact,
            $medical_history,
            $patientId
        );

        if ($stmt->execute()) {

            $stmt->close();

            header(
                "Location: view.php?id=" .
                $patientId .
                "&success=Patient updated successfully"
            );

            exit;

        } else {

            $error = "Patient could not be updated.";
        }

        $stmt->close();
    }

//   Keep Entered Values After Validation Error

    $patient["name"] = $name;
    $patient["age"] = $age;
    $patient["gender"] = $gender;
    $patient["date_of_birth"] = $date_of_birth;
    $patient["phone"] = $phone;
    $patient["email"] = $email;
    $patient["address"] = $address;
    $patient["blood_group"] = $blood_group;
    $patient["emergency_contact"] = $emergency_contact;
    $patient["medical_history"] = $medical_history;
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
                        Edit Patient
                    </h2>

                    <p class="text-muted mb-0">
                        Update patient information
                    </p>

                </div>

                <a href="index.php" class="btn btn-secondary">
                    Back to Patients
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

                            <!-- Patient ID -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Patient ID
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $patient["patient_code"]
                                    );
                                    ?>"
                                    readonly
                                >

                            </div>

                            <!-- Patient Name -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Patient Name *
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    required
                                    value="<?php
                                    echo htmlspecialchars(
                                        $patient["name"]
                                    );
                                    ?>"
                                >

                            </div>

                            <!-- Age -->
                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Age *
                                </label>

                                <input
                                    type="number"
                                    name="age"
                                    class="form-control"
                                    min="0"
                                    max="120"
                                    required
                                    value="<?php
                                    echo htmlspecialchars(
                                        $patient["age"]
                                    );
                                    ?>"
                                >

                            </div>

                            <!-- Gender -->
                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Gender *
                                </label>

                                <select
                                    name="gender"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        Select Gender
                                    </option>

                                    <option value="Male"
                                        <?php
                                        echo $patient["gender"] === "Male"
                                            ? "selected"
                                            : "";
                                        ?>>
                                        Male
                                    </option>

                                    <option value="Female"
                                        <?php
                                        echo $patient["gender"] === "Female"
                                            ? "selected"
                                            : "";
                                        ?>>
                                        Female
                                    </option>

                                    <option value="Other"
                                        <?php
                                        echo $patient["gender"] === "Other"
                                            ? "selected"
                                            : "";
                                        ?>>
                                        Other
                                    </option>

                                </select>

                            </div>

                            <!-- Date of Birth -->
                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Date of Birth
                                </label>

                                <input
                                    type="date"
                                    name="date_of_birth"
                                    class="form-control"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $patient["date_of_birth"] ?? ""
                                    );
                                    ?>"
                                >

                            </div>

                            <!-- Phone -->
                            <div class="col-md-3 mb-3">

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
                                        $patient["phone"]
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
                                        $patient["email"] ?? ""
                                    );
                                    ?>"
                                >

                            </div>

                            <!-- Blood Group -->
                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Blood Group
                                </label>

                                <select
                                    name="blood_group"
                                    class="form-select">

                                    <option value="">
                                        Select Blood Group
                                    </option>

                                    <?php

                                    $bloodGroups = [
                                        "A+",
                                        "A-",
                                        "B+",
                                        "B-",
                                        "AB+",
                                        "AB-",
                                        "O+",
                                        "O-"
                                    ];

                                    foreach ($bloodGroups as $group):

                                    ?>

                                        <option
                                            value="<?php echo $group; ?>"
                                            <?php
                                            echo $patient["blood_group"] === $group
                                                ? "selected"
                                                : "";
                                            ?>>
                                            <?php echo $group; ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <!-- Emergency Contact -->
                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Emergency Contact
                                </label>

                                <input
                                    type="text"
                                    name="emergency_contact"
                                    class="form-control"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $patient["emergency_contact"] ?? ""
                                    );
                                    ?>"
                                >

                            </div>

                            <!-- Address -->
                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Address
                                </label>

                                <textarea
                                    name="address"
                                    class="form-control"
                                    rows="3"><?php
                                    echo htmlspecialchars(
                                        $patient["address"] ?? ""
                                    );
                                    ?></textarea>

                            </div>

                            <!-- Medical History -->
                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Medical History
                                </label>

                                <textarea
                                    name="medical_history"
                                    class="form-control"
                                    rows="4"><?php
                                    echo htmlspecialchars(
                                        $patient["medical_history"] ?? ""
                                    );
                                    ?></textarea>

                            </div>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary">
                            Update Patient
                        </button>

                        <a
                            href="view.php?id=<?php echo $patientId; ?>"
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