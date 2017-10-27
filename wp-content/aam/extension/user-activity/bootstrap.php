<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

if (defined('AAM_KEY') && !defined('AAM_USER_ACTIVITY')) {
    //define extension constant as it's version #
    define('AAM_USER_ACTIVITY', '1.3');

    //register activate and extension classes
    $basedir = dirname(__FILE__);
    AAM_Autoloader::add('AAM_UserActivity', $basedir . '/UserActivity.php');
    AAM_Autoloader::add('AAM_UserActivity_List_Table', $basedir . '/Table.php');
    AAM_Autoloader::add('AAM_UserActivity_Activation', $basedir . '/Activation.php');
    
    if (class_exists('AAM_UserActivity_Activation')) {
        AAM_UserActivity_Activation::run();
    }

    AAM_UserActivity::bootstrap();
}