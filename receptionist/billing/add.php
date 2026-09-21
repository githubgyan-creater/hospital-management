<?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "receptionist") {
    header("Location: ../../login.php");
    exit();
}

require_once "../../config/database.php";

/* Get patients */
$patients = $conn->query("
    SELECT id, patient_code, name
    FROM patients
    ORDER BY name ASC
");

/* Get appointments */
$appointments = $conn->query("
    SELECT
        appointments.id,
        appointments.appointment_date,
        patients.name AS patient_name
    FROM appointments
    LEFT JOIN patients
        ON appointments.patient_id = patients.id
    ORDER BY appointments.id DESC
");

/* Save bill */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $patient_id = intval($_POST["patient_id"]);
    $appointment_id = !empty($_POST["appointment_id"])
        ? intval($_POST["appointment_id"])
        : null;

    $bill_date = $_POST["bill_date"];
    $amount = $_POST["amount"];
    $payment_status = $_POST["payment_status"];
    $payment_method = !empty($_POST["payment_method"])
        ? $_POST["payment_method"]
        : null;

    $description = $_POST["description"];

    /* Generate bill number */
    $bill_number = "BILL-" . date("YmdHis");

    $stmt = $conn->prepare("
        INSERT INTO billing
        (
            patient_id,
            appointment_id,
            bill_number,
            bill_date,
            amount,
            payment_status,
            payment_method,
            description
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iissdsss",
        $patient_id,
        $appointment_id,
        $bill_number,
        $bill_date,
        $amount,
        $payment_status,
        $payment_method,
        $description
    );

    if ($stmt->execute()) {

        header("Location: index.php");
        exit();

    } else {

        $error = "Error creating bill.";

    }
}

require_once "../../includes/header.php";

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 p-0">
            <?php require_once "../../includes/sidebar.php"; ?>
        </div>


        <!-- Main Content -->
        <div class="col-md-10">

            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h2>Add Bill</h2>

                    <a href="index.php" class="btn btn-secondary">
                        Back to Billing
                    </a>

                </div>


                <?php if (isset($error)): ?>

                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>

                <?php endif; ?>


                <div class="card">

                    <div class="card-header bg-success text-white">

                        <strong>Billing Information</strong>

                    </div>


                    <div class="card-body">

                        <form method="POST">


                            <!-- Patient -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Patient
                                </label>

                                <select name="patient_id"
                                        class="form-control"
                                        required>

                                    <option value="">
                                        Select Patient
                                    </option>

                                    <?php while ($patient = $patients->fetch_assoc()): ?>

                                        <option value="<?php echo $patient["id"]; ?>">

                                            <?php

                                            echo htmlspecialchars(
                                                $patient["patient_code"]
                                                . " - "
                                                . $patient["name"]
                                            );

                                            ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>


                            <!-- Appointment -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Appointment
                                </label>

                                <select name="appointment_id"
                                        class="form-control">

                                    <option value="">
                                        Select Appointment (Optional)
                                    </option>

                                    <?php while ($appointment = $appointments->fetch_assoc()): ?>

                                        <option value="<?php echo $appointment["id"]; ?>">

                                            <?php

                                            echo "Appointment #"
                                                . $appointment["id"]
                                                . " - "
                                                . htmlspecialchars($appointment["patient_name"])
                                                . " - "
                                                . htmlspecialchars($appointment["appointment_date"]);

                                            ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>


                            <!-- Bill Date -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Bill Date
                                </label>

                                <input type="date"
                                       name="bill_date"
                                       class="form-control"
                                       value="<?php echo date('Y-m-d'); ?>"
                                       required>

                            </div>


                            <!-- Amount -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Amount
                                </label>

                                <input type="number"
                                       name="amount"
                                       class="form-control"
                                       step="0.01"
                                       min="0"
                                       required>

                            </div>


                            <!-- Payment Status -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Payment Status
                                </label>

                                <select name="payment_status"
                                        class="form-control"
                                        required>

                                    <option value="Pending">
                                        Pending
                                    </option>

                                    <option value="Paid">
                                        Paid
                                    </option>

                                    <option value="Cancelled">
                                        Cancelled
                                    </option>

                                </select>

                            </div>


                            <!-- Payment Method -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Payment Method
                                </label>

                                <select name="payment_method"
                                        class="form-control">

                                    <option value="">
                                        Select Payment Method
                                    </option>

                                    <option value="Cash">
                                        Cash
                                    </option>

                                    <option value="Card">
                                        Card
                                    </option>

                                    <option value="UPI">
                                        UPI
                                    </option>

                                    <option value="Bank Transfer">
                                        Bank Transfer
                                    </option>

                                </select>

                            </div>


                            <!-- Description -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Description
                                </label>

                                <textarea name="description"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Enter billing details"></textarea>

                            </div>


                            <button type="submit"
                                    class="btn btn-success">

                                Save Bill

                            </button>


                            <a href="index.php"
                               class="btn btn-secondary">

                                Cancel

                            </a>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>