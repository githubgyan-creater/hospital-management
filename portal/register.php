 <?php

session_start();

require_once "../config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $date_of_birth = $_POST["date_of_birth"] ?? "";
    $gender = trim($_POST["gender"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $address = trim($_POST["address"] ?? "");
    $blood_group = trim($_POST["blood_group"] ?? "");
    $emergency_contact = trim($_POST["emergency_contact"] ?? "");
    $medical_history = trim($_POST["medical_history"] ?? "");


    /* Validation */

    if (
        $name === "" ||
        $gender === "" ||
        $phone === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {

        $message = "Please fill all required fields.";
        $message_type = "danger";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $message_type = "danger";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "danger";

    } elseif (!preg_match("/^[0-9+\-\s]{10,20}$/", $phone)) {

        $message = "Please enter a valid phone number.";
        $message_type = "danger";

    } elseif (
        $email !== "" &&
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {

        $message = "Please enter a valid email address.";
        $message_type = "danger";

    } else {


        /* Generate patient code */

        $result = $conn->query(
            "SELECT id
             FROM patients
             ORDER BY id DESC
             LIMIT 1"
        );

        if ($result && $result->num_rows > 0) {

            $last_patient = $result->fetch_assoc();

            $next_id = (int)$last_patient["id"] + 1;

        } else {

            $next_id = 1;

        }

        $patient_code = "PAT-" . str_pad(
            $next_id,
            6,
            "0",
            STR_PAD_LEFT
        );


        /* Date */

        $date_of_birth_value = (
            $date_of_birth !== ""
        )
            ? $date_of_birth
            : null;


        /* Password encryption */

        $password_hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );


        /* Insert patient */

        $stmt = $conn->prepare(
            "INSERT INTO patients
            (
                patient_code,
                name,
                date_of_birth,
                gender,
                phone,
                email,
                portal_password,
                address,
                blood_group,
                emergency_contact,
                medical_history
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );


        if ($stmt) {

            $stmt->bind_param(
                "sssssssssss",
                $patient_code,
                $name,
                $date_of_birth_value,
                $gender,
                $phone,
                $email,
                $password_hash,
                $address,
                $blood_group,
                $emergency_contact,
                $medical_history
            );


            if ($stmt->execute()) {

                $new_patient_id = $stmt->insert_id;

                $_SESSION["portal_patient_id"] =
                    $new_patient_id;

                $_SESSION["portal_patient_code"] =
                    $patient_code;

                $_SESSION["portal_patient_name"] =
                    $name;

                $stmt->close();

                header(
                    "Location: registration_success.php"
                );

                exit();

            } else {

                $message =
                    "Unable to register patient. Please try again.";

                $message_type = "danger";
            }


            $stmt->close();

        } else {

            $message =
                "Database error. Please contact administrator.";

            $message_type = "danger";
        }
    }
}


require_once "../includes/header.php";

?>


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-9">

            <div class="card shadow-sm border-0">

                <div class="card-body p-4 p-md-5">


                    <div class="text-center mb-4">

                        <h2 class="fw-bold">
                            New Patient Registration
                        </h2>

                        <p class="text-muted">
                            Create your hospital patient profile
                        </p>

                    </div>


                    <?php if ($message !== ""): ?>

                        <div class="alert alert-<?php echo $message_type; ?>">

                            <?php
                            echo htmlspecialchars($message);
                            ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST">


                        <!-- Name -->

                        <div class="mb-3">

                            <label class="form-label">
                                Full Name
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                required
                                value="<?php echo htmlspecialchars($_POST["name"] ?? ""); ?>"
                            >

                        </div>


                        <div class="row">


                            <!-- Date of Birth -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Date of Birth
                                </label>

                                <input
                                    type="date"
                                    name="date_of_birth"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($_POST["date_of_birth"] ?? ""); ?>"
                                >

                            </div>


                            <!-- Gender -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Gender
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    name="gender"
                                    class="form-select"
                                    required
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


                            <!-- Phone -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Phone Number
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    maxlength="20"
                                    required
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
                                >

                            </div>


                            <!-- Password -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Password
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    minlength="6"
                                    required
                                >

                                <small class="text-muted">
                                    Minimum 6 characters
                                </small>

                            </div>


                            <!-- Confirm Password -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Confirm Password
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="password"
                                    name="confirm_password"
                                    class="form-control"
                                    minlength="6"
                                    required
                                >

                            </div>


                            <!-- Blood Group -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Blood Group
                                </label>

                                <select
                                    name="blood_group"
                                    class="form-select"
                                >

                                    <option value="">
                                        Select Blood Group
                                    </option>

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

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Emergency Contact
                                </label>

                                <input
                                    type="text"
                                    name="emergency_contact"
                                    class="form-control"
                                    maxlength="100"
                                >

                            </div>


                            <!-- Address -->

                            <div class="col-12 mb-3">

                                <label class="form-label">
                                    Address
                                </label>

                                <textarea
                                    name="address"
                                    class="form-control"
                                    rows="2"
                                ></textarea>

                            </div>


                            <!-- Medical History -->

                            <div class="col-12 mb-3">

                                <label class="form-label">
                                    Medical History
                                </label>

                                <textarea
                                    name="medical_history"
                                    class="form-control"
                                    rows="3"
                                ></textarea>

                            </div>


                        </div>


                        <div class="d-flex justify-content-between mt-4">

                            <a
                                href="index.php"
                                class="btn btn-secondary"
                            >
                                Back
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Register Patient
                            </button>

                        </div>


                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<?php

require_once "../includes/footer.php";

?>