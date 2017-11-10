<?php
namespace app\index\controller;
use app\index\model;
use think\Controller;
class TaskManage extends Common
{
    //获取任务列表
    public function getTaskList()
    {
        $result = Model('Task')->where(['status'=>'1'])->select();
        foreach($result as $k=>$v){
            $result[$k]['deptNo'] = Model('OrgDept')->where(['DEPT_NO' => $v['deptNo']])->value('DEPT_NAME');
            $result[$k]['typeId'] = Model('TaskType')->where(['id' => $v['typeId']])->value('typeName');
            $result[$k]['pId'] = Model('Process')->where(['id' => $v['pId']])->value('name');
            $result[$k]['timeLimit'] = substr($v['timeLimit'],0,4).'年'.substr($v['timeLimit'],4,6).'月';
            $result[$k]['releaseTime'] = date('Y-m-d H:i:s',$v['releaseTime']);
            $result[$k]['completeTime'] = date('Y-m-d H:i:s',$v['completeTime']);
        }
        if($result){
            $this->success($result);
        }
    }

    // 获取任务的分类
    public function getType()
    {
        $result = Model('TaskType')->select();
        if($result){
            $this->success($result);
        }else{
            $this->error('分类错误！');
        }
    }

    // 获取所有的流程
    public function getProcess()
    {
        $result = Model('Process')->select();
        if($result){
            $this->success($result);
        }else{
            $this->error('流程错误！');
        }
    }
	// 添加任务
	public function addTask($taskInfo)
	{
		$task = array();
		$timeLimit = explode('-',$taskInfo['timeLimit']);
		$task['timeLimit'] = $timeLimit['0'] . ($timeLimit['1'] + 1);
		$task['deptNo'] = $taskInfo['deptValue'];
		$task['typeId'] = $taskInfo['typeValue'];
		$task['serialNumber'] = 0;
		$task['content'] = $taskInfo['content'];
		$task['releaseTime'] = time();
		$task['level'] = $taskInfo['tasklevel'];
		$task['pId'] = $taskInfo['processValue'];
		$result = Model('Task')->insert($task);
		if($result){
			$this->success('添加任务成功');
		}else{
			$this->error('添加失败');
		}
	}
    //删除任务
    public function delTask($id)
    {
        if($id == '0'){
            $this->error('选择数据!');
        }
        if(is_numeric($id)){
            $result = Model('Task')->where(['id' => $id])->update(['status'=>'-1']);
            if($result){
                $this->success('删除成功!');
            }else{
                $this->error('删除失败!');
            }
        }else{
            foreach($id as $k=>$v){
                Model('Task')->where(['id' => $v['id']])->update(['status'=>'-1']);
            }
            $this->success('删除成功!');
        }
    }


}



