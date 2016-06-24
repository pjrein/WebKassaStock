<?php

echo 'test';
$test = array(1,2,3,4,5);
echo "<pre>";
print_r($test);
echo "<pre>";
unset($test[1]);
echo "<pre>";
print_r($test);
echo "<pre>";
$test = array_values($test);
echo "<pre>";
print_r($test);
echo "<pre>";
?>
