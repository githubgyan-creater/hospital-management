<?php

require_once "includes/header.php";

?>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 col-lg-2 p-0">
            <?php require_once "includes/sidebar.php"; ?>
        </div>

        <div class="col-md-9 col-lg-10 p-4">

            <h2 class="fw-bold">
                Dashboard Layout Test
            </h2>

            <p class="text-muted">
                If you can see the navbar, sidebar, and footer,
                the reusable layout is working correctly.
            </p>

        </div>

    </div>
</div>

<?php

require_once "includes/footer.php";

?>