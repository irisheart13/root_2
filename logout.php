<?php
session_start();

session_unset();

session_destroy();

header("Location: /Root_1/index.php");
exit();
?>