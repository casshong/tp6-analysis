<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace think\initializer;

use think\App;
use think\service\ModelService;
use think\service\PaginatorService;
use think\service\ValidateService;

/**
 * 注册系统服务
 */
class RegisterService
{

    protected $services = [
        PaginatorService::class,
        ValidateService::class,
        ModelService::class,
    ];

    public function init(App $app)
    {
        /**
         * 加载系统扩展服务定义文件
         * 当用户通过composer安装框架扩展服务时, 该扩展的服务将会添加至此文件中.
         * (比如安装官方swoole扩展会在此应用文件中添加'think\swoole\Service'信息)
         */
        $file = $app->getRootPath() . 'vendor/services.php';
        /**
         * 系统服务
         * think\service\PaginatorService:分页服务
         * think\service\ValidateService:验证服务
         * think\service\ModelService:模型服务
         */
        $services = $this->services;

        if (is_file($file)) {
            $services = array_merge($services, include $file);
        }
        //  调用app基础类的register方法注册服务
        foreach ($services as $service) {
            if (class_exists($service)) {
                $app->register($service);
            }
        }
    }
}
