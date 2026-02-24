<?php
$_GET["name"] = "Aquasol";
ob_start();
require "c:/xampp/htdocs/ANDISON-1/brand.php";
$html = ob_get_clean();
echo "data-images= : " . (strpos($html,'data-images=') !== false ? 'YES' : 'NO')."\n";
echo "data-slider= : " . (strpos($html,'data-slider=') !== false ? 'YES' : 'NO')."\n";
$p = strpos($html,'data-slider=');
if($p !== false) echo "data-slider value: ".substr($html,$p,300)."\n";
$p2 = strpos($html,'data-images=');
if($p2 !== false) echo "data-images value: ".substr($html,$p2,200)."\n";
