<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Management Portal - ABC Super Speciality Hospital</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>


<body class="bg-light">


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-10">


            <!-- Main Card -->

            <div class="card shadow-sm border-0">


                <div class="card-body p-4 p-md-5">


                    <!-- Heading -->

                    <div class="text-center mb-5">

                        <h1 class="fw-bold">
                            ABC Super Speciality Hospital
                        </h1>

                        <h3 class="text-primary mt-3">
                            Management Portal
                        </h3>

                        <p class="text-muted mt-2">
                            Please select the portal you want to access.
                        </p>

                    </div>


                    <!-- Portal Options -->

                    <div class="row g-4">


                        <!-- Admin -->

                        <div class="col-md-4">

                            <div class="card h-100 border shadow-sm">

                                <div class="card-body p-4 text-center d-flex flex-column">

                                    <h4 class="fw-bold">
                                        Admin
                                    </h4>

                                    <p class="text-muted">

                                        Manage patients, doctors,
                                        departments, appointments,
                                        billing and reports.

                                    </p>


                                    <div class="mt-auto pt-3">

                                        <a
                                            href="../admin/login.php"
                                            class="btn btn-primary w-100">

                                            Admin Login

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>



                        <!-- Doctor -->

                        <div class="col-md-4">

                            <div class="card h-100 border shadow-sm">

                                <div class="card-body p-4 text-center d-flex flex-column">

                                    <h4 class="fw-bold">
                                        Doctor
                                    </h4>

                                    <p class="text-muted">

                                        Login to manage appointments,
                                        consultations, tokens,
                                        reports and profile.

                                    </p>


                                    <div class="mt-auto pt-3">

                                        <a
                                            href="../doctor/login.php"
                                            class="btn btn-success w-100">

                                            Doctor Login

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>



                        <!-- Receptionist -->

                        <div class="col-md-4">

                            <div class="card h-100 border shadow-sm">

                                <div class="card-body p-4 text-center d-flex flex-column">

                                    <h4 class="fw-bold">
                                        Receptionist
                                    </h4>

                                    <p class="text-muted">

                                        Manage patients,
                                        appointments and
                                        billing.

                                    </p>


                                    <div class="mt-auto pt-3">

                                        <a
                                            href="../receptionist/login.php"
                                            class="btn btn-warning w-100">

                                            Receptionist Login

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>


                    </div>


                    <!-- Back Button -->

                    <div class="text-center mt-5">

                        <a
                            href="../index.php"
                            class="btn btn-outline-secondary">

                            ← Back to Hospital Website

                        </a>

                    </div>


                </div>

            </div>


        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>