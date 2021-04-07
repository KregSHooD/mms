<?php
/**
 * CLI执行
 * User: Clake
 * Date: 15/6/7
 * Time: 00:59
 */
define('CK_DEBUG',true);
define('CK_DEF_CONF','dev');

$path = dirname(__FILE__);
chdir($path);

require '../../ck_core/trigger.php';

\CK\Core\App::inst(\CK\Core\App::MODE_CLI)->start();