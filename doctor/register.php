<?php

session_start();

require_once "../config/database.php";


/*
|--------------------------------------------------------------------------
| Get Departments
|--------------------------------------------------------------------------
*/

$departmentSql = "
    SELECT id, name
    FROM departments
    WHERE status = 1
    ORDER BY name ASC
";

$departmentResult = $conn->query($departmentSql);


$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Handle Registration
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $department_id = (int) ($_POST["department_id"] ?? 0);
    $specialization = trim($_POST["specialization"] ?? "");
    $qualification = trim($_POST["qualification"] ?? "");
    $consultation_fee = trim($_POST["consultation_fee"] ?? "");
    $available_days = trim($_POST["available_days"] ?? "");
    $available_time = trim($_POST["available_time"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | Basic Validation
    |--------------------------------------------------------------------------
    */

    if (
        $name === "" ||
        $email === "" ||
        $phone === "" ||
        $department_id <= 0 ||
        $specialization === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {

        $error = "Please fill all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $error = "Password must contain at least 6 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Password and confirm password do not match.";

    } elseif ($consultation_fee !== "" && !is_numeric($consultation_fee)) {

        $error = "Consultation fee must be a valid number.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | Check Email in Users Table
        |--------------------------------------------------------------------------
        */

        $userCheckSql = "
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ";

        $userCheckStmt = $conn->prepare($userCheckSql);

        $userCheckStmt->bind_param(
            "s",
            $email
        );

        $userCheckStmt->execute();

        $userCheckResult = $userCheckStmt->get_result();

        if ($userCheckResult->num_rows > 0) {

            $error = "This email is already registered.";

        }

        $userCheckStmt->close();


        /*
        |--------------------------------------------------------------------------
        | Check Email in Doctors Table
        |--------------------------------------------------------------------------
        */

        if ($error === "") {

            $doctorCheckSql = "
                SELECT id
                FROM doctors
                WHERE email = ?
                LIMIT 1
            ";

            $doctorCheckStmt = $conn->prepare($doctorCheckSql);

            $doctorCheckStmt->bind_param(
                "s",
                $email
            );

            $doctorCheckStmt->execute();

            $doctorCheckResult = $doctorCheckStmt->get_result();

            if ($doctorCheckResult->num_rows > 0) {

                $error = "A doctor profile already exists with this email.";

            }

            $doctorCheckStmt->close();
        }


        /*
        |--------------------------------------------------------------------------
        | Check Department
        |--------------------------------------------------------------------------
        */

        if ($error === "") {

            $departmentCheckSql = "
                SELECT id
                FROM departments
                WHERE id = ?
                AND status = 1
                LIMIT 1
            ";

            $departmentCheckStmt = $conn->prepare(
                $departmentCheckSql
            );

            $departmentCheckStmt->bind_param(
                "i",
                $department_id
            );

            $departmentCheckStmt->execute();

            $departmentCheckResult =
                $departmentCheckStmt->get_result();

            if ($departmentCheckResult->num_rows !== 1) {

                $error = "Please select a valid department.";

            }

            $departmentCheckStmt->close();
        }


        /*
        |--------------------------------------------------------------------------
        | Create Doctor Account
        |--------------------------------------------------------------------------
        */

        if ($error === "") {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            /*
            |--------------------------------------------------------------
            | Start Database Transaction
            |--------------------------------------------------------------
            */

            $conn->begin_transaction();


            try {

                /*
                |--------------------------------------------------------------------------
                | Insert Login Account
                |--------------------------------------------------------------------------
                */

                $userSql = "
                    INSERT INTO users
                    (
                        name,
                        email,
                        password,
                        role,
                        status
                    )
                    VALUES (?, ?, ?, 'doctor', 1)
                ";

                $userStmt = $conn->prepare($userSql);

                $userStmt->bind_param(
                    "sss",
                    $name,
                    $email,
                    $hashedPassword
                );

                if (!$userStmt->execute()) {

                    throw new Exception(
                        "Doctor login account could not be created."
                    );

                }

                $userStmt->close();


                /*
                |--------------------------------------------------------------------------
                | Insert Doctor Profile
                |--------------------------------------------------------------------------
                */

                $fee = $consultation_fee !== ""
                    ? (float) $consultation_fee
                    : 0.00;


                $doctorSql = "
                    INSERT INTO doctors
                    (
                        name,
                        email,
                        phone,
                        department_id,
                        specialization,
                        qualification,
                        consultation_fee,
                        available_days,
                        available_time,
                        status
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
                ";

                $doctorStmt = $conn->prepare($doctorSql);

                $doctorStmt->bind_param(
                    "sssissdss",
                    $name,
                    $email,
                    $phone,
                    $department_id,
                    $specialization,
                    $qualification,
                    $fee,
                    $available_days,
                    $available_time
                );

                if (!$doctorStmt->execute()) {

                    throw new Exception(
                        "Doctor profile could not be created."
                    );

                }

                $doctorStmt->close();


                /*
                |--------------------------------------------------------------------------
                | Complete Transaction
                |--------------------------------------------------------------------------
                */

                $conn->commit();


                /*
                |--------------------------------------------------------------------------
                | Redirect to Login
                |--------------------------------------------------------------------------
                */

                header(
                    "Location: login.php?registered=1"
                );

                exit();


            } catch (Exception $e) {

                /*
                |--------------------------------------------------------------------------
                | Rollback if Something Goes Wrong
                |--------------------------------------------------------------------------
                */

                $conn->rollback();

                $error = "Doctor registration could not be completed.";
            }
        }
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Doctor Registration
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body class="bg-light">


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">


            <div class="card shadow-sm border-0">


                <div class="card-header bg-primary text-white">

                    <h3 class="fw-bold mb-0">
                        Doctor Registration
                    </h3>

                </div>


                <div class="card-body p-4">


                    <?php if ($error !== ""): ?>

                        <div class="alert alert-danger">

                            <?php
                            echo htmlspecialchars($error);
                            ?>

                        </div>

                    <?php endif; ?>


                    <div class="alert alert-info">

                        Register your doctor profile and create
                        your doctor login account.

                    </div>


                    <form
                        method="POST"
                        action=""
                    >


                        <!-- Doctor Name -->

                        <div class="mb-3">

                            <label class="form-label">

                                Doctor Name *

                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["name"] ?? ""
                                );
                                ?>"
                                required
                            >

                        </div>


                        <!-- Email -->

                        <div class="mb-3">

                            <label class="form-label">

                                Email *

                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["email"] ?? ""
                                );
                                ?>"
                                required
                            >

                            <small class="text-muted">

                                This email will be used for doctor login.

                            </small>

                        </div>


                        <!-- Phone -->

                        <div class="mb-3">

                            <label class="form-label">

                                Phone *

                            </label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["phone"] ?? ""
                                );
                                ?>"
                                required
                            >

                        </div>


                        <!-- Department -->

                        <div class="mb-3">

                            <label class="form-label">

                                Department *

                            </label>

                            <select
                                name="department_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Select Department
                                </option>


                                <?php while (
                                    $department =
                                    $departmentResult->fetch_assoc()
                                ): ?>

                                    <option
                                        value="<?php
                                        echo $department["id"];
                                        ?>"
                                        <?php
                                        if (
                                            isset(
                                                $_POST["department_id"]
                                            ) &&
                                            $_POST["department_id"]
                                            == $department["id"]
                                        ) {
                                            echo "selected";
                                        }
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

                        <div class="mb-3">

                            <label class="form-label">

                                Specialization *

                            </label>

                            <input
                                type="text"
                                name="specialization"
                                class="form-control"
                                placeholder="Example: Cardiologist"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["specialization"] ?? ""
                                );
                                ?>"
                                required
                            >

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
                                placeholder="Example: MBBS, MD"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["qualification"] ?? ""
                                );
                                ?>"
                            >

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
                                min="0"
                                step="0.01"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["consultation_fee"] ?? ""
                                );
                                ?>"
                            >

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
                                placeholder="Example: Monday, Wednesday, Friday"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["available_days"] ?? ""
                                );
                                ?>"
                            >

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
                                placeholder="Example: 10:00 AM - 2:00 PM"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST["available_time"] ?? ""
                                );
                                ?>"
                            >

                        </div>


                        <hr class="my-4">


                        <h5 class="fw-bold mb-3">

                            Login Password

                        </h5>


                        <!-- Password -->

                        <div class="mb-3">

                            <label class="form-label">

                                Password *

                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                minlength="6"
                                required
                            >

                            <small class="text-muted">

                                Minimum 6 characters.

                            </small>

                        </div>


                        <!-- Confirm Password -->

                        <div class="mb-4">

                            <label class="form-label">

                                Confirm Password *

                            </label>

                            <input
                                type="password"
                                name="confirm_password"
                                class="form-control"
                                minlength="6"
                                required
                            >

                        </div>


                        <!-- Buttons -->

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                Register as Doctor

                            </button>


                            <a
                                href="login.php"
                                class="btn btn-secondary"
                            >

                                Already Registered? Login

                            </a>

                        </div>


                    </form>


                </div>

            </div>


        </div>

    </div>

</div>


</body>

</html>