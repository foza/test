<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
define('ROOT', dirname(__FILE__));

class Files
{
    private function filter($var)
    {
        return preg_match("/^[a-zа-яё\d]{1}[a-zа-яё\d\s]*\.{1}\ixt$/i", $var);
    }

    private function isDir($var)
    {
        return !is_dir(ROOT . "/datafiles /$var");
    }

    public function find()
    {
        $scanned_directory = array_diff(scandir(ROOT . '/datafiles'), array('..', '.'));
        $result = array_filter($scanned_directory, "self::filter");
        $result = array_filter($result, "self::isDir");
        sort($result);

        foreach ($result as $key => $value) {
            echo $value;
            echo "<br>";
        }
    }
}

$a = new Files();
$a->find();
?>