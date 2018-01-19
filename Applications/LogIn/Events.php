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

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{

//	public static $db = null;
//
//	/**
//	   * 进程启动后初始化数据库连接
//	   */
//	public static function onWorkerStart($worker)
//	{
//	    self::$db = new Workerman\MySQL\Connection('host', 'port', 'user', 'password', 'db_name');
//	}

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id) {
        // 向当前client_id发送数据 
		Gateway::sendToCurrentClient("Hello $client_id\n");
		
//        // 向所有人发送
//        Gateway::sendToAll("$client_id login\n");
    }

/*	// 数据结构
	public static $empty = array(
		'head'          => 0x55AA,  //2B
		'len'           => 0,       //2B
		'cmd'           => 0,       //1B
		'dat'           => 0,       //nB
		'crc'           => 0x0000,  //2B
		'end'           => 0xFF,    //1B
	);
*/
	/**
	 * 当客户端发来消息时触发
	 * @param int $client_id 连接id
	 * @param mixed $message 具体消息
	 */
    public static function onMessage($client_id, $message) {
    	if ($message === null)  //异常数据，跳过
	        return;

		Gateway::sendToCurrentClient("message:".bin2hex($message)."\r");

//	    if (stripos($message, "\x55\xAA") === 0) {
			$rec = unpack("C*", $message);
			switch ($rec[0])    //cmd
			{
				case 0x00:  //登录

					break;

				case 0x01:  //
					break;

				default:
					break;
			}

		    Gateway::sendToCurrentClient("data receive ok!!\r");
//		}
//		else {
//			Gateway::sendToCurrentClient("data receive err!!\r");
//		}

    }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id) {
       // 向所有人发送 
//       GateWay::sendToAll("$client_id logout");
   }
}
