<?php
session_start();
session_destroy();
header("Location: loginh.php");
exit;
