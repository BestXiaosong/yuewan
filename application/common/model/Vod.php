<?php
namespace app\common\model;




class Vod extends Base
{

    public function getList($where = []){
        return $this
            ->where($where)
            ->order('create_time desc')
            ->field('top,play_url,cid,pid,img,title,status,create_time,update_time')
            ->paginate(15,false,['query'=>request()->param()]);
    }

    public function getTop()
    {
        $where['a.status'] = 1;
        $where['b.status'] = 1;
        $rows = $this->where($where)->alias('a')
            ->join([
                [ 'play_category b','b.cid = a.cid','LEFT'],
            ])
            ->field('a.img,a.title,b.cate_name,a.pid')
            ->order(['a.top desc','a.update_time desc'])->limit(4)->select();
        if (empty($rows)) return false;
        $url = request()->domain();
        foreach ($rows as $k){
            $k['img'] = $url.str_replace('\\','/',$k['img']);
        }
        return $rows;
    }

    public function getRows($where)
    {

        $rows = $this->where($where)->alias('a')
            ->join([
                [ 'play_category b','b.cid = a.cid','LEFT'],
            ])
            ->field('a.img,a.title,b.cate_name,a.pid')
            ->order(['a.top desc','a.update_time desc'])->paginate()->each(function ($item){
                $item['img'] = request()->domain().str_replace('\\','/',$item['img']);
            });
        $items = $rows->items();
        if (empty($items)) return false;
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];
    }

    public function getDetail($where = [])
    {
        $row =  $this->alias('a')->join('play_category b','b.cid = a.cid','LEFT')->where($where)
            ->field('a.cid,a.pid,a.playUrl,a.img,a.title,a.detail,b.cate_name')->find();
        $url = request()->domain();
        if (!empty($row)){
            $row['img'] = $url.str_replace('\\','/',$row['img']);
            return $row;
        }
        return false;
    }


    public function getLists($where = [],$id){
        $join = [
            ['cl_play_category vc','v.cid = vc.cid','left'],
            ['cl_users u','v.user_id = u.user_id','left'],
            ['cl_role r','u.role_id = r.role_id','left'],
            ['cl_users uu',"uu.user_id = $id",'left'],
            ['cl_vod_up vu',array('v.pid = vu.up_id ','uu.role_id = vu.role_id'),'left'],

        ];
        $file = 'v.pid,v.title,v.play_url,v.img,v.num,v.up,v.reply_num,v.share_num,r.role_name,r.header_img,vu.status as up_status,vc.cate_name';
        $rows = $this->alias('v')->join($join)->order(array('v.up desc','v.create_time desc'))->where($where)->field($file)->cache(120)->limit(0,4)->select();

//        return $this->getLastSql();
//        $items = $rows->items();
        if (empty($rows)) return false;
        foreach ($rows as $k=>$v){
            if($v['num']/10000>=1){
                $rows[$k]['num'] = round($v['num']/10000,1).'万';
            }else{
                $rows[$k]['num'] = "$v[num]";
            }
            $rows[$k]['up'] = "$v[up]";
            $rows[$k]['reply_num']  = "$v[reply_num]";
            $rows[$k]['share_num']  = "$v[share_num]";
            if(empty($v['up_status'])){
                $rows[$k]['up_status'] = 0;
            }
        }
        return $rows;
    }


    public function getRowss($where = [],$id){
        $join = [
            ['cl_play_category vc','v.cid = vc.cid','left'],
            ['cl_users u','v.user_id = u.user_id','left'],
            ['cl_role r','u.role_id = r.role_id','left'],
            ['cl_users uu',"uu.user_id = $id",'left'],
            ['cl_vod_up vu',array('v.pid = vu.up_id ', 'uu.role_id = vu.role_id'),'left'],
        ];
        $where['vc.status'] = 1;
        $file = 'v.pid,v.title,v.play_url,v.img,v.num,v.up,v.reply_num,v.share_num,r.role_name,r.header_img,vu.status as up_status,vc.cate_name';
        $rows = $this->alias('v')->join($join)->order(array('v.up desc','v.create_time desc'))->where($where)->field($file)->cache(120)->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        foreach($items as $k=>$v){
            if($v['num']/10000>=1){
                $items[$k]['num'] = round($v['num']/10000,1).'万';
            }else{
                $items[$k]['num'] = "$v[num]";
            }
            $items[$k]['up'] = "$v[up]";
            $items[$k]['reply_num']  = "$v[reply_num]";
            $items[$k]['share_num']  = "$v[share_num]";
            if(empty($v['up_status'])){
                $items[$k]['up_status'] = 0;
            }
        }
        return ['thisPage'=>$rows->currentPage(),'total_page'=>$rows->lastPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];
    }


    public function getCate($where = []){
        $result = db('play_category')->where($where)->field('cid,cate_name,img')->select();
        return $result;
    }


    public function getVedio($where = [],$id){
        $join = [
            ['cl_play_category vc','v.cid = vc.cid','left'],
            ['cl_users u','v.user_id = u.user_id','left'],
            ['cl_role r','u.role_id = r.role_id','left'],
            ['cl_users uu',"uu.user_id = $id",'left'],
            ['cl_vod_up vu',array('v.pid = vu.up_id ', 'uu.role_id = vu.role_id'),'left'],
        ];
        $file = 'v.pid,v.title,v.play_url,v.img,v.num,v.up,v.reply_num,v.share_num,r.role_name,r.header_img,vu.status as up_status,vc.cate_name';
        $rows = $this->alias('v')->join($join)->order(array('v.up desc','v.create_time desc'))->where($where)->field($file)->cache(120)->paginate();
//        return $this->getLastSql();
        $items = $rows->items();
        if (empty($items)) return false;
        foreach($items as $k=>$v){
            if($v['num']/10000>=1){
                $items[$k]['num'] = round($v['num']/10000,1).'万';
            }else{
                $items[$k]['num'] = "$v[num]";
            }
            $items[$k]['up'] = "$v[up]";
            $items[$k]['reply_num']  = "$v[reply_num]";
            $items[$k]['share_num']  = "$v[share_num]";
            if(empty($v['up_status'])){
                $items[$k]['up_status'] = 0;
            }
        }
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];
    }

    public function getHistoryList($user_id,$type){
        if($type == 1) {
            $join = [
                ['cl_vod v','v.pid = ph.play_id','left'],
                ['cl_users u','v.user_id = u.user_id','left'],
                ['cl_role r','u.role_id = r.role_id','left']
            ];
            $file = 'v.img,v.title,ph.update_time,r.role_name,v.pid';
            $where = array('ph.user_id'=>$user_id,'ph.type'=>$type);
            $data = db('play_history')->alias('ph')->field($file)->join($join)->where($where)->select();
            foreach ($data as $k=>$v){

                $data[$k]['update_time'] = time_format(date('Y-m-d H:i:s',$v['update_time']));
            }
        }else{
            $join = [
                ['cl_room rm','rm.room_id = ph.play_id','left'],
                ['cl_users u','rm.user_id = u.user_id','left'],
                ['cl_role r','u.role_id = r.role_id','left']
            ];
            $file = 'rm.img,rm.room_name,r.role_name,rm.room_id,rm.cid';
            $where = array('ph.user_id'=>$user_id,'ph.type'=>$type);
            $data = db('play_history')->alias('ph')->field($file)->join($join)->where($where)->select();
            foreach ($data as $k=>$v){
                $data[$k]['hot'] = hotValue($v['room_id']);
                $data[$k]['room_id'] = hashid($v['room_id']);
            }
        }


        return $data;
    }

    public function getLists_all($where = [],$id){
        $join = [
            ['cl_play_category vc','v.cid = vc.cid','left'],
            ['cl_users u','v.user_id = u.user_id','left'],
            ['cl_role r','u.role_id = r.role_id','left'],
            ['cl_users uu',"uu.user_id = $id",'left'],
            ['cl_vod_up vu',array('v.pid = vu.up_id ','uu.role_id = vu.role_id'),'left'],

        ];
        $file = 'v.pid,v.title,v.play_url,v.img,v.num,v.up,v.reply_num,v.share_num,r.role_name,r.header_img,vu.status as up_status,vc.cate_name';
        $rows = $this->alias('v')->join($join)->order(array('v.up desc','v.create_time desc'))->where($where)->field($file)->cache(120)->paginate();
//        return $this->getLastSql();
//        return $this->getLastSql();
//        $items = $rows->items();
        $items = $rows->items();
        if (empty($items)) return false;
        foreach ($items as $k=>$v){
            if($v['num']/10000>=1){
                $items[$k]['num'] = round($v['num']/10000,1).'万';
            }else{
                $items[$k]['num'] = "$v[num]";
            }
            $items[$k]['up'] = "$v[up]";
            $items[$k]['reply_num']  = "$v[reply_num]";
            $items[$k]['share_num']  = "$v[share_num]";
            if(empty($v['up_status'])){
                $items[$k]['up_status'] = 0;
            }
        }

        return ['thisPage'=>$rows->currentPage(),'totalPage'=>$rows->lastPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];
    }
    public function getCateId($where){
        $result = db('play_category')->where($where)->field('cid')->select();
        foreach ($result as $k=>$v){
            $results[] = $v['cid'];
        }
        return ['data'=>$results];
    }

    /**
     * 获取使用空间过期的用户
     */
    public function expired_list($where){
//        $where['use_space'] = ['exp', 'gt bucket_space'];
        $rows = db('users')->where($where)->where('use_space','exp','> bucket_space')->paginate(15,false,['query'=>request()->param()]);
//        return db('users')->getLastSql();
        return $rows;
    }

    public function no_login_getRows($where = [],$id){
        $join = [
            ['cl_play_category vc','v.cid = vc.cid','left'],
            ['cl_users u','v.user_id = u.user_id','left'],
            ['cl_role r','u.role_id = r.role_id','left'],
        ];
        $where['vc.status'] = 1;
        $file = 'v.pid,v.title,v.play_url,v.img,v.num,v.up,v.reply_num,v.share_num,r.role_name,r.header_img,vc.cate_name';
        $rows = $this->alias('v')->join($join)->order(array('v.up desc','v.create_time desc'))->where($where)->field($file)->cache(120)->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        foreach($items as $k=>$v){
            if($v['num']/10000>=1){
                $items[$k]['num'] = round($v['num']/10000,1).'万';
            }else{
                $items[$k]['num'] = "$v[num]";
            }
            $items[$k]['up'] = "$v[up]";
            $items[$k]['reply_num']  = "$v[reply_num]";
            $items[$k]['share_num']  = "$v[share_num]";
            if(empty($v['up_status'])){
                $items[$k]['up_status'] = 0;
            }
        }
        return ['thisPage'=>$rows->currentPage(),'total_page'=>$rows->lastPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];
    }


    public function no_login_getLists($where = []){
        $join = [
            ['cl_play_category vc','v.cid = vc.cid','left'],
            ['cl_users u','v.user_id = u.user_id','left'],
            ['cl_role r','u.role_id = r.role_id','left'],

        ];
        $file = 'v.pid,v.title,v.play_url,v.img,v.num,v.up,v.reply_num,v.share_num,r.role_name,r.header_img,vc.cate_name';
        $rows = $this->alias('v')->join($join)->order(array('v.up desc','v.create_time desc'))->where($where)->field($file)->cache(120)->limit(0,4)->select();

//        return $this->getLastSql();
//        $items = $rows->items();
        if (empty($rows)) return false;
        foreach ($rows as $k=>$v){
            if($v['num']/10000>=1){
                $rows[$k]['num'] = round($v['num']/10000,1).'万';
            }else{
                $rows[$k]['num'] = "$v[num]";
            }
            $rows[$k]['up'] = "$v[up]";
            $rows[$k]['reply_num']  = "$v[reply_num]";
            $rows[$k]['share_num']  = "$v[share_num]";
            $rows[$k]['up_status'] = 0;
        }
        return $rows;
    }
}