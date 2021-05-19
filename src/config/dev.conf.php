<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 15/6/7
 * Time: 04:28
 */

/**
 * 应用开发配置文件
 */
return [
    //模块配置
    'module' => [],
    //控制器配置项
    'controller'=>[],
    //组件配置项
    'component'=>[
        'Common\Component\Upload'=>[
            'access_key_id'=>'LTAIYYdCYXJLSQr2',
            'access_key_secret'=>'gt8iMQtBKahFgbAVJUMD1ivQQUqG0r',
            'domain'=>'http://oss.weiwubao.com',
            'is_custom'=>true,
            'bucket'=>'weiwubao',
            'dir_path'=>'upload/image/ticket/'
        ],
        'CK\Api\Weixin'=>[
            'appid'=>'wx51fb19688ec0020b',
            'secret'=>'4d1b7eb00e5e935d5bca761483aba718'
        ],
        'CK\Util\DayuSms'=>[
            'app_key'=>'23467985',
            'secret_key'=>'183a827e4ea94a7a858114c71ec6b836'
        ]
    ],
    //路由配置项
    'router'=>[
        //默认模块
        'def_module' => 'Site',
        //默认控制器
        'def_controller'=>'Index',
        //默认动作
        'def_action'=>'Index',
        //允许执行模块
        'allowed_modules'=>['Site','Admin','Debug','Res','ResWap','Wap','Pay','Task','Cms'],
        //URL地址分区配置模板
        'url_partition'=>'{module}/{controller}/{action}'
    ],
    //数据库配置
    'database'=>[
        'main' => [
            //要连接的数据库类型,DBA::DBA_MYSQL|DBA::DBA_SQLITE|DBA::DBA_MSSQL
            'type'=>\CK\Database\DBA::DBA_MYSQL,
            //表前缀
            'db_prefix'=>'',
            'master' => [
                'db_host'=>'168.168.1.10',
                'db_name'=>'zc_cms',
                'db_user'=>'db_user',
                'db_pass'=>'pOe93jfn7hU!7318fVghB',
                //可选
                'db_port'=>3306,
                'db_charset'=>'utf8mb4'
            ],
            'slave'=>[]
        ],
        'center' => [
            //要连接的数据库类型,DBA::DBA_MYSQL|DBA::DBA_SQLITE|DBA::DBA_MSSQL
            'type'=>\CK\Database\DBA::DBA_MYSQL,
            //表前缀
            'db_prefix'=>'',
            'master' => [
                'db_host'=>'localhost',
                'db_name'=>'zc_center',
                'db_user'=>'db_user',
                'db_pass'=>'pOe93jfn7hU!7318fVghB',
                //可选
                'db_port'=>3306,
                'db_charset'=>'utf8mb4'
            ],
            'slave'=>[]
        ],
    ],
    //浏览器 COOKIE 配置
    'cookie'=>[
        //Cookie 前缀
        'prefix'=>'mms_',
        //Cookie 作用路径
        'path'=>'/',
        //Cookie 作用域
        'domain'=>'',
        //Cookie 过期时间
        'expire'=>null,
        //加密KEY
        'cipher'=>'mms.zcxf.com'
    ],
    //缓存配置
    'cache'=>[
        'type'=>'memcached',//memcached or file
        'conf'=>[
            'prefix'=>'mms_',
            'servers'=>[['host'=>'127.0.0.1','port'=>11211]]
        ]
    ]
];