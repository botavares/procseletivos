<?php
/**
 * phpwkhtmltopdf config
 * @link https://gist.github.com/DykiSA/1b768c3e296a983a0b398b2b3a08a07d
 * @source https://github.com/mikehaertl/phpwkhtmltopdf
 */

/**
 * path to the wkhtmltopdf executable
 */
$config['phpwkhtmltopdf']['binary'] = 'C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf';

/**
 * path to store generated temp files
 */
$config['phpwkhtmltopdf']['tmpDir'] = APPPATH . '../temp/';


$config['phpwkhtmltopdf']['commandOptions'] = [
    'useExec' => true
];