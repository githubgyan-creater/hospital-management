 <?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "receptionist") {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/database.php";
require_once "../../includes/header.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $date_of_birth = $_POST["date_of_birth"];
    $age = intval($_POST["age"]);
    $gender = $_POST["gender"];
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $address = trim($_POST["address"]);
    $blood_group = trim($_POST["blood_group"]);
    $emergency_contact = trim($_POST["emergency_contact"]);
    $medical_history = trim($_POST["medical_history"]);

    if ($name === "") {

        $error = "Patient name is required.";

    } else {

        $code_result = $conn->query(
            "SELECT id FROM patients ORDER BY id DESC LIMIT 1"
        );

        if ($code_result && $code_result->num_rows > 0) {

            $last_patient = $code_result->fetch_assoc();
            $next_id = $last_patient["id"] + 1;

        } else {

            $next_id = 1;

        }

        $patient_code = "PAT-" . str_pad($next_id, 6, "0", STR_PAD_LEFT);

        $stmt = $conn->prepare("
            INSERT INTO patients
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
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssisssssss",
            $patient_code,
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

            header("Location: index.php");
            exit();

        } else {

            $error = "Failed to add patient: " . $stmt->error;

        }
    }
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

                    <h2>Add Patient</h2>

                    <a href="index.php" class="btn btn-secondary">
                        Back to Patients
                    </a>

                </div>


                <?php if ($error !== ""): ?>

                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>

                <?php endif; ?>


                <div class="card">

                    <div class="card-header bg-dark text-white">

                        <strong>Patient Information</strong>

                    </div>


                    <div class="card-body">

                        <form method="POST">

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Patient Name
                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control"
                                        required
                                    >

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Date of Birth
                                    </label>

                                    <input
                                        type="date"
                                        name="date_of_birth"
                                        class="form-control"
                                    >

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Age
                                    </label>

                                    <input
                                        type="number"
                                        name="age"
                                        class="form-control"
                                        min="0"
                                        max="150"
                                    >

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Gender
                                    </label>

                                    <select
                                        name="gender"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Gender
                                        </option>

                                        <option value="Male">
                                            Male
                                        </option>

                                        <option value="Female">
                                            Female
                                        </option>

                                        <option value="Other">
                                            Other
                                        </option>

                                    </select>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Phone
                                    </label>

                                    <input
                                        type="text"
                                        name="phone"
                                        class="form-control"
                                    >

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Email
                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                    >

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Blood Group
                                    </label>

                                    <input
                                        type="text"
                                        name="blood_group"
                                        class="form-control"
                                        placeholder="Example: O+"
                                    >

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Emergency Contact
                                    </label>

                                    <input
                                        type="text"
                                        name="emergency_contact"
                                        class="form-control"
                                    >

                                </div>


                                <div class="col-md-12 mb-3">

                                    <label class="form-label">
                                        Address
                                    </label>

                                    <textarea
                                        name="address"
                                        class="form-control"
                                        rows="3"
                                    ></textarea>

                                </div>


                                <div class="col-md-12 mb-3">

                                    <label class="form-label">
                                        Medical History
                                    </label>

                                    <textarea
                                        name="medical_history"
                                        class="form-control"
                                        rows="4"
                                    ></textarea>

                                </div>

                            </div>


                            <button
                                type="submit"
                                class="btn btn-success"
                            >
                                Add Patient
                            </button>

                            <a
                                href="index.php"
                                class="btn btn-secondary"
                            >
                                Cancel
                            </a>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>