<?php
/** 公共模型类
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/11
 * Time: 10:23
 */
namespace app\model;

use think\Db;
use think\Model;
class CommonModel extends Model
{
    //获取列表(分页)
    public function getList($where = [], $page=1, $limit=10, $field='*', $orderBy = [], $groupBy = '')
    {
        $info = Db::name($this->table)
            ->where($where)
            ->field($field)
            ->page($page, $limit)
            ->order($orderBy)
            ->group($groupBy)
            ->select();
        return $info;
    }

    //获取列表所有
    public function getListAll($where = [], $field='*', $orderBy = [], $groupBy = '')
    {
        $info = Db::name($this->table)
            ->where($where)
            ->field($field)
            ->order($orderBy)
            ->group($groupBy)
            ->select();
        return $info;
    }

    //获取一行数据
    public function getRow($where, $field='*', $orderBy = [])
    {
        $info = Db::name($this->table)
            ->where($where)
            ->field($field)
            ->order($orderBy)
            ->find();
        return $info;
    }

    //获取某个字段的数据
    public function getValue($where, $field)
    {
        $info = Db::name($this->table)->where($where)->value($field);
        return $info;
    }

    //根据id获取记录
    public function getRowById($id, $field='*')
    {
        $info = Db::name($this->table)
            ->where('id',$id)
            ->field($field)
            ->find();
        return $info;
    }

    //获取总数
    public function getCount($where = [])
    {
        $info = Db::name($this->table)->where($where)->count();
        return $info;
    }

    //新增数据
    public function add($data)
    {
        $info = Db::name($this->table)->insertGetId($data);
        return $info;
    }

    //新增多条数据
    public function addAll($data)
    {
        $info = Db::name($this->table)->insertAll($data);
        return $info;
    }

    //更新数据
    public function updateByWhere($where, $data)
    {
        $info = Db::name($this->table)->where($where)->data($data)->update();
        return $info;
    }

    //根据id更新数据
    public function updateById($id, $data)
    {
        $res = Db::name($this->table)->where('id', $id)->data($data)->update();
        return $res;
    }
}