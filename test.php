<?php
$link = mysqli_connect('localhost', 'dmbe11_cmon', '0utD00Rs', 'dmbe11_customer');

if (!$link) {
    die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
}

echo 'Connected... ' . mysqli_get_host_info($link) . "\n";