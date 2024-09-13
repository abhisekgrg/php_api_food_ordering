<?php
session_start();

if (isset($_SESSION['test'])) {
    echo "Session value: " . $_SESSION['test'];
} else {
    $_SESSION['test'] = 'Session is working!';
    echo "Session initialized.";
}
?>
