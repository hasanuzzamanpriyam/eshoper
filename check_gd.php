<?php
echo "Function imagettfbbox: " . (function_exists('imagettfbbox') ? 'YES' : 'NO') . "\n";
$gd = gd_info();
echo "FreeType Support: " . ($gd['FreeType Support'] ? 'YES' : 'NO') . "\n";
