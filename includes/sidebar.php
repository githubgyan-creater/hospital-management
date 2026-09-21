 <?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_role = $_SESSION["user_role"] ?? "";

?>

<div class="bg-dark text-white min-vh-100 p-3">

    <!-- Hospital Name -->

    <div class="mb-4">

        <h5 class="fw-bold mb-1">
            Hospital Management
        </h5>

        <small class="text-white-50">
            <?php echo htmlspecialchars(ucfirst($user_role)); ?> Panel
        </small>

    </div>


    <!-- Navigation -->

    <ul class="nav flex-column">


        <!-- ========================================================= -->
        <!-- ADMIN MENU -->
        <!-- ========================================================= -->

        <?php if ($user_role === "admin"): ?>

            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/admin/dashboard.php"
                    class="nav-link text-white"
                >
                    Dashboard
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/admin/patients/index.php"
                    class="nav-link text-white"
                >
                    Patients
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/admin/doctors/index.php"
                    class="nav-link text-white"
                >
                    Doctors
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/admin/departments/index.php"
                    class="nav-link text-white"
                >
                    Departments
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/admin/appointments/index.php"
                    class="nav-link text-white"
                >
                    Appointments
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/admin/billing/index.php"
                    class="nav-link text-white"
                >
                    Billing
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/report/index.php"
                    class="nav-link text-white"
                >
                    Reports
                </a>

            </li>


        <!-- ========================================================= -->
        <!-- DOCTOR MENU -->
        <!-- ========================================================= -->

        <?php elseif ($user_role === "doctor"): ?>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/doctor/dashboard.php"
                    class="nav-link text-white"
                >
                    Dashboard
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/doctor/profile/index.php"
                    class="nav-link text-white"
                >
                    My Profile
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/doctor/appointments/index.php"
                    class="nav-link text-white"
                >
                    My Appointments
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/doctor/consultations/index.php"
                    class="nav-link text-white"
                >
                    My Consultations
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/doctor/tokens/index.php"
                    class="nav-link text-white"
                >
                    My Token Queue
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/doctor/reports/index.php"
                    class="nav-link text-white"
                >
                    Medical Reports
                </a>

            </li>


        <!-- ========================================================= -->
        <!-- RECEPTIONIST MENU -->
        <!-- ========================================================= -->

        <?php elseif ($user_role === "receptionist"): ?>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/receptionist/dashboard.php"
                    class="nav-link text-white"
                >
                    Dashboard
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/receptionist/patients/index.php"
                    class="nav-link text-white"
                >
                    Patients
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/receptionist/appointments/index.php"
                    class="nav-link text-white"
                >
                    Appointments
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/receptionist/billing/index.php"
                    class="nav-link text-white"
                >
                    Billing
                </a>

            </li>


            <li class="nav-item mb-2">

                <a
                    href="/projects/hospital_management/reports/index.php"
                    class="nav-link text-white"
                >
                    Medical Reports
                </a>

            </li>


        <?php endif; ?>


        <!-- ========================================================= -->
        <!-- LOGOUT -->
        <!-- ========================================================= -->

        <li class="nav-item mt-4">

            <a
                href="/projects/hospital_management/logout.php"
                class="nav-link text-danger fw-bold"
            >
                Logout
            </a>

        </li>


    </ul>

</div>