<?php

/**
 * WEB网站DEMO首页
 * User: Clake
 * Date: 15/6/7
 * Time: 00:59
 */
define('CK_DEBUG',true);
define('CK_DEF_CONF','dev');

require '../../ck_core/trigger.php';

define('CMS_TEMPLATE_PATH',realpath(APP_PATH.'../view/cms').'/');

\CK\Core\App::inst()->start();
