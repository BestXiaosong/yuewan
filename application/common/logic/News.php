<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31 0031
 * Time: 14:24
 */

namespace app\common\logic;


use think\Model;

class News extends Model
{
    public function saveChange($data)
    {
        if (is_numeric($data['id'])) {
            return $this->validate('news.edit')->allowField(true)
                ->isUpdate(true)->save($data, [$this->getPk() => $data['id']]);
        } else {
            return $this->validate('news.add')->allowField(true)->save($data);
        }
    }
    //更新评论数
    public function changeLihao($news_id)
    {
        return $this->where('news_id',$news_id)->setInc('lihao',1);
//        return $this->save(['lihao'=>'lihao + 1'],['news_id'=>$news_id]);

    }


    public function changeLikong($news_id)
    {
        return $this->where('news_id',$news_id)->setInc('likong',1);
//        return $this->save(['likong'=>'likong + 1'],['news_id'=>$news_id]);

    }

}