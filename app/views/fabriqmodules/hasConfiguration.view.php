<?php
header('Content-type: application/json');
$data = new stdClass();
$data->hasConfiguration = ($numConfigs > 0) ? TRUE : FALSE;
echo json_encode($data);
?>