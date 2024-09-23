<?php

require_once 'config.php'; // Include the config file to access SECRET_KEY

function hashPassword($password) {
    return password_hash($password . SECRET_KEY, PASSWORD_BCRYPT);
}

function verifyPassword($enteredPassword, $storedHash) {
    return password_verify($enteredPassword . SECRET_KEY, $storedHash);
}

?>
