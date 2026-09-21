<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "doctor") {

    header("Location: ../../index.php");
    exit;

}

require_once "../../config/database.php";

require_once "../../includes/header.php";


// Get logged-in doctor's email
$email = $_SESSION["user_email"];


// Get doctor details
$sql = "SELECT 
            doctors.*,
            departments.name AS department_name
        FROM doctors
        LEFT JOIN departments 
            ON doctors.department_id = departments.id
        WHERE doctors.email = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

$doctor = $result->fetch_assoc();

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
                        My Profile
                    </h2>

                    <p class="text-muted mb-0">
                        View your doctor details
                    </p>

                </div>

                <a
                    href="edit.php"
                    class="btn btn-primary">

                    Edit My Details

                </a>

            </div>


            <!-- Profile Card -->

            <?php if ($doctor) { ?>

                <div class="card shadow-sm">

                    <div class="card-body">

                        <div class="row">


                            <!-- Name -->

                            <div class="col-md-6 mb-3">

                                <strong>Name</strong>

                                <p class="mb-0">
                                    <?php echo htmlspecialchars($doctor["name"]); ?>
                                </p>

                            </div>


                            <!-- Email -->

                            <div class="col-md-6 mb-3">

                                <strong>Email</strong>

                                <p class="mb-0">
                                    <?php echo htmlspecialchars($doctor["email"]); ?>
                                </p>

                            </div>


                            <!-- Phone -->

                            <div class="col-md-6 mb-3">

                                <strong>Phone</strong>

                                <p class="mb-0">
                                    <?php echo htmlspecialchars($doctor["phone"]); ?>
                                </p>

                            </div>


                            <!-- Department -->

                            <div class="col-md-6 mb-3">

                                <strong>Department</strong>

                                <p class="mb-0">
                                    <?php echo htmlspecialchars($doctor["department_name"]); ?>
                                </p>

                            </div>


                            <!-- Specialization -->

                            <div class="col-md-6 mb-3">

                                <strong>Specialization</strong>

                                <p class="mb-0">
                                    <?php echo htmlspecialchars($doctor["specialization"]); ?>
                                </p>

                            </div>


                            <!-- Qualification -->

                            <div class="col-md-6 mb-3">

                                <strong>Qualification</strong>

                                <p class="mb-0">
                                    <?php echo htmlspecialchars($doctor["qualification"]); ?>
                                </p>

                            </div>


                            <!-- Consultation Fee -->

                            <div class="col-md-6 mb-3">

                                <strong>Consultation Fee</strong>

                                <p class="mb-0">
                                    ₹<?php echo htmlspecialchars($doctor["consultation_fee"]); ?>
                                </p>

                            </div>


                            <!-- Available Days -->

                            <div class="col-md-6 mb-3">

                                <strong>Available Days</strong>

                                <p class="mb-0">
                                    <?php echo htmlspecialchars($doctor["available_days"]); ?>
                                </p>

                            </div>


                            <!-- Available Time -->

                            <div class="col-md-6 mb-3">

                                <strong>Available Time</strong>

                                <p class="mb-0">
                                    <?php echo htmlspecialchars($doctor["available_time"]); ?>
                                </p>

                            </div>


                            <!-- Status -->

                            <div class="col-md-6 mb-3">

                                <strong>Status</strong>

                                <p class="mb-0">

                                    <?php

                                    if ($doctor["status"] == 1) {

                                        echo "Active";

                                    } else {

                                        echo "Inactive";

                                    }

                                    ?>

                                </p>

                            </div>


                        </div>

                    </div>

                </div>

            <?php } else { ?>

                <div class="alert alert-warning">

                    Doctor profile not found.

                </div>

            <?php } ?>


        </div>

    </div>

</div>


<?php

require_once "../../includes/footer.php";

?>