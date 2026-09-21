<?php

require_once "../../includes/auth.php";

if ($_SESSION['user_role'] !== 'receptionist') {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/database.php";
require_once "../../includes/header.php";

$today = date("Y-m-d");

$message = "";
$message_type = "";

/* =========================
   CALL TOKEN
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token_id = (int)($_POST["token_id"] ?? 0);

    if ($token_id > 0) {

        $stmt = $conn->prepare("
            UPDATE tokens
            SET
                status = 'CALLED',
                called_at = NOW()
            WHERE id = ?
            AND token_date = ?
            AND status = 'WAITING'
        ");

        $stmt->bind_param(
            "is",
            $token_id,
            $today
        );

        if ($stmt->execute() && $stmt->affected_rows > 0) {

            $message = "Token called successfully.";
            $message_type = "success";

        } else {

            $message = "Token could not be called.";
            $message_type = "danger";
        }

        $stmt->close();
    }
}


/* =========================
   GET TODAY'S TOKENS
========================= */

$stmt = $conn->prepare("
    SELECT
        t.id,
        t.token_number,
        t.status,
        t.priority,
        t.called_at,
        t.consultation_started_at,
        t.completed_at,

        p.patient_code,
        p.name AS patient_name,
        p.phone,

        d.name AS department_name,

        doc.name AS doctor_name

    FROM tokens t

    INNER JOIN patients p
        ON t.patient_id = p.id

    INNER JOIN departments d
        ON t.department_id = d.id

    INNER JOIN doctors doc
        ON t.doctor_id = doc.id

    WHERE t.token_date = ?

    ORDER BY
        CASE
            WHEN t.status = 'WAITING' THEN 1
            WHEN t.status = 'CALLED' THEN 2
            WHEN t.status = 'IN CONSULTATION' THEN 3
            WHEN t.status = 'REPORT PENDING' THEN 4
            WHEN t.status = 'REPORT UPLOADED' THEN 5
            WHEN t.status = 'FOLLOW UP' THEN 6
            WHEN t.status = 'COMPLETED' THEN 7
            ELSE 8
        END,
        t.id ASC
");

$stmt->bind_param("s", $today);
$stmt->execute();

$tokens = $stmt->get_result();

$stmt->close();

?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Token Queue
            </h2>

            <p class="text-muted mb-0">
                Manage today's OPD token queue.
            </p>

        </div>

        <a
            href="../dashboard.php"
            class="btn btn-secondary"
        >
            Back to Dashboard
        </a>

    </div>


    <?php if ($message !== ""): ?>

        <div class="alert alert-<?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


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

                            <th>Doctor</th>

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

                                        <?php echo htmlspecialchars(
                                            $token["patient_name"]
                                        ); ?>

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

                                        <?php echo htmlspecialchars(
                                            $token["doctor_name"]
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

                                            <?php echo htmlspecialchars($status); ?>

                                        </span>

                                    </td>


                                    <td>

                                        <?php if ($status === "WAITING"): ?>

                                            <form method="POST">

                                                <input
                                                    type="hidden"
                                                    name="token_id"
                                                    value="<?php echo $token["id"]; ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn btn-primary btn-sm"
                                                >
                                                    Call Token
                                                </button>

                                            </form>

                                        <?php elseif ($status === "CALLED"): ?>

                                            <span class="text-primary fw-bold">
                                                Patient Called
                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                No Action
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center py-4"
                                >

                                    <strong>
                                        No tokens found for today.
                                    </strong>

                                    <br>

                                    <small class="text-muted">
                                        Patient tokens will appear here after generation.
                                    </small>

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php require_once "../../includes/footer.php"; ?>