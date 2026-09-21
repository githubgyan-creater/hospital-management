<?php

session_start();

require_once "../config/database.php";

$message = "";
$message_type = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $patient_code = trim(
        $_POST["patient_code"] ?? ""
    );

    $password = $_POST["password"] ?? "";


    if (
        $patient_code === "" ||
        $password === ""
    ) {

        $message =
            "Please enter Patient ID and password.";

        $message_type = "danger";

    } else {


        $stmt = $conn->prepare(
            "SELECT
                id,
                patient_code,
                name,
                portal_password
             FROM patients
             WHERE patient_code = ?
             LIMIT 1"
        );


        $stmt->bind_param(
            "s",
            $patient_code
        );


        $stmt->execute();


        $result = $stmt->get_result();


        if ($result->num_rows === 1) {

            $patient = $result->fetch_assoc();


            if (
                !empty($patient["portal_password"]) &&
                password_verify(
                    $password,
                    $patient["portal_password"]
                )
            ) {


                $_SESSION["portal_patient_id"] =
                    $patient["id"];

                $_SESSION["portal_patient_code"] =
                    $patient["patient_code"];

                $_SESSION["portal_patient_name"] =
                    $patient["name"];


                header(
                    "Location: dashboard.php"
                );

                exit();


            } else {

                $message =
                    "Invalid Patient ID or password.";

                $message_type = "danger";
            }


        } else {

            $message =
                "Invalid Patient ID or password.";

            $message_type = "danger";
        }


        $stmt->close();
    }
}


require_once "../includes/header.php";

?>


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6 col-lg-5">

            <div class="card shadow-sm border-0">

                <div class="card-body p-4 p-md-5">


                    <div class="text-center mb-4">

                        <h2 class="fw-bold">
                            Patient Login
                        </h2>

                        <p class="text-muted">
                            Access your hospital patient portal
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


                        <div class="mb-3">

                            <label class="form-label">
                                Patient ID
                            </label>

                            <input
                                type="text"
                                name="patient_code"
                                class="form-control"
                                placeholder="PAT-000001"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Login
                        </button>


                    </form>


                    <div class="text-center mt-4">

                        <p class="mb-1">
                            New patient?
                        </p>

                        <a href="register.php">
                            Register here
                        </a>

                    </div>


                    <div class="text-center mt-3">

                        <a href="index.php">
                            ← Back to Patient Portal
                        </a>

                    </div>


                </div>

            </div>

        </div>

    </div>

</div>


<?php

require_once "../includes/footer.php";

?>