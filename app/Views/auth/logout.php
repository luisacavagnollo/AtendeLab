<?php
session_start();
session_destroy();
header("Location: /atendelab/app/Views/auth/login.php");
exit;
