<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

if (defined('AAM_KEY') && !defined('AAM_CONFIGPRESS')) {
    //define extension constant as it's version #
    define('AAM_CONFIGPRESS', '1.3.2');

    //register activate and extension classes
    $basedir = dirname(__FILE__);
    AAM_Autoloader::add('AAM_ConfigPress', $basedir . '/ConfigPress.php');
    AAM_Autoloader::add('AAM_ConfigPress_Reader', $basedir . '/Reader.php');
    AAM_Autoloader::add('AAM_ConfigPress_Evaluator', $basedir . '/Evaluator.php');

    AAM_ConfigPress::bootstrap();
}