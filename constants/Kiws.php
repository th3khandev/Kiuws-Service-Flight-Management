<?php

$errors = [
    '10035'  => 'La fecha de salida no debe ser pasada ni superior a 330 días.',
];

function getErrorMessage($code) {
    global $errors;
    if (isset($errors[$code])) {
        return $errors[$code];
    }
    return 'Error desconocido';
}