<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function checkLogin(){
    $sM = session('member');
    $cM = cookie('member');
    if(cookie('logintype') == 'protal'){

        $token = cookie('token_'.\think\Config::get('ldap.appNo'));
        $t_time = cookie('t_time');
        $empno = cookie('empno');
        $personid = cookie('personid');
        if($token == '' || $empno == '' || $t_time == '' || $personid == ''){
            return false;
        }
    }
    if((isset($sM['EMP_NO']) && isset($cM['EMP_NO']) && $cM['EMP_NO'] === $sM['EMP_NO']) || (isset($sM['username']) && isset($cM['username']) && $sM['username'] == $cM['username'])){
        return true;
    }else{
        return false;
    }
}

function getUserInfo($empNo = ''){
    if($empNo == ''){
        $sM = session('member');
        $empNo = $sM['EMP_NO'];

    }
    $model = new \app\common\model\UserEmp();

    return $model->getUserInfo($empNo);
}
function getCompNo($empNo = ''){
    $userInfo = getUserInfo($empNo);
    return $userInfo['COMP_NO'];
}


/**
 * 功能：  组织或者成员向上查询所属组织和公司
 *
 * @param  $value 组织或者成员的id
 * @param  $type  组织默认为2  成员默认为1  公司为3
 * return  返回替换后的名字  和  成员名字的所处的根组织
 */
function getNameList($value,$type)
{
    if($type == '2')
    {
        $info = model('OrgDept')->where(array('DEPT_NO'=>$value))->find();
        $str = $info['DEPT_NAME'];
        if(!$info)
        {
            return false;
        }
        if(model('OrgDept')->where('DEPT_NO',$info['PARENT_DEPT_NO'])->find())
        {
            return getNameList($info['PARENT_DEPT_NO'],2).'/'.$str;
        }
        else
        {
            $compname =  model('OrgComp')->getCompName($info['COMP_NO']);
            return $compname.'/'.$str;
        }
    }
    else if($type == '1')
    {
        $username = model('UserEmp')->where(array('EMP_NO'=>$value))->find();
        $str = $username['EMP_NAME'];
        $info = model('OrgDept')->where(array('DEPT_NO'=>$username['DEPTNO']))->find();
        if(!$info)
            return $str;
        return getNameList($info['DEPT_NO'],2).'/'.$str;
    }
    else if($type == '3')
    {
        return model('OrgComp')->getCompName($value);
    }
    else if($type == '4')
        return '北京地铁集团';
}

/**
 * 根据部门类型和Id返回名称
 * @param string $type 类型
 * @param string $id   id
 * @param bool $isAll  是否返回部门树结构
 * @return string|Ambigous 名称
 */
function getAllName($type,$id,$isAll=false)
{
    switch ($type)
    {
        case 'clique':
            return '北京地铁';
        case 'dept':
            if($isAll)
                return getNameList($id, 2);
            return model('UserEmp')->getDeptName($id);
        case 'comp':
            return model('OrgComp')->getCompName($id);
        case 'tag':
            return model('label')->getLabelRealName($id);
        case 'person':
            if($isAll)
                return getNameList($id, 1);
            return model('UserEmp')->getUserRealName($id);
        default:
            return '未知';
    }
}