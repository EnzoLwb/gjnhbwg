<?php

namespace App\Dao;


class ReserveDao
{
	//	reserve_status 审核中:0   预约失败:2   审核成功(预约成功):1    已取消:3     进行中:4    已完成:5
	const RESERVE_EXAMINING = 0;
	const RESERVE_SUCCESS = 1;
    const RESERVE_FAILED = 2;
    const RESERVE_CANCEL = 3;
    const RESERVE_ING = 4;
    const RESERVE_FINISH = 5;
    public static $reserve_status = array(
        self::RESERVE_EXAMINING=>'预约审核中',
        self::RESERVE_SUCCESS=>'预约成功',
        self::RESERVE_FAILED=>'预约失败',
        self::RESERVE_CANCEL=>'已取消',
        self::RESERVE_ING=>'进行中',
        self::RESERVE_FINISH=>'已完成',
    );
	//  button_text  取消预约   预约失败   已取消   讲解中   等待讲解  讲解评价 查看评价
	const BUTTON_CANCEL = 0;
	const BUTTON_FAILED = 1;
	const BUTTON_CANCELED = 2;
	const BUTTON_AUDITING = 3;
	const BUTTON_WAITING = 4;
	const BUTTON_EVA = 5;
	const BUTTON_LOOK = 6;
	public static $button_text = array(
		self::BUTTON_CANCEL=>'取消预约',
		self::BUTTON_FAILED=>'预约失败',
		self::BUTTON_CANCELED=>'已取消',
		self::BUTTON_AUDITING=>'讲解中',
		self::BUTTON_WAITING=>'等待讲解',
		self::BUTTON_EVA=>'讲解评价',
		self::BUTTON_LOOK=>'查看评价',
	);
}
