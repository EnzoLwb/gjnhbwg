<?php

namespace App\Dao;

use App\Models\NavigationRoad;
use App\Models\NavigationPoint;
use Illuminate\Support\Facades\DB;
/**
 * 公安数据同步业务模型
 *
 * @author lxp 20170925
 */
class NavigationDao extends NavigationPoint
{

    /**
     * 路径导航计算
     *
     * @author yyj 20171118
     * @param int $start_x 起点x坐标
	 * @param int $start_y 起点y坐标
	 * @param int $end_x 终点x坐标
	 * @param int $end_y 终点y坐标
	 * @param int $map_id 地图编号
	 * @return array
     */
    public static function get_road($start_x,$start_y,$end_x,$end_y,$map_id){
        
		//获得距离起点最近的点
        $start_info = NavigationPoint::where('map_id', $map_id)->orderBy(DB::raw("((x-$start_x)*(x-$start_x)+(y-$start_y)*(y-$start_y))"), "asc")->select('x', 'y', 'id')->first();

        //获得距离终点最近的点
        $end_info = NavigationPoint::where('map_id', $map_id)->orderBy(DB::raw("((x-$end_x)*(x-$end_x)+(y-$end_y)*(y-$end_y))"), "asc")->select('x', 'y', 'id')->first();

        if (!$start_info || !$end_info) {
            return [];
        }
        //起点终点相距较近直接相连
        if($start_info->id==$end_info->id){
            $road[0]['x']=$start_x;
            $road[0]['y']=$start_y;
            $road[1]['x']=$end_x;
            $road[1]['y']=$end_y;
            return $road;
        }
        else{
            //迪杰斯特拉计算最短路径
            $road=NavigationDao::djstl($start_info->id,$end_info->id,$map_id,0,0);
            $road_num=count($road);
            if($road_num>2){
                //路径优化
                //前两个导航点坐标
                $x_start1=$road[0]['x'];
                $y_start1=$road[0]['y'];
                $x_start2=$road[1]['x'];
                $y_start2=$road[1]['y'];
                //最后连个导航点坐标
                $x_end1=end($road)['x'];
                $y_end1=end($road)['y'];
                $x_end2=array_slice($road,-2,1)[0]['x'];
                $y_end2=array_slice($road,-2,1)[0]['y'];
                //起点不是第一个导航点，优化起点
                if($start_x!=$x_start1&&$start_y!=$y_start1){
                    $star_arr[0]['x']=$start_x;
                    $star_arr[0]['y']=$start_y;
                    if($y_start1==$y_start2){
                        $star_arr[1]['x']=$start_x;
                        $star_arr[1]['y']=$y_start1;
                        if($x_start1<$x_start2){
                            if($start_x>$x_start1){
                                unset($road[0]);
                            }

                        }
                        elseif($x_start1>$x_start2){
                            if($start_x<$x_start1){
                                unset($road[0]);
                            }
                        }
                    }
                    elseif($x_start1==$x_start2){
                        $star_arr[1]['x']=$x_start1;
                        $star_arr[1]['y']=$start_y;
                        if($y_start1<$y_start2){
                            if($start_y>$y_start1){
                                unset($road[0]);
                            }
                        }
                        elseif($y_start1>$y_start2){
                            if($start_y<$y_start1){
                                unset($road[0]);
                            }
                        }
                    }
                    //将优化后的起点数组加在road数组之前
                    $road=array_merge($star_arr,$road);
                }
                //判断终点
                //终点不是最后一个导航点，优化终点
                if($end_x!=$x_end1&&$end_y!=$y_end1){
                    $arr_num=count($road);
                    if($y_end1==$y_end2){
                        $end_arr[0]['x']=$end_x;
                        $end_arr[0]['y']=$y_end1;
                        if($x_end1<$x_end2){
                            if($end_x>$x_end1){
                                unset($road[$arr_num-1]);
                            }

                        }
                        elseif($x_end1>$x_end2){
                            if($end_x<$x_end1){
                                unset($road[$arr_num-1]);
                            }
                        }
                    }
                    elseif($x_end1==$x_end2){
                        $end_arr[0]['x']=$x_end1;
                        $end_arr[0]['y']=$end_y;
                        if($y_end1<$y_end2){
                            if($end_y>$y_end1){
                                unset($road[$arr_num-1]);
                            }
                        }
                        elseif($y_end1>$y_end2){
                            if($end_y<$y_end1){
                                unset($road[$arr_num-1]);
                            }
                        }
                    }
                    $end_arr[1]['x']=$end_x;
                    $end_arr[1]['y']=$end_y;
                    //将优化后的起点数组加在road数组之前
                    $road=array_merge($road,$end_arr);
                }

            }
            return $road;
        }

    }

