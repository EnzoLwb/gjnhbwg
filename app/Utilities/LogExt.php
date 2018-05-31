<?php

namespace App\Utilities;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Redis;

/**
 * 日志方法
 *
 * @author lxp 20151219
 */
class LogExt
{

	const END_STR = '********************************************************************';
	private $logObj;
	/**
	 * 允许的错误等级
	 */
	private $logLevelAllow = [
		'info',
		// 感兴趣的事件，比如登录、退出
		'debug',
		// 详细的调试信息
		'notice',
		// 普通但值得注意的事件
		'warning',
		// 警告但不是错误，比如使用了被废弃的API
		'error',
		// 运行时错误，不需要立即处理但需要被记录和监控
		'alert',
		// 需要立即采取行动的问题，比如整站宕掉，数据库异常等，这种状况应该通过短信提醒
		'critical',
		// 严重问题，比如：应用组件无效，意料之外的异常
		'emergency'
		// 紧急状况，比如系统挂掉
	];

	/**
	 * 默认错误等级
	 */
	private $logLevel = 'info';
	private $redisLogKey;

	public function __construct()
	{
		return $this;
	}

	/**
	 * 初始化monolog和redis
	 *
	 * @author lxp 20170615
	 * @param string $logTitle 日志标题，用于文件名或key
	 * @param string $logLevel 错误等级
	 * @return $this
	 */
	public function init($logTitle, $logLevel = 'info')
	{
		$this->logObj = new Logger($logTitle);
		// 创建0777权限的目录，兼容root和www用户
		$path_dir = storage_path() . '/logs/' . date('Y-m', time()) . '/';
		m_mkdir($path_dir);
		$path_dir .= date('d', time()) . '/';
		m_mkdir($path_dir);
		// 拼接日志文件路径
		$path = $path_dir . $logTitle . '.log';
		// 初始化日志文件流，权限0777
		$this->logObj->pushHandler($handler = new StreamHandler($path, Logger::DEBUG, true, 0777));
		$handler->setFormatter(new LineFormatter(null, null, true, true));
		if ($logLevel && in_array($logLevel, $this->logLevelAllow)) {
			$this->logLevel = $logLevel;
		}

		if (env('REDIS_LOG', false)) {
			Redis::select(env('REDIS_DBNUM_DEFAULT'));
			$this->redisLogKey = 'log:' . $logTitle . ':__' . date('Ymd');
		}

		return $this;
	}

	/**
	 * 记录日志
	 *
	 * @author lxp 20151219
	 * @param string $message 字符串数据
	 * @param array $data 数组数据
	 */
	public function log($message, $data = [])
	{
		$this->logObj->{$this->logLevel}($message, $data);
		$this->logRedis($message, $data);
	}

	/**
	 * 日志结束
	 *
	 * @author lxp 20151219
	 */
	public function end()
	{
		$this->logObj->info(self::END_STR);
		$this->logRedis(self::END_STR);
	}

	/**
	 * 处理redis日志格式
	 *
	 * @param string $message
	 * @param array $data
	 */
	private function logRedis($message, $data = [])
	{
		if (env('REDIS_LOG', false)) {
			if (!empty($data)) {
				$message = $message . ' ' . json_encode($data);
			}

			list($usec, $sec) = explode(" ", microtime());

			$redisMessage = [
				date('Y-m-d H:i:s') . ' ' . floor($usec * 1000),
				$this->logLevel . ': ' . $message
			];
			Redis::rPush($this->redisLogKey, json_encode($redisMessage));
		}
	}
}
