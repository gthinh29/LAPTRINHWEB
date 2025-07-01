<?php
$serverName = "LAPTOP-UHHNOGG1\\SQLEXPRESS";
$connectionOptions = [
    "Database" => "BlogDB"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}   


?>