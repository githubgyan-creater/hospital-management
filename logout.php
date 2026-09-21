<?php

session_start();

$_SESSION = [];

session_destroy();

header("Location: /projects/hospital_management/index.php");

exit();

?>