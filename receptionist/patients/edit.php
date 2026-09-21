 <?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "receptionist") {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/database.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    die("Patient not found.");
}

/* Update patient */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = $_POST["name"];
    $date_of_birth = $_POST["date_of_birth"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $blood_group = $_POST["blood_group"];
    $emergency_contact = $_POST["emergency_contact"];
    $medical_history = $_POST["medical_history"];

    $update = $conn->prepare("
        UPDATE patients SET
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
        WHERE id = ?
    ");

    $update->bind_param(
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
        $id
    );

    if ($update->execute()) {
        header("Location: view.php?id=" . $id);
        exit();
    } else {
        echo "Error updating patient.";
    }
}

require_once "../../includes/header.php";

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
                    <h2>Edit Patient</h2>

                    <a href="view.php?id=<?php echo $patient["id"]; ?>"
                       class="btn btn-secondary">
                        Back
                    </a>
                </div>

                <div class="card">

                    <div class="card-header bg-warning">
                        <strong>Edit Patient Information</strong>
                    </div>

                    <div class="card-body">

                        <form method="POST">

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Patient ID</label>

                                    <input type="text"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($patient["patient_code"]); ?>"
                                           readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Patient Name</label>

                                    <input type="text"
                                           name="name"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($patient["name"]); ?>"
                                           required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>

                                    <input type="date"
                                           name="date_of_birth"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($patient["date_of_birth"]); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Age</label>

                                    <input type="number"
                                           name="age"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($patient["age"]); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>

                                    <select name="gender" class="form-control">

                                        <option value="Male"
                                            <?php if ($patient["gender"] == "Male") echo "selected"; ?>>
                                            Male
                                        </option>

                                        <option value="Female"
                                            <?php if ($patient["gender"] == "Female") echo "selected"; ?>>
                                            Female
                                        </option>

                                        <option value="Other"
                                            <?php if ($patient["gender"] == "Other") echo "selected"; ?>>
                                            Other
                                        </option>

                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>

                                    <input type="text"
                                           name="phone"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($patient["phone"]); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>

                                    <input type="email"
                                           name="email"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($patient["email"]); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Blood Group</label>

                                    <input type="text"
                                           name="blood_group"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($patient["blood_group"]); ?>">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Address</label>

                                    <textarea name="address"
                                              class="form-control"
                                              rows="2"><?php echo htmlspecialchars($patient["address"]); ?></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Emergency Contact</label>

                                    <input type="text"
                                           name="emergency_contact"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($patient["emergency_contact"]); ?>">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Medical History</label>

                                    <textarea name="medical_history"
                                              class="form-control"
                                              rows="3"><?php echo htmlspecialchars($patient["medical_history"]); ?></textarea>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary">
                                Update Patient
                            </button>

                            <a href="view.php?id=<?php echo $patient["id"]; ?>"
                               class="btn btn-secondary">
                                Cancel
                            </a>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>