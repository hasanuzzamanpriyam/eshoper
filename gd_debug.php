<?php
echo "Loaded Configuration File: " . php_ini_loaded_file() . "\n";
echo "GD Section:\n";
$gd = gd_info();
print_r($gd);
