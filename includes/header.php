<?php
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
?>
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospital Management System</title>

   
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

   
    <link rel="stylesheet" href="/projects/hospital_management/assets/css/style.css">
</head>

<body>

<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">
            Hospital Management System
        </a>
    </div>
</nav>