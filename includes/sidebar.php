 <?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get logged-in user's role
$userRole = $_SESSION["user_role"] ?? "";

?>

<div class="bg-dark text-white p-3" style="min-height: calc(100vh - 56px);">

    <h5 class="fw-bold mb-4">
        Menu
    </h5>

    <ul class="nav flex-column">

        <!-- Dashboard -->

        <?php if ($userRole === "admin"): ?>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/admin/dashboard.php"
                    class="nav-link text-white">
                    Dashboard
                </a>
            </li>

        <?php elseif ($userRole === "doctor"): ?>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/doctor/dashboard.php"
                    class="nav-link text-white">
                    Dashboard
                </a>
            </li>

        <?php elseif ($userRole === "receptionist"): ?>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/receptionist/dashboard.php"
                    class="nav-link text-white">
                    Dashboard
                </a>
            </li>

        <?php endif; ?>


        <!-- ADMIN MENU -->

        <?php if ($userRole === "admin"): ?>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/admin/patients/index.php"
                    class="nav-link text-white">
                    Patients
                </a>
            </li>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/admin/doctors/index.php"
                    class="nav-link text-white">
                    Doctors
                </a>
            </li>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/admin/departments/index.php"
                    class="nav-link text-white">
                    Departments
                </a>
            </li>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/admin/appointments/index.php"
                    class="nav-link text-white">
                    Appointments
                </a>
            </li>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/admin/billing/index.php"
                    class="nav-link text-white">
                    Billing
                </a>
            </li>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/admin/reports/index.php"
                    class="nav-link text-white">
                    Reports
                </a>
            </li>

        <?php endif; ?>


        <!-- DOCTOR MENU -->

        <?php if ($userRole === "doctor"): ?>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/doctor/appointments/index.php"
                    class="nav-link text-white">
                    My Appointments
                </a>
            </li>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/doctor/consultations/index.php"
                    class="nav-link text-white">
                    Consultations
                </a>
            </li>

        <?php endif; ?>


        <!-- RECEPTIONIST MENU -->

        <?php if ($userRole === "receptionist"): ?>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/receptionist/patients/index.php"
                    class="nav-link text-white">
                    Patients
                </a>
            </li>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/receptionist/appointments/index.php"
                    class="nav-link text-white">
                    Appointments
                </a>
            </li>

            <li class="nav-item mb-2">
                <a
                    href="/projects/hospital_management/receptionist/billing/index.php"
                    class="nav-link text-white">
                    Billing
                </a>
            </li>

        <?php endif; ?>


        <!-- Logout -->

        <?php if ($userRole !== ""): ?>

            <li class="nav-item mt-4">

                <a
                    href="/projects/hospital_management/admin/logout.php"
                    class="nav-link text-danger fw-bold">

                    Logout

                </a>

            </li>

        <?php endif; ?>

    </ul>

</div>