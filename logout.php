<?php
require_once 'includes/functions.php';
initSession();
session_unset();
session_destroy();
header('Location: index.php');
exit;