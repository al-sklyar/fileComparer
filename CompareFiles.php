<?php

namespace FilesWork;

class CompareFiles
{
    private static $instance;

    private static string $fileInputName1;
    private static string $fileInputName2;
    private static string $fileOutputName1;
    private static string $fileOutputName2;

    private function __construct(string $inputFIle1, string $inputFile2, string $outputFile1, string $outputFile2, string $dir = 'upload')
    {
        self::$fileInputName1 = $_SERVER['DOCUMENT_ROOT'] . '/' . $dir . '/' . $inputFIle1;
        self::$fileInputName2 = $_SERVER['DOCUMENT_ROOT'] . '/' . $dir . '/' . $inputFile2;
        self::$fileOutputName1 = $_SERVER['DOCUMENT_ROOT'] . '/' . $dir . '/' . $outputFile1;
        self::$fileOutputName2 = $_SERVER['DOCUMENT_ROOT'] . '/' . $dir . '/' . $outputFile2;

        self::workWithFiles();
    }

    public static function createUniqueFiles(string $inputFIle1, string $inputFile2, string $outputFile1, string $outputFile2, string $dir = 'upload')
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($inputFIle1, $inputFile2, $outputFile1, $outputFile2, $dir);
        }
        return self::$instance;
    }

    private static function workWithFiles()
    {
        $fileInput1 = fopen(self::$fileInputName1, 'r');
        $fileInput2 = fopen(self::$fileInputName2, 'r');

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
                        self::cleanArrayFromDuplicates($string1, $arFileOutput2);
                        // We've come to a coincidence. Since the data in the files are presented lexicographically,
                        //the previous values of this array will not be found in another array.
                        //We can write them to a file and clear the array:
                        self::addArrayToFile(self::$fileOutputName1, $arFileOutput1);
                    }
                    if (!in_array($string2, $arFileOutput1) && $string2 != $sDuplicateRows2) {
                        $arFileOutput2[] = $string2;
                        $sDuplicateRows2 = '';
                    } elseif (in_array($string2, $arFileOutput1)) {
                        self::cleanArrayFromDuplicates($string2, $arFileOutput1);
                        self::addArrayToFile(self::$fileOutputName2, $arFileOutput2);
                    }
                    // If the number of rows in file is different:
                    // The first file is over:
                } elseif (!$string1) {
                    if (!in_array($string2, $arFileOutput1) && $string2 != $sDuplicateRows2) {
                        $arFileOutput2[] = $string2;
                        $sDuplicateRows2 = '';
                    } else {
                        self::cleanArrayFromDuplicates($string2, $arFileOutput1);
                        self::addArrayToFile(self::$fileOutputName2, $arFileOutput2);
                    }
                    //The second file is over:
                } elseif (!$string2) {
                    if (!in_array($string1, $arFileOutput2) && $string1 != $sDuplicateRows1) {
                        $arFileOutput1[] = $string1;
                    } else {
                        self::cleanArrayFromDuplicates($string1, $arFileOutput2);
                        self::addArrayToFile(self::$fileOutputName1, $arFileOutput1);
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
                file_put_contents(self::$fileOutputName1, $arFileOutput1, FILE_APPEND);
            }
            if ($arFileOutput2) {
                file_put_contents(self::$fileOutputName2, $arFileOutput2, FILE_APPEND);
            }
        }
        fclose($fileInput1);
        fclose($fileInput2);
    }

    /**
     * @param string $string   a line from the file
     * @param array  $arOutput array of values passed to the file
     * @param string  $arDuplicate  line with duplicate values
     */

    private static function cleanArrayFromDuplicates(string $string, array &$arOutput)
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
    private static function  addArrayToFile(string $fileName, array &$arOutput)
    {
        file_put_contents($fileName, $arOutput, FILE_APPEND);
        $arOutput = [];
    }
}