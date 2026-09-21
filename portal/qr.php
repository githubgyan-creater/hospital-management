<?php

$portalUrl = "http://" . $_SERVER['HTTP_HOST'] . "/projects/hospital_management/portal/";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospital Patient Portal QR Code</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        body {
            background: #f5f7fa;
        }

        .qr-card {
            max-width: 550px;
            margin: 60px auto;
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.10);
            text-align: center;
        }

        #qrcode {
            display: flex;
            justify-content: center;
            margin: 25px 0;
        }

        .portal-url {
            word-break: break-all;
            background: #f1f3f5;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="qr-card">

        <h2 class="fw-bold mb-2">
            Hospital Patient Portal
        </h2>

        <p class="text-muted">
            Scan this QR code to access the Patient Portal
        </p>

        <div id="qrcode"></div>

        <div class="portal-url">
            <?php echo htmlspecialchars($portalUrl); ?>
        </div>

        <div class="mt-4">

            <a
                href="<?php echo htmlspecialchars($portalUrl); ?>"
                class="btn btn-primary"
                target="_blank">
                Open Patient Portal
            </a>

        </div>

        <p class="text-muted mt-4 mb-0">
            New patients can register and existing patients can login
            through the Patient Portal.
        </p>

    </div>

</div>

<script>

    new QRCode(document.getElementById("qrcode"), {
        text: <?php echo json_encode($portalUrl); ?>,
        width: 250,
        height: 250
    });

</script>

</body>
</html>