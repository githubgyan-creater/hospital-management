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

    if ($name === "" || $age === "" || $gender === "" || $phone === "") {

        $error = "Please fill all required fields.";

    } elseif (!is_numeric($age) || $age < 0 || $age > 120) {

        $error = "Please enter a valid age.";

    } elseif (!preg_match("/^[0-9+\-\s()]{7,20}$/", $phone)) {

        $error = "Please enter a valid phone number.";

    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        /*
        --------------------------------------------------
        Generate Patient Code
        Example: PAT-000001
        --------------------------------------------------
        */

        $result = $conn->query(
            "SELECT id FROM patients ORDER BY id DESC LIMIT 1"
        );

        if ($result->num_rows > 0) {

            $lastPatient = $result->fetch_assoc();

            $nextId = $lastPatient["id"] + 1;

        } else {

            $nextId = 1;
        }

        $patientCode = "PAT-" . str_pad($nextId, 6, "0", STR_PAD_LEFT);

        /*
        --------------------------------------------------
        Insert Patient
        --------------------------------------------------
        */

        $sql = "INSERT INTO patients
                (
                    patient_code,
                    name,
                    date_of_birth,
                    age,
                    gender,
                    phone,
                    email,
                    address,
                    blood_group,
                    emergency_contact,
                    medical_history
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssisssssss",
            $patientCode,
            $name,
            $date_of_birth,
            $age,
            $gender,
            $phone,
            $email,
            $address,
            $blood_group,
            $emergency_contact,
            $medical_history
        );

        if ($stmt->execute()) {

            header("Location: index.php?success=Patient added successfully");
            exit;

        } else {

            $error = "Patient could not be added.";
        }

        $stmt->close();
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
                    <h2 class="fw-bold mb-1">Add Patient</h2>

                    <p class="text-muted mb-0">
                        Register a new patient
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
                                    value="<?php echo isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : ""; ?>"
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
                                    value="<?php echo isset($_POST["age"]) ? htmlspecialchars($_POST["age"]) : ""; ?>"
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

                                    <option value="">Select Gender</option>

                                    <option value="Male"
                                        <?php echo (isset($_POST["gender"]) && $_POST["gender"] === "Male") ? "selected" : ""; ?>>
                                        Male
                                    </option>

                                    <option value="Female"
                                        <?php echo (isset($_POST["gender"]) && $_POST["gender"] === "Female") ? "selected" : ""; ?>>
                                        Female
                                    </option>

                                    <option value="Other"
                                        <?php echo (isset($_POST["gender"]) && $_POST["gender"] === "Other") ? "selected" : ""; ?>>
                                        Other
                                    </option>

                                </select>

                            </div>

                            <!-- Date of Birth -->
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Date of Birth
                                </label>

                                <input
                                    type="date"
                                    name="date_of_birth"
                                    class="form-control"
                                    value="<?php echo isset($_POST["date_of_birth"]) ? htmlspecialchars($_POST["date_of_birth"]) : ""; ?>"
                                >

                            </div>

                            <!-- Phone -->
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Phone *
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    required
                                    value="<?php echo isset($_POST["phone"]) ? htmlspecialchars($_POST["phone"]) : ""; ?>"
                                >

                            </div>

                            <!-- Email -->
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?php echo isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : ""; ?>"
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
                                    rows="2"><?php echo isset($_POST["address"]) ? htmlspecialchars($_POST["address"]) : ""; ?></textarea>

                            </div>

                            <!-- Blood Group -->
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Blood Group
                                </label>

                                <select
                                    name="blood_group"
                                    class="form-select">

                                    <option value="">Select Blood Group</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>

                                </select>

                            </div>

                            <!-- Emergency Contact -->
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Emergency Contact
                                </label>

                                <input
                                    type="text"
                                    name="emergency_contact"
                                    class="form-control"
                                >

                            </div>

                            <!-- Medical History -->
                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Medical History
                                </label>

                                <textarea
                                    name="medical_history"
                                    class="form-control"
                                    rows="4"></textarea>

                            </div>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary">
                            Save Patient
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