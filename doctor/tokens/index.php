 <?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "doctor") {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/database.php";
require_once "../../includes/header.php";

$message = "";
$message_type = "";


/*
|--------------------------------------------------------------------------
| Find Logged-in Doctor
|--------------------------------------------------------------------------
*/

$userEmail = $_SESSION["user_email"] ?? "";

$stmt = $conn->prepare("
    SELECT
        id,
        name,
        department_id
    FROM doctors
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $userEmail);
$stmt->execute();

$doctor = $stmt->get_result()->fetch_assoc();

$stmt->close();


if (!$doctor) {
    die("Doctor profile not found.");
}

$doctorId = (int)$doctor["id"];


/*
|--------------------------------------------------------------------------
| Start Consultation
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tokenId = (int)($_POST["token_id"] ?? 0);

    $action = $_POST["action"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | Start Consultation
    |--------------------------------------------------------------------------
    */

    if ($action === "start_consultation" && $tokenId > 0) {

        $stmt = $conn->prepare("
            UPDATE tokens
            SET
                status = 'IN CONSULTATION',
                consultation_started_at = NOW()
            WHERE id = ?
            AND doctor_id = ?
            AND status = 'CALLED'
        ");

        $stmt->bind_param(
            "ii",
            $tokenId,
            $doctorId
        );

        if ($stmt->execute() && $stmt->affected_rows > 0) {

            $message = "Consultation started successfully.";
            $message_type = "success";

        } else {

            $message = "Token could not be started.";
            $message_type = "danger";
        }

        $stmt->close();
    }


    /*
    |--------------------------------------------------------------------------
    | Complete Token
    |--------------------------------------------------------------------------
    */

    if ($action === "complete_token" && $tokenId > 0) {

        $stmt = $conn->prepare("
            UPDATE tokens
            SET
                status = 'COMPLETED',
                completed_at = NOW()
            WHERE id = ?
            AND doctor_id = ?
            AND status = 'FOLLOW UP'
        ");

        $stmt->bind_param(
            "ii",
            $tokenId,
            $doctorId
        );

        if ($stmt->execute() && $stmt->affected_rows > 0) {

            $message = "Token completed successfully.";
            $message_type = "success";

        } else {

            $message = "Token could not be completed.";
            $message_type = "danger";
        }

        $stmt->close();
    }
}


/*
|--------------------------------------------------------------------------
| Get Today's Tokens
|--------------------------------------------------------------------------
*/

$today = date("Y-m-d");

$stmt = $conn->prepare("
    SELECT
        t.id,
        t.token_number,
        t.status,
        t.priority,
        t.called_at,
        t.consultation_started_at,
        t.completed_at,
        t.appointment_id,

        p.patient_code,
        p.name AS patient_name,
        p.phone,

        dep.name AS department_name

    FROM tokens t

    INNER JOIN patients p
        ON t.patient_id = p.id

    INNER JOIN departments dep
        ON t.department_id = dep.id

    WHERE t.doctor_id = ?
    AND t.token_date = ?

    ORDER BY
        CASE
            WHEN t.status = 'CALLED' THEN 1
            WHEN t.status = 'IN CONSULTATION' THEN 2
            WHEN t.status = 'REPORT PENDING' THEN 3
            WHEN t.status = 'REPORT UPLOADED' THEN 4
            WHEN t.status = 'FOLLOW UP' THEN 5
            WHEN t.status = 'COMPLETED' THEN 6
            ELSE 7
        END,
        t.id ASC
");

$stmt->bind_param(
    "is",
    $doctorId,
    $today
);

$stmt->execute();

$tokens = $stmt->get_result();

$stmt->close();

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
                        My Token Queue
                    </h2>

                    <p class="text-muted mb-0">
                        Today's patients assigned to you.
                    </p>

                </div>

                <a
                    href="../dashboard.php"
                    class="btn btn-secondary"
                >
                    Back to Dashboard
                </a>

            </div>


            <!-- Message -->

            <?php if ($message !== ""): ?>

                <div class="alert alert-<?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <!-- Token Table -->

            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-dark">

                                <tr>

                                    <th>#</th>
                                    <th>Token</th>
                                    <th>Patient</th>
                                    <th>Patient Code</th>
                                    <th>Department</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php if ($tokens->num_rows > 0): ?>

                                    <?php $count = 1; ?>

                                    <?php while ($token = $tokens->fetch_assoc()): ?>

                                        <tr>

                                            <td>
                                                <?php echo $count++; ?>
                                            </td>


                                            <td>

                                                <strong class="fs-5">

                                                    <?php echo htmlspecialchars(
                                                        $token["token_number"]
                                                    ); ?>

                                                </strong>

                                            </td>


                                            <td>

                                                <strong>

                                                    <?php echo htmlspecialchars(
                                                        $token["patient_name"]
                                                    ); ?>

                                                </strong>

                                                <br>

                                                <small class="text-muted">

                                                    <?php echo htmlspecialchars(
                                                        $token["phone"]
                                                    ); ?>

                                                </small>

                                            </td>


                                            <td>

                                                <?php echo htmlspecialchars(
                                                    $token["patient_code"]
                                                ); ?>

                                            </td>


                                            <td>

                                                <?php echo htmlspecialchars(
                                                    $token["department_name"]
                                                ); ?>

                                            </td>


                                            <td>

                                                <?php if ($token["priority"] === "High"): ?>

                                                    <span class="badge bg-danger">
                                                        High
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge bg-secondary">
                                                        Normal
                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <td>

                                                <?php

                                                $status = $token["status"];

                                                $badge = "secondary";

                                                if ($status === "WAITING") {
                                                    $badge = "warning";
                                                }

                                                if ($status === "CALLED") {
                                                    $badge = "primary";
                                                }

                                                if ($status === "IN CONSULTATION") {
                                                    $badge = "info";
                                                }

                                                if ($status === "REPORT PENDING") {
                                                    $badge = "danger";
                                                }

                                                if ($status === "REPORT UPLOADED") {
                                                    $badge = "success";
                                                }

                                                if ($status === "FOLLOW UP") {
                                                    $badge = "dark";
                                                }

                                                if ($status === "COMPLETED") {
                                                    $badge = "success";
                                                }

                                                ?>

                                                <span
                                                    class="badge bg-<?php echo $badge; ?>"
                                                >

                                                    <?php echo htmlspecialchars(
                                                        $status
                                                    ); ?>

                                                </span>

                                            </td>


                                            <td>


                                                <?php if ($status === "CALLED"): ?>


                                                    <!-- Start Consultation -->

                                                    <form method="POST">

                                                        <input
                                                            type="hidden"
                                                            name="token_id"
                                                            value="<?php echo $token["id"]; ?>"
                                                        >

                                                        <input
                                                            type="hidden"
                                                            name="action"
                                                            value="start_consultation"
                                                        >

                                                        <button
                                                            type="submit"
                                                            class="btn btn-success btn-sm"
                                                        >
                                                            Start Consultation
                                                        </button>

                                                    </form>


                                                <?php elseif ($status === "IN CONSULTATION"): ?>


                                                    <!-- Consultation Actions -->

                                                    <div class="d-flex gap-2 flex-wrap">

                                                        <a
                                                            href="../consultations/add.php?appointment_id=<?php echo $token["appointment_id"]; ?>"
                                                            class="btn btn-primary btn-sm"
                                                        >
                                                            Open Consultation
                                                        </a>

                                                        <a
                                                            href="../reports/request.php?appointment_id=<?php echo $token["appointment_id"]; ?>"
                                                            class="btn btn-warning btn-sm"
                                                        >
                                                            Request Report
                                                        </a>

                                                    </div>


                                                <?php elseif ($status === "REPORT PENDING"): ?>


                                                    <span class="text-danger fw-bold">
                                                        Report Pending
                                                    </span>


                                                <?php elseif ($status === "REPORT UPLOADED"): ?>


                                                    <!-- Report Uploaded -->

                                                    <div class="d-flex gap-2 flex-wrap">

                                                        <a
                                                            href="../reports/index.php"
                                                            class="btn btn-success btn-sm"
                                                        >
                                                            View Report
                                                        </a>

                                                        <a
                                                            href="../followups/create.php?appointment_id=<?php echo $token["appointment_id"]; ?>"
                                                            class="btn btn-primary btn-sm"
                                                        >
                                                            Schedule Follow-up
                                                        </a>

                                                    </div>


                                                <?php elseif ($status === "FOLLOW UP"): ?>


                                                    <!-- Complete Token -->

                                                    <form method="POST">

                                                        <input
                                                            type="hidden"
                                                            name="token_id"
                                                            value="<?php echo $token["id"]; ?>"
                                                        >

                                                        <input
                                                            type="hidden"
                                                            name="action"
                                                            value="complete_token"
                                                        >

                                                        <button
                                                            type="submit"
                                                            class="btn btn-success btn-sm"
                                                        >
                                                            Complete Token
                                                        </button>

                                                    </form>


                                                <?php elseif ($status === "COMPLETED"): ?>


                                                    <span class="text-success fw-bold">

                                                        Completed

                                                    </span>


                                                <?php else: ?>


                                                    <span class="text-muted">

                                                        Waiting

                                                    </span>


                                                <?php endif; ?>


                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td
                                            colspan="8"
                                            class="text-center py-5"
                                        >

                                            <h5 class="fw-bold">
                                                No Tokens Found
                                            </h5>

                                            <p class="text-muted mb-0">
                                                No patients are currently
                                                assigned to you for today.
                                            </p>

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<?php

require_once "../../includes/footer.php";

?>