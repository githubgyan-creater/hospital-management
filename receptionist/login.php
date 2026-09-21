<?php

session_start();

require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $error = "Please enter email and password.";

    } else {

        $sql = "
            SELECT
                id,
                name,
                email,
                password,
                role,
                status
            FROM users
            WHERE email = ?
            AND role = 'receptionist'
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if ((int)$user["status"] !== 1) {

                $error = "Your receptionist account is not active.";

            } elseif (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["user_role"] = "receptionist";

                header("Location: dashboard.php");
                exit();

            } else {

                $error = "Invalid email or password.";

            }

        } else {

            $error = "Invalid receptionist email or password.";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Receptionist Login</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>


<body class="bg-light">


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6 col-lg-5">

            <div class="card shadow-sm border-0">


                <!-- Header -->

                <div class="card-header bg-warning text-dark text-center p-4">

                    <h3 class="fw-bold mb-1">
                        Receptionist Login
                    </h3>

                    <p class="mb-0">
                        Hospital Management System
                    </p>

                </div>


                <!-- Body -->

                <div class="card-body p-4">


                    <?php if ($error !== ""): ?>

                        <div class="alert alert-danger">

                            <?php echo htmlspecialchars($error); ?>

                        </div>

                    <?php endif; ?>


                    <!-- Login Form -->

                    <form method="POST">


                        <div class="mb-3">

                            <label class="form-label fw-bold">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="Enter receptionist email"
                                required>

                        </div>


                        <div class="mb-4">

                            <label class="form-label fw-bold">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter password"
                                required>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-warning w-100 fw-bold">

                            Login

                        </button>


                    </form>


                    <!-- Back -->

                    <div class="text-center mt-4">

                        <a
                            href="../management/index.php"
                            class="text-decoration-none">

                            ← Back to Management Portal

                        </a>

                    </div>


                    <div class="text-center mt-2">

                        <a
                            href="../index.php"
                            class="text-decoration-none">

                            ← Back to Hospital Website

                        </a>

                    </div>


                </div>

            </div>

        </div>

    </div>

</div>


</body>

</html>