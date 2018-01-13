<?php
use \crc16

namespace Protocols;
class SmartLifeTcp	//TCP透传协议
{
    /**
     * 检查包的完整性
     * 如果能够得到包长，则返回包的在buffer中的长度，否则返回0继续等待数据
     * 如果协议有问题，则可以返回false，当前客户端连接会因此断开
     * @param string $buffer
     * @return int
     */
	public static $intput_pos = 0;
	public static $intput_len = 0;

    public static function input($buffer)
    {
		//最小指令长度	Head(2B)+Lenth(2B)+CMD(1B)+CRC(2B)
		if (strlen($buffer) < 7)
			return 0;

		// 获取起始码位置
        $intput_pos = strpos($buffer, "\x55\x57");
        // 起始码，无法得知包长，返回0继续等待数据
        if($intput_pos === false)
			return 0;

		// 协议数据长度
		$intput_len = ord(substr($buffer, $intput_pos + 2, 2));

		// 数据总长不足（数据未接收完成 或 接收异常）
		if (strlen($buffer) - $intput_pos < $intput_len + 4)
			return 0;
			
		// 有效数据
		$dat = substr($buffer, $intput_pos, $intput_len + 4);

		echo "receive:\r"
		echo "{len:".$intput_len."\r";
		echo "hex:".bin2hex($dat)."}\r"
		
		$crc = ord(substr($dat, $intput_len + 1, 2));
		if ($crc == crc16($dat))
			return ($intput_len + 4);
    }

    /**
     * 打包，当向客户端发送数据的时候会自动调用
     * @param string $buffer
     * @return string
     */
    public static function encode($buffer)
    {
		// 内容长度
		$len = (string)(strlen($buffer) + 2);
		$dat = "\x55+\x57".$len.$buffer;
		$crc = crc16($dat);

        return ($dat.$crc);
    }

    /**
     * 解包，当接收到的数据字节数等于input返回的值（大于0的值）自动调用
     * 并传递给onMessage回调函数的$data参数
     * @param string $buffer
     * @return string
     */
    public static function decode($buffer)
    {
		// 有效数据
		$dat = substr($buffer, $intput_pos, $intput_len + 4);

        // 返回有效数据
        return $dat;
    }
}