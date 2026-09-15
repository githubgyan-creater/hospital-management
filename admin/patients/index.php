<?php

require_once "../../includes/auth.php";

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require_once "../../config/database.php";
require_once "../../includes/header.php";

?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            <?php require_once "../../includes/sidebar.php"; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h2 class="fw-bold mb-1">Patients</h2>
                    <p class="text-muted mb-0">
                        Manage hospital patients
                    </p>
                </div>

                <a href="add.php" class="btn btn-primary">
                    + Add Patient
                </a>

            </div>

            <!-- Search -->
            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <form method="GET">

                        <div class="row">

                            <div class="col-md-10">

                                <input
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    placeholder="Search by patient name, ID, or phone"
                                    value="<?php
                                    echo isset($_GET["search"])
                                        ? htmlspecialchars($_GET["search"])
                                        : "";
                                    ?>"
                                >

                            </div>

                            <div class="col-md-2 mt-2 mt-md-0">

                                <button
                                    type="submit"
                                    class="btn btn-primary w-100">
                                    Search
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

            <!-- Patient Table -->
            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-primary">

                                <tr>
                                    <th>Patient ID</th>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Phone</th>
                                    <th>Blood Group</th>
                                    <th>Registration Date</th>
                                    <th>Action</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php

                                $search = "";

                                if (isset($_GET["search"])) {
                                    $search = trim($_GET["search"]);
                                }

                                if ($search !== "") {

                                    $sql = "SELECT *
                                            FROM patients
                                            WHERE patient_code LIKE ?
                                            OR name LIKE ?
                                            OR phone LIKE ?
                                            ORDER BY id DESC";

                                    $stmt = $conn->prepare($sql);

                                    $searchTerm = "%" . $search . "%";

                                    $stmt->bind_param(
                                        "sss",
                                        $searchTerm,
                                        $searchTerm,
                                        $searchTerm
                                    );

                                } else {

                                    $sql = "SELECT *
                                            FROM patients
                                            ORDER BY id DESC";

                                    $stmt = $conn->prepare($sql);
                                }

                                $stmt->execute();

                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {

                                    while ($patient = $result->fetch_assoc()) {

                                ?>

                                        <tr>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $patient["patient_code"]
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $patient["name"]
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $patient["age"]
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $patient["gender"]
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $patient["phone"]
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $patient["blood_group"]
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo date(
                                                    "d-m-Y",
                                                    strtotime(
                                                        $patient["created_at"]
                                                    )
                                                );
                                                ?>
                                            </td>

                                            <td>

                                                <a
                                                    href="view.php?id=<?php echo $patient["id"]; ?>"
                                                    class="btn btn-sm btn-info">
                                                    View
                                                </a>

                                                <a
                                                    href="edit.php?id=<?php echo $patient["id"]; ?>"
                                                    class="btn btn-sm btn-warning">
                                                    Edit
                                                </a>

                                                <a
                                                    href="delete.php?id=<?php echo $patient["id"]; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this patient?');">
                                                    Delete
                                                </a>

                                            </td>

                                        </tr>

                                <?php

                                    }

                                } else {

                                ?>

                                    <tr>

                                        <td colspan="8"
                                            class="text-center text-muted">

                                            No patients found.

                                        </td>

                                    </tr>

                                <?php

                                }

                                $stmt->close();

                                ?>

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