    /**
     * 迪杰斯特拉 单源最短路劲计算
     * @param int $start_id 开始点位
     * @param int $end_id 结束点位
     * @param int $map_id 地图id
     * @param float $start_x 起点x坐标
     * @param float $start_y 起点y坐标
     * @return array
     * @author yyj 20160830
     */
    public static function djstl($start_id,$end_id,$map_id,$start_x,$start_y){
        $start_node_id=$start_id;//设置开始节点
        //获取所有节点信息
        $info=NavigationPoint::where('map_id',$map_id)->pluck('axis', 'id')->toArray();
        $i=0;
        foreach($info as $k=>$g){
            $node_arr[$i]['node_id']=$k;
            $node_arr[$i]['next_node']=array();
            $node_arr[$i]['distance']=array();
            //获取当前点位的关联点位
            $road_info=NavigationRoad::where('start_id',$k)->orwhere('end_id',$k)->select('start_id','end_id','distance')->get()->toArray();
            foreach($road_info as $v){
                $next_node=($k==$v['start_id'])?$v['end_id']:$v['start_id'];
                $distance=$v['distance'];
                array_push($node_arr[$i]['next_node'],$next_node);
                array_push($node_arr[$i]['distance'],$distance);
            }
            $i+=1;
        }

        //迪杰斯特拉 单源最短路劲计算
        foreach ($node_arr as $node_info) {
            foreach ($node_info['next_node'] as $key => $next_node) {
                $i_cost[$node_info['node_id']][$next_node]['distance'] = $node_info['distance'][$key];
                $i_cost[$node_info['node_id']][$next_node]['note'] = $node_info['next_node'][$key];
            }
            $i_dist[$node_info['node_id']]['distance'] = 'INF'; // 初始化为无穷大
            $i_dist[$node_info['node_id']]['road'] =$start_node_id; // 初始化为起点
            $b_mark[$node_info['node_id']] = false; // 初始化未加入
        }

        $i_dist[$start_node_id]['distance']= 0; // 初始点到其本身的距离为0
        $b_mark[$start_node_id] = true; // 初始点加入集合
        $current_node_id = $start_node_id; // 最近加入的节点id
        $node_count = count($node_arr);//需要循环的次数

        for ($i = 0; $i < $node_count; $i ++) {
            $min = 'INF';
            // 当前节点的最近距离
            if (is_array($i_cost[$current_node_id])) {
                foreach ($i_cost[$current_node_id] as $key => $val) {
                    if ($i_dist[$key]['distance'] == 'INF' || $i_dist[$key]['distance'] > $i_dist[$current_node_id]['distance'] + $val['distance']) {
                        $i_dist[$key]['distance']= $i_dist[$current_node_id]['distance']+ $val['distance'];
                        $i_dist[$key]['road']=$i_dist[$current_node_id]['road'].'#'.$key;
                    }
                }
            }
            foreach ($i_dist as $key => $val) {
                if (! $b_mark[$key]) {
                    if ($val['distance'] != 'INF' && ($min == 'INF' || $min > $val['distance'])) {
                        $min = $val['distance'];
                        $candidate_node_id = $key; // 候选最近结点id
                    }
                }
            }
            if ($min == 'INF') {
                break;
            }
            $current_node_id = $candidate_node_id;
            $b_mark[$current_node_id] = true;
        }
        //获取最短路径
        $arr=$i_dist[$end_id]['road'];
        $arr=explode('#',$arr);
        $info_x=NavigationPoint::where('map_id',$map_id)->pluck('x', 'id')->toArray();
        $info_y=NavigationPoint::where('map_id',$map_id)->pluck('y', 'id')->toArray();
        if(!empty($start_x)&&!empty($start_y)){
            $i=0;
            $road[$i]['x']=$start_x;
            $road[$i]['y']=$start_y;
        }
        else{
            $i=-1;
        }
        foreach ($arr as $k=>$g) {
            $road[$i+1]['x']=$info_x[$g];
            $road[$i+1]['y']=$info_y[$g];
            $i=$i+1;
        }
        $road[$i]['x']=$info_x[$end_id];
        $road[$i]['y']=$info_y[$end_id];
        return $road;
    }
}
