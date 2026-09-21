<?php

require_once "../includes/auth.php";

if ($_SESSION["user_role"] !== "receptionist") {
    header("Location: ../index.php");
    exit;
}

require_once "../config/database.php";
require_once "../includes/header.php";

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">

            <?php require_once "../includes/sidebar.php"; ?>

        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold">
                        Receptionist Dashboard
                    </h2>

                    <p class="text-muted mb-0">
                        Welcome,
                        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
                    </p>

                </div>

                <a href="../logout.php" class="btn btn-danger">
                    Logout
                </a>

            </div>

            <div class="card shadow-sm">

                <div class="card-body">

                    <h4 class="fw-bold">
                        Welcome to Receptionist Panel
                    </h4>

                    <p class="text-muted mb-0">
                        You will be able to register patients,
                        book appointments, view appointments,
                        and create basic bills from this panel.
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once "../includes/footer.php";

?>