<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;
//载入Composer的自动加载autoload文件(tp6的核心框架类已更改为Composer方式维护)
require __DIR__ . '/../vendor/autoload.php';

// 执行HTTP应用并响应
// 触发Container的魔术方法__get获取http实例
$http = (new App())->http;
// 这里才会通过http调用app基础类的初始化操作, 包括环境变量,配置文件,事件注册及服务的注册和启动
$response = $http->run();

$response->send();

$http->end($response);
