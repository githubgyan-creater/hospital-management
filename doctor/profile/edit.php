<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "doctor") {

    header("Location: ../../index.php");
    exit;

}

require_once "../../config/database.php";


// Get logged-in doctor's email
$email = $_SESSION["user_email"];


// Get doctor details
$sql = "SELECT * FROM doctors WHERE email = ? LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

$doctor = $result->fetch_assoc();


// If doctor not found
if (!$doctor) {

    die("Doctor profile not found.");

}


// Update profile
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $specialization = $_POST["specialization"];
    $qualification = $_POST["qualification"];
    $consultation_fee = $_POST["consultation_fee"];
    $available_days = $_POST["available_days"];
    $available_time = $_POST["available_time"];


    $sql = "UPDATE doctors SET
                name = ?,
                phone = ?,
                specialization = ?,
                qualification = ?,
                consultation_fee = ?,
                available_days = ?,
                available_time = ?
            WHERE id = ?";


    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssssi",
        $name,
        $phone,
        $specialization,
        $qualification,
        $consultation_fee,
        $available_days,
        $available_time,
        $doctor["id"]
    );


    if ($stmt->execute()) {

        header("Location: index.php");
        exit;

    } else {

        $error = "Profile update failed.";

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

            <!-- Page Header -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Edit My Details
                    </h2>

                    <p class="text-muted mb-0">
                        Update your doctor profile
                    </p>

                </div>


                <a
                    href="index.php"
                    class="btn btn-secondary">

                    Back to Profile

                </a>

            </div>


            <?php if (isset($error)) { ?>

                <div class="alert alert-danger">

                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php } ?>


            <!-- Edit Form -->

            <div class="card shadow-sm">

                <div class="card-body">

                    <form method="POST">


                        <!-- Name -->

                        <div class="mb-3">

                            <label class="form-label">
                                Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="<?php echo htmlspecialchars($doctor["name"]); ?>"
                                required>

                        </div>


                        <!-- Email -->

                        <div class="mb-3">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                value="<?php echo htmlspecialchars($doctor["email"]); ?>"
                                readonly>

                        </div>


                        <!-- Phone -->

                        <div class="mb-3">

                            <label class="form-label">
                                Phone
                            </label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="<?php echo htmlspecialchars($doctor["phone"]); ?>"
                                required>

                        </div>


                        <!-- Specialization -->

                        <div class="mb-3">

                            <label class="form-label">
                                Specialization
                            </label>

                            <input
                                type="text"
                                name="specialization"
                                class="form-control"
                                value="<?php echo htmlspecialchars($doctor["specialization"]); ?>"
                                required>

                        </div>


                        <!-- Qualification -->

                        <div class="mb-3">

                            <label class="form-label">
                                Qualification
                            </label>

                            <input
                                type="text"
                                name="qualification"
                                class="form-control"
                                value="<?php echo htmlspecialchars($doctor["qualification"]); ?>">

                        </div>


                        <!-- Consultation Fee -->

                        <div class="mb-3">

                            <label class="form-label">
                                Consultation Fee
                            </label>

                            <input
                                type="number"
                                name="consultation_fee"
                                class="form-control"
                                value="<?php echo htmlspecialchars($doctor["consultation_fee"]); ?>"
                                min="0"
                                step="0.01"
                                required>

                        </div>


                        <!-- Available Days -->

                        <div class="mb-3">

                            <label class="form-label">
                                Available Days
                            </label>

                            <input
                                type="text"
                                name="available_days"
                                class="form-control"
                                value="<?php echo htmlspecialchars($doctor["available_days"]); ?>">

                        </div>


                        <!-- Available Time -->

                        <div class="mb-3">

                            <label class="form-label">
                                Available Time
                            </label>

                            <input
                                type="text"
                                name="available_time"
                                class="form-control"
                                value="<?php echo htmlspecialchars($doctor["available_time"]); ?>">

                        </div>


                        <!-- Update Button -->

                        <button
                            type="submit"
                            class="btn btn-primary">

                            Update My Details

                        </button>


                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<?php

require_once "../../includes/footer.php";

?>