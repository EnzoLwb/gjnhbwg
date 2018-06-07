<?php
/**
 * composer require workerman/gateway-worker
 *
 * run with command
 * php workerman.php restart
 *
 * 后台运行
 * /etc/rc.d/rc.local 加入
 * php /home/www/wwwroot/headking/workerman.php restart -d 1>/home/www/wwwroot/headking/storage/logs/workerman_output.log 2>&1 &
 *
 * php /alidata1/wwwroot/tnwz/workerman.php restart -d 1>/alidata1/wwwroot/tnwz/storage/logs/workerman_output.log 2>&1 &
 *
 * 客户端连接测试
 * curl -v telnet://127.0.0.1:9100
 *
 * @author lxp 20170126
 */

ini_set('display_errors', 'on');

use Workerman\Worker;
use GatewayWorker\Register;
use GatewayWorker\Gateway;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Lib\Gateway AS GatewayLib;
use Workerman\Lib\Timer;

// 检查扩展
if (!extension_loaded('pcntl')) {
	exit("Please install pcntl extension.\n");
}

if (!extension_loaded('posix')) {
	exit("Please install posix extension.\n");
}

// 初始化Laravel框架
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

/**
 * 相关业务逻辑处理
 *
 * @author lxp
 */
class Events
{
	/**
	 * 当客户端连接时触发
	 *
	 * @param int $client_id 连接id
	 */
	public static function onConnect($client_id)
	{
		echo '[' . date('Y-m-d H:i:s') . '] ' . $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'] . " connected 1...\n";

	}

	/**
	 * 当客户端发来消息时触发
	 *
	 * @param int $client_id 连接id
	 * @param mixed $message 具体消息
	 */
	public static function onMessage($client_id, $message)
	{
		echo '[' . date('Y-m-d H:i:s') . '] ' . $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'] . " get message...\n";
		// 客户端传递的是json数据

		$message_data = json_decode($message, true);
		if(!$message_data) return ;
		dump($message);
		// 根据类型执行不同的业务
		switch($message_data['type'])
		{
			// 客户端回应服务端的心跳
			case 'pong':	return;
			// 创建群组
			case 'create_group':
				echo 'test_create_group1111';
				$uid=$message_data['uid'];
				$group_id=rand(10000,99999);
				$group=new \App\Models\Group;
				$group->holder = $uid;
				$group->name = $message_data['group_name'];
				$group->create_time = time();
				$group->group_id= $group_id;
				$group->save();
				//将群主加入该群 并且绑定client_id
				$group_member=new \App\Models\GroupMember;
				$group_member->member_id=$uid;
				$group_member->add_time=time();
				$group_member->group_id=$group_id;
				$group_member->save();
				//绑定机器号
				GatewayLib::bindUid($client_id, $uid);
				//对他说
				$new_message=['content'=>"你已成功创建并加入群组"];
				Gateway::sendToClient($client_id, json_encode($new_message));
				Gateway::sendToUid($uid, json_encode($new_message));
				break;
			// 加入群组  message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
			case 'login':
				// 判断是否有房间号
				if(!isset($message_data['room_id']))
				{
					throw new \Exception("\$message_data['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
				}
				// 把房间号昵称放到session中
				$room_id = $message_data['room_id'];
				$client_name = htmlspecialchars($message_data['client_name']);
				$_SESSION['room_id'] = $room_id;
				$_SESSION['client_name'] = $client_name;

				// 获取房间内所有用户列表
				$clients_list = Gateway::getClientSessionsByGroup($room_id);
				foreach($clients_list as $tmp_client_id=>$item)
				{
					$clients_list[$tmp_client_id] = $item['client_name'];
				}
				$clients_list[$client_id] = $client_name;

				// 转播给当前房间的所有客户端，xx进入聊天室 message {type:login, client_id:xx, name:xx}
				$new_message = array('type'=>$message_data['type'], 'client_id'=>$client_id, 'client_name'=>htmlspecialchars($client_name), 'time'=>date('Y-m-d H:i:s'));
				Gateway::sendToGroup($room_id, json_encode($new_message));
				Gateway::joinGroup($client_id, $room_id);

				// 给当前用户发送用户列表
				$new_message['client_list'] = $clients_list;
				Gateway::sendToCurrentClient(json_encode(``));
				return;

			// 客户端发言 message: {type:say, to_client_id:xx, content:xx}
			case 'say':
				// 非法请求
				if(!isset($_SESSION['room_id']))
				{
					throw new \Exception("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
				}
				$room_id = $_SESSION['room_id'];
				$client_name = $_SESSION['client_name'];

				// 私聊
				if($message_data['to_client_id'] != 'all')
				{
					$new_message = array(
						'type'=>'say',
						'from_client_id'=>$client_id,
						'from_client_name' =>$client_name,
						'to_client_id'=>$message_data['to_client_id'],
						'content'=>"<b>对你说: </b>".nl2br(htmlspecialchars($message_data['content'])),
						'time'=>date('Y-m-d H:i:s'),
					);
					Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));
					$new_message['content'] = "<b>你对".htmlspecialchars($message_data['to_client_name'])."说: </b>".nl2br(htmlspecialchars($message_data['content']));
					return Gateway::sendToCurrentClient(json_encode($new_message));
				}

				$new_message = array(
					'type'=>'say',
					'from_client_id'=>$client_id,
					'from_client_name' =>$client_name,
					'to_client_id'=>'all',
					'content'=>nl2br(htmlspecialchars($message_data['content'])),
					'time'=>date('Y-m-d H:i:s'),
				);
				return Gateway::sendToGroup($room_id ,json_encode($new_message));
		}
	}

	/**
	 * 当用户断开连接时触发
	 *
	 * @param int $client_id 连接id
	 */
	public static function onClose($client_id)
	{
		echo '[' . date('Y-m-d H:i:s') . '] [' . $client_id . '] ' . $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'] . " disconnected\n";
	}

	/**
	 * onWorkerStop
	 *
	 * @author lxp
	 * @param $businessWorker
	 */
	public static function onWorkerStop($businessWorker)
	{
	}
}

$registerIP = env('WM_REGISTER_IP', '127.0.0.1');
$registerPort = env('WM_REGISTER_PORT', '1238');
$gatewayPort = env('WM_GATEWAY_PORT', '9100');

// register 服务必须是text协议
$register = new Register("text://0.0.0.0:{$registerPort}");

// gateway 进程，这里使用Text协议，可以用telnet测试
$gateway = new Gateway("Websocket://0.0.0.0:{$gatewayPort}");
// gateway名称，status方便查看
$gateway->name = 'DM_Gateway';
// gateway进程数
$gateway->count = 4;
// gateway起始端口
$gateway->startPort = env('WM_GATEWAY_STARTPORT', 3000);
// 服务注册地址
$gateway->registerAddress = "$registerIP:$registerPort";
// 心跳间隔
//$gateway->pingInterval = 10;
// 心跳数据
//$gateway->pingData = '{"type":"ping"}';

// bussinessWorker 进程
$worker = new BusinessWorker();
// worker名称
$worker->name = 'DM_BusinessWorker';
// bussinessWorker进程数量
$worker->count = 4;
// 服务注册地址
$worker->registerAddress = "$registerIP:$registerPort";

// Workerman运行日志
Worker::$logFile = __DIR__ . '/storage/logs/workerman.log';
// 运行所有服务
Worker::runAll();
