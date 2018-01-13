<?php 
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

// bussinessWorker 进程
$worker = new BusinessWorker();
// worker名称
$worker->name = 'LogInBusinessWorker_test';
// bussinessWorker进程数量
$worker->count = 3;
// 服务注册地址
$worker->registerAddress = '127.0.0.1:11000';
// 设置监控服务端业务超时时间（单位秒）。不设置默认是30秒，设置为0表示不监控。
$worker->processTimeout = 5;

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START')) {
    Worker::runAll();
}

