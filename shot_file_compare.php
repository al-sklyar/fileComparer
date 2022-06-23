<?php
$fileInputName1 = $_SERVER['DOCUMENT_ROOT'] . '/upload/input1.txt';
$fileInputName2 = $_SERVER['DOCUMENT_ROOT'] . '/upload/input2.txt';
$fileOutputName1 = $_SERVER['DOCUMENT_ROOT'] . '/upload/output1.txt';
$fileOutputName2 = $_SERVER['DOCUMENT_ROOT'] . '/upload/output2.txt';

$arFileInput1 = file($fileInputName1);
$arFileInput2 = file($fileInputName2);

$arFileOutput1 = array_diff($arFileInput1,$arFileInput2);
$arFileOutput2 = array_diff($arFileInput2,$arFileInput1);

file_put_contents($fileOutputName1, $arFileOutput1);
file_put_contents($fileOutputName2, $arFileOutput2);