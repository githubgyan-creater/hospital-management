<?php

session_start();

require_once "../config/database.php";

if (
    !isset($_SESSION["portal_patient_id"]) ||
    !isset($_SESSION["portal_patient_code"])
) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION["portal_patient_id"];
$patient_code = $_SESSION["portal_patient_code"];

$stmt = $conn->prepare(
    "SELECT name, phone, email
     FROM patients
     WHERE id = ?"
);

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$result = $stmt->get_result();

$patient = $result->fetch_assoc();

$stmt->close();

require_once "../includes/header.php";

?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card shadow-sm border-0">

                <div class="card-body p-5 text-center">

                    <div class="mb-4">

                        <div
                            class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center"
                            style="width:70px;height:70px;"
                        >

                            <span style="font-size:32px;">
                                ✓
                            </span>

                        </div>

                    </div>


                    <h2 class="fw-bold text-success">
                        Registration Successful
                    </h2>


                    <p class="text-muted mt-3">
                        Your patient profile has been created successfully.
                    </p>


                    <div class="alert alert-primary mt-4">

                        <div class="small text-muted">
                            Your Patient ID
                        </div>

                        <div class="fs-3 fw-bold">
                            <?php echo htmlspecialchars($patient_code); ?>
                        </div>

                    </div>


                    <?php if ($patient): ?>

                        <div class="text-start mt-4">

                            <p>
                                <strong>Name:</strong>
                                <?php echo htmlspecialchars($patient["name"]); ?>
                            </p>

                            <p>
                                <strong>Phone:</strong>
                                <?php echo htmlspecialchars($patient["phone"]); ?>
                            </p>

                            <?php if (!empty($patient["email"])): ?>

                                <p>
                                    <strong>Email:</strong>
                                    <?php echo htmlspecialchars($patient["email"]); ?>
                                </p>

                            <?php endif; ?>

                        </div>

                    <?php endif; ?>


                    <div class="alert alert-warning text-start mt-4">

                        <strong>Important:</strong>

                        Please keep your Patient ID safe.
                        It will be used for your future hospital visits.

                    </div>


                    <a
                        href="dashboard.php"
                        class="btn btn-primary mt-3"
                    >
                        Continue to Patient Portal
                    </a>


                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once "../includes/footer.php";

?>