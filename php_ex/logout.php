<?php
session_start();
session_destroy();
header("Location: ../interfata/loginh.php");
exit;
