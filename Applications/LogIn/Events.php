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
//     * 进程启动后初始化数据库连接
//     */
//    public static function onWorkerStart($worker)
//    {
//        self::$db = new Workerman\MySQL\Connection('host', 'port', 'user', 'password', 'db_name');
//    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id) {
        // 向当前client_id发送数据 
		Gateway::sendToClient($client_id, "Hello $client_id\n");
		
//        // 向所有人发送
//        Gateway::sendToAll("$client_id login\n");
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message) {
//		do {
//			$sum = 0;
//			// 获取头码位置
//			$pos = strpos($buffer, "\x55\x57");		
//			// 头码，无法得知包长
//			if($pos === false)
//				break;
//
//			// 字符长度
//			$len = ord(substr($message, $pos, 1)) + 3;	
//
//			// 数据总长不足
//			if (strlen($message) < $pos + $len + 1)
//				break;
//			
//			echo bin2hex($message);
//			
//			for ($i = $pos; $i < $pos + $len; $i++)
//			{
//				$sum += ord(substr($message, $i, 1));
////				Gateway::sendToClient($client_id, bin2hex($sum)."\r");
//			}
//
//			if ($sum == ord(substr($message, $len, 1)))
//			{
				Gateway::sendToClient($client_id,"success\r");
//				return;
//			}
//		}while(0);
//		
//		Gateway::sendToClient($client_id,"error\r");
//		Gateway::sendToAll(bin2hex($message));
//		Gateway::closeCurrentClient();
		
        // 向所有人发送 
//        Gateway::sendToAll("$client_id said $message");
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
