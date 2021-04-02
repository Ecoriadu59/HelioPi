<?php 

$result = shell_exec("sudo /sbin/shutdown -h now");

echo $result;
?>