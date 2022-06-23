<?php
$fileInputName1 = $_SERVER['DOCUMENT_ROOT'] . '/upload/input1.txt';
$fileInputName2 = $_SERVER['DOCUMENT_ROOT'] . '/upload/input2.txt';
$fileOutputName1 = $_SERVER['DOCUMENT_ROOT'] . '/upload/output1.txt';
$fileOutputName2 = $_SERVER['DOCUMENT_ROOT'] . '/upload/output2.txt';

$arFileInput1 = file($fileInputName1);
$arFileInput2 = file($fileInputName2);

$fileInput1 = fopen($fileInputName1, 'r');
$fileInput2 = fopen($fileInputName2, 'r');

$arFileOutput1 = [];
$arFileOutput2 = [];
$sDuplicateRows1 = '';
$sDuplicateRows2 = '';

if ($fileInput1 && $fileInput2) {
    while (!feof($fileInput1) || !feof($fileInput2)) {
        $string1 = fgets($fileInput1);
        $string2 = fgets($fileInput2);
        if ($string1 != $string2 && $string1 && $string2) {
            if (!in_array($string1, $arFileOutput2) && $string1 != $sDuplicateRows1) {
                $arFileOutput1[] = $string1;
                $sDuplicateRows1 = '';
            } elseif (in_array($string1, $arFileOutput2)) {
                // If the value is already in another array, delete it from another array:
                cleanArrayFromDuplicates($string1, $arFileOutput2);
                // We've come to a coincidence. Since the data in the files are presented lexicographically,
                //the previous values of this array will not be found in another array.
                //We can write them to a file and clear the array:
                addArrayToFile($fileOutputName1, $arFileOutput1);
            }
            if (!in_array($string2, $arFileOutput1) && $string2 != $sDuplicateRows2) {
                $arFileOutput2[] = $string2;
                $sDuplicateRows2 = '';
            } elseif (in_array($string2, $arFileOutput1)) {
                cleanArrayFromDuplicates($string2, $arFileOutput1);
                addArrayToFile($fileOutputName2, $arFileOutput2);
            }
            // If the number of rows in file is different:
            // The first file is over:
        } elseif (!$string1) {
            if (!in_array($string2, $arFileOutput1) && $string2 != $sDuplicateRows2) {
                $arFileOutput2[] = $string2;
                $sDuplicateRows2 = '';
            } else {
                cleanArrayFromDuplicates($string2, $arFileOutput1);
                addArrayToFile($fileOutputName2, $arFileOutput2);
            }
            //The second file is over:
        } elseif (!$string2) {
            if (!in_array($string1, $arFileOutput2) && $string1 != $sDuplicateRows1) {
                $arFileOutput1[] = $string1;
            } else {
                cleanArrayFromDuplicates($string1, $arFileOutput2);
                addArrayToFile($fileOutputName1, $arFileOutput1);
            }
        } elseif ($string1 === $string2) {
            //If the rows are repeated in arrays:
            $sDuplicateRows1 = $string1;
            $sDuplicateRows2 = $string2;
            if (in_array($string1, $arFileOutput1)) {
                unset($arFileOutput1[array_search($string1, $arFileOutput1)]);
            }
            if (in_array($string2, $arFileOutput2)) {
                unset($arFileOutput2[array_search($string2, $arFileOutput2)]);
            }
        }
    }
    if ($arFileOutput1) {
        file_put_contents($fileOutputName1, $arFileOutput1, FILE_APPEND);
    }
    if ($arFileOutput2) {
        file_put_contents($fileOutputName2, $arFileOutput2, FILE_APPEND);
    }
}
fclose($fileInput1);
fclose($fileInput2);

/**
* @param string $string   a line from the file
* @param array  $arOutput array of values passed to the file
*/
function cleanArrayFromDuplicates(string $string, array &$arOutput)
{
    $arCounter = array_count_values($arOutput);
    //There may be several duplicate elements that should be removed:
    for ($i = 1; $i <= $arCounter[$string]; $i++) {
        unset($arOutput[array_search($string, $arOutput)]);
    }
}

/**
 * @param string $fileName link to file
 * @param array  $arOutput array of values passed to the file
 */
function addArrayToFile(string $fileName, array &$arOutput)
{
    file_put_contents($fileName, $arOutput, FILE_APPEND);
    $arOutput = [];
}