<?php
session_start();

session_unset();

session_destroy();

header("Location: /Root_2/index.php");
exit();
?>