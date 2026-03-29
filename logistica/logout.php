<?php
session_start();
session_destroy();
header("Location: LoginCamiones.php");
exit();
