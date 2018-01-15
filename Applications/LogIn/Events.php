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
	public static function crc16(&$ptr)
	{
		$crc = 0x0000;
		$crc_table = array(
			0x0,  	0x1021,  0x2042,  0x3063,  0x4084,  0x50a5,  0x60c6,  0x70e7,
			0x8108,  0x9129,  0xa14a,  0xb16b,  0xc18c,  0xd1ad,  0xe1ce,  0xf1ef,
			0x1231,  0x210,  0x3273,  0x2252,  0x52b5,  0x4294,  0x72f7,  0x62d6,
			0x9339,  0x8318,  0xb37b,  0xa35a,  0xd3bd,  0xc39c,  0xf3ff,  0xe3de,
			0x2462,  0x3443,  0x420,  0x1401,  0x64e6,  0x74c7,  0x44a4,  0x5485,
			0xa56a,  0xb54b,  0x8528,  0x9509,  0xe5ee,  0xf5cf,  0xc5ac,  0xd58d,
			0x3653,  0x2672,  0x1611,  0x630,  0x76d7,  0x66f6,  0x5695,  0x46b4,
			0xb75b,  0xa77a,  0x9719,  0x8738,  0xf7df,  0xe7fe,  0xd79d,  0xc7bc,
			0x48c4,  0x58e5,  0x6886,  0x78a7,  0x840,  0x1861,  0x2802,  0x3823,
			0xc9cc,  0xd9ed,  0xe98e,  0xf9af,  0x8948,  0x9969,  0xa90a,  0xb92b,
			0x5af5,  0x4ad4,  0x7ab7,  0x6a96,  0x1a71,  0xa50,  0x3a33,  0x2a12,
			0xdbfd,  0xcbdc,  0xfbbf,  0xeb9e,  0x9b79,  0x8b58,  0xbb3b,  0xab1a,
			0x6ca6,  0x7c87,  0x4ce4,  0x5cc5,  0x2c22,  0x3c03,  0xc60,  0x1c41,
			0xedae,  0xfd8f,  0xcdec,  0xddcd,  0xad2a,  0xbd0b,  0x8d68,  0x9d49,
			0x7e97,  0x6eb6,  0x5ed5,  0x4ef4,  0x3e13,  0x2e32,  0x1e51,  0xe70,
			0xff9f,  0xefbe,  0xdfdd,  0xcffc,  0xbf1b,  0xaf3a,  0x9f59,  0x8f78,
			0x9188,  0x81a9,  0xb1ca,  0xa1eb,  0xd10c,  0xc12d,  0xf14e,  0xe16f,
			0x1080,  0xa1,  0x30c2,  0x20e3,  0x5004,  0x4025,  0x7046,  0x6067,
			0x83b9,  0x9398,  0xa3fb,  0xb3da,  0xc33d,  0xd31c,  0xe37f,  0xf35e,
			0x2b1,	0x1290,  0x22f3,  0x32d2,  0x4235,  0x5214,  0x6277,  0x7256,
			0xb5ea,  0xa5cb,  0x95a8,  0x8589,  0xf56e,  0xe54f,  0xd52c,  0xc50d,
			0x34e2,  0x24c3,  0x14a0,  0x481,  0x7466,  0x6447,  0x5424,  0x4405,
			0xa7db,  0xb7fa,  0x8799,  0x97b8,  0xe75f,  0xf77e,  0xc71d,  0xd73c,
			0x26d3,  0x36f2,  0x691,  0x16b0,  0x6657,  0x7676,  0x4615,  0x5634,
			0xd94c,  0xc96d,  0xf90e,  0xe92f,  0x99c8,  0x89e9,  0xb98a,  0xa9ab,
			0x5844,  0x4865,  0x7806,  0x6827,  0x18c0,  0x8e1,  0x3882,  0x28a3,
			0xcb7d,  0xdb5c,  0xeb3f,  0xfb1e,  0x8bf9,  0x9bd8,  0xabbb,  0xbb9a,
			0x4a75,  0x5a54,  0x6a37,  0x7a16,  0xaf1,  0x1ad0,  0x2ab3,  0x3a92,
			0xfd2e,  0xed0f,  0xdd6c,  0xcd4d,  0xbdaa,  0xad8b,  0x9de8,  0x8dc9,
			0x7c26,  0x6c07,  0x5c64,  0x4c45,  0x3ca2,  0x2c83,  0x1ce0,  0xcc1,
			0xef1f,  0xff3e,  0xcf5d,  0xdf7c,  0xaf9b,  0xbfba,  0x8fd9,  0x9ff8,
			0x6e17,  0x7e36,  0x4e55,  0x5e74,  0x2e93,  0x3eb2,  0xed1,  0x1ef0);

		for ($i = 0; $i < strlen($ptr); $i++)
			$crc =  $crc_table[(($crc>>8) ^ ord($ptr[$i]))] ^ (($crc<<8) & 0x00FFFF);
		return $crc;
	}

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
		Gateway::sendToCurrentClient("Hello $client_id\n");
		
//        // 向所有人发送
//        Gateway::sendToAll("$client_id login\n");
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message) {
	do {
		//最小指令长度	Head(2B)+Lenth(2B)+CMD(1B)+CRC(2B)
		$num = strlen($message);
		Gateway::sendToCurrentClient("len:".$num."\r");
		Gateway::sendToCurrentClient("hex:".bin2hex($message)."\r");
		if ($num < 7)
		{
			Gateway::sendToCurrentClient("lenth too short\r");
			break;
		}

		// 获取起始码位置
		$intput_pos = strpos($message, "\x55\x57");
		Gateway::sendToCurrentClient("datpos:".$intput_pos."\r");  
       	// 起始码，无法得知包长，返回0继续等待数据
       	if($intput_pos === false)
		{
			Gateway::sendToCurrentClient("none start\r");
			break;
		}

		// 协议数据长度
		$intput_len = ord(substr($message, $intput_pos + 2, 1)) * 256 +  ord(substr($message, $intput_pos + 3, 1));
		Gateway::sendToCurrentClient("datlen:".$intput_len."\r");  
		// 指令长度异常
		if ($intput_len < 2)
		{
			Gateway::sendToCurrentClient("datlen too short\r");
			break;
		}
		// 数据总长不足（数据未接收完成 或 接收异常）
		if (strlen($message) - $intput_pos < $intput_len + 4)
		{
			Gateway::sendToCurrentClient("message too short\r");
			break;
		}
			
		// 有效数据
		$dat = substr($message, $intput_pos, $intput_len + 4);

//		Gateway::sendToCurrentClient("receive:\r");
//		Gateway::sendToCurrentClient("{\r\tlen:".$intput_len."\r");
//		Gateway::sendToCurrentClient("\thex:".bin2hex($dat)."\r}\r");
		
		$crc = ord(substr($dat, $intput_len + 2, 1)) * 256 + ord(substr($dat, $intput_len + 3, 1));
		Gateway::sendToCurrentClient("crc:".dechex($crc)."\r");

		$strCalc = substr($dat,0,strlen($dat) - 2);
		$check = self::crc16($strCalc);
		Gateway::sendToCurrentClient("calc:".dechex($check)."\r");
		if ($crc == $check)
		{
			Gateway::sendToCurrentClient("success\r");
			return;
		}
//		else
//		{
//			Gateway::sendToCurrentClient("CRC true = ".crc16($dat)."\r");
//			Gateway::sendToCurrentClient("CRC check error\r");;
//			break;
//		}
	}while(0);

	Gateway::sendToCurrentClient("error\r");
	Gateway::closeCurrentClient();
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
