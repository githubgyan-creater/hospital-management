<?php

session_start();

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

require_once "../../config/database.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}


/* Get existing bill */

$stmt = $conn->prepare("SELECT * FROM billing WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$bill = $result->fetch_assoc();

if (!$bill) {
    die("Bill not found.");
}


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


/* Update bill */

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


    $update = $conn->prepare("
        UPDATE billing SET
        patient_id = ?,
        appointment_id = ?,
        bill_date = ?,
        amount = ?,
        payment_status = ?,
        payment_method = ?,
        description = ?
        WHERE id = ?
    ");

    $update->bind_param(
        "iisdsssi",
        $patient_id,
        $appointment_id,
        $bill_date,
        $amount,
        $payment_status,
        $payment_method,
        $description,
        $id
    );


    if ($update->execute()) {

        header("Location: view.php?id=" . $id);
        exit();

    } else {

        $error = "Error updating bill.";

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

                    <div>

                        <h2>Edit Bill</h2>

                        <p class="text-muted">
                            Update billing information
                        </p>

                    </div>

                    <a
                        href="view.php?id=<?php echo $bill["id"]; ?>"
                        class="btn btn-secondary"
                    >
                        Back
                    </a>

                </div>


                <?php if (isset($error)): ?>

                    <div class="alert alert-danger">

                        <?php echo htmlspecialchars($error); ?>

                    </div>

                <?php endif; ?>


                <div class="card">

                    <div class="card-header bg-warning">

                        <strong>
                            Edit Billing Information
                        </strong>

                    </div>


                    <div class="card-body">

                        <form method="POST">


                            <!-- Bill Number -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Bill Number
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($bill["bill_number"]); ?>"
                                    readonly
                                >

                            </div>


                            <!-- Patient -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Patient
                                </label>

                                <select
                                    name="patient_id"
                                    class="form-control"
                                    required
                                >

                                    <option value="">
                                        Select Patient
                                    </option>


                                    <?php while ($patient = $patients->fetch_assoc()): ?>

                                        <option
                                            value="<?php echo $patient["id"]; ?>"
                                            <?php
                                            if ($patient["id"] == $bill["patient_id"]) {
                                                echo "selected";
                                            }
                                            ?>
                                        >

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

                                <select
                                    name="appointment_id"
                                    class="form-control"
                                >

                                    <option value="">
                                        Select Appointment (Optional)
                                    </option>


                                    <?php while ($appointment = $appointments->fetch_assoc()): ?>

                                        <option
                                            value="<?php echo $appointment["id"]; ?>"
                                            <?php
                                            if ($appointment["id"] == $bill["appointment_id"]) {
                                                echo "selected";
                                            }
                                            ?>
                                        >

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

                                <input
                                    type="date"
                                    name="bill_date"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($bill["bill_date"]); ?>"
                                    required
                                >

                            </div>


                            <!-- Amount -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Amount
                                </label>

                                <input
                                    type="number"
                                    name="amount"
                                    class="form-control"
                                    step="0.01"
                                    min="0"
                                    value="<?php echo htmlspecialchars($bill["amount"]); ?>"
                                    required
                                >

                            </div>


                            <!-- Payment Status -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Payment Status
                                </label>

                                <select
                                    name="payment_status"
                                    class="form-control"
                                    required
                                >

                                    <option
                                        value="Pending"
                                        <?php
                                        if ($bill["payment_status"] == "Pending") {
                                            echo "selected";
                                        }
                                        ?>
                                    >
                                        Pending
                                    </option>

                                    <option
                                        value="Paid"
                                        <?php
                                        if ($bill["payment_status"] == "Paid") {
                                            echo "selected";
                                        }
                                        ?>
                                    >
                                        Paid
                                    </option>

                                    <option
                                        value="Cancelled"
                                        <?php
                                        if ($bill["payment_status"] == "Cancelled") {
                                            echo "selected";
                                        }
                                        ?>
                                    >
                                        Cancelled
                                    </option>

                                </select>

                            </div>


                            <!-- Payment Method -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Payment Method
                                </label>

                                <select
                                    name="payment_method"
                                    class="form-control"
                                >

                                    <option value="">
                                        Select Payment Method
                                    </option>

                                    <option
                                        value="Cash"
                                        <?php
                                        if ($bill["payment_method"] == "Cash") {
                                            echo "selected";
                                        }
                                        ?>
                                    >
                                        Cash
                                    </option>

                                    <option
                                        value="Card"
                                        <?php
                                        if ($bill["payment_method"] == "Card") {
                                            echo "selected";
                                        }
                                        ?>
                                    >
                                        Card
                                    </option>

                                    <option
                                        value="UPI"
                                        <?php
                                        if ($bill["payment_method"] == "UPI") {
                                            echo "selected";
                                        }
                                        ?>
                                    >
                                        UPI
                                    </option>

                                    <option
                                        value="Bank Transfer"
                                        <?php
                                        if ($bill["payment_method"] == "Bank Transfer") {
                                            echo "selected";
                                        }
                                        ?>
                                    >
                                        Bank Transfer
                                    </option>

                                </select>

                            </div>


                            <!-- Description -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Description
                                </label>

                                <textarea
                                    name="description"
                                    class="form-control"
                                    rows="3"
                                ><?php echo htmlspecialchars($bill["description"] ?? ""); ?></textarea>

                            </div>


                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Update Bill
                            </button>


                            <a
                                href="view.php?id=<?php echo $bill["id"]; ?>"
                                class="btn btn-secondary"
                            >
                                Cancel
                            </a>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>