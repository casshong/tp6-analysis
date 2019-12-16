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
namespace think;

use Closure;
use Exception;
use Throwable;

class Pipeline
{
    protected $passable;

    protected $pipes = [];

    protected $exceptionHandler;

    /**
     * 初始数据
     * 设置管道需要传递的参数
     * @param $passable
     * @return $this
     */
    public function send($passable)
    {
        $this->passable = $passable;
        return $this;
    }

    /**
     * 调用栈
     * 设置需要被调用的对象数组, 最终在执行时会被一层层按顺序调用, 调用过程中会传递send方法所设置的传递参数
     * @param $pipes
     * @return $this
     */
    public function through($pipes)
    {
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();
        return $this;
    }

    /**
     * 执行
     * 开始顺序执行through所设置的调用对象,并且最终由传入的$destination匿名函数处理返回
     * @param Closure $destination
     * @return mixed
     */
    public function then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            function ($passable) use ($destination) {
                try {
                    return $destination($passable);
                } catch (Throwable | Exception $e) {
                    return $this->handleException($passable, $e);
                }
            });

        return $pipeline($this->passable);
    }

    /**
     * 设置异常处理器
     * @param callable $handler
     * @return $this
     */
    public function whenException($handler)
    {
        $this->exceptionHandler = $handler;
        return $this;
    }

    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                try {
                    return $pipe($passable, $stack);
                } catch (Throwable | Exception $e) {
                    return $this->handleException($passable, $e);
                }
            };
        };
    }

    /**
     * 异常处理
     * @param $passable
     * @param $e
     * @return mixed
     */
    protected function handleException($passable, Throwable $e)
    {
        if ($this->exceptionHandler) {
            return call_user_func($this->exceptionHandler, $passable, $e);
        }
        throw $e;
    }

}
