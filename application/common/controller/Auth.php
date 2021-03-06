<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16 0016
 * Time: 17:05
 */

namespace app\common\controller;

use app\common\exception\CommonException;
use app\common\service\AliyunRedis;
use app\common\service\Token;
use think\facade\Cache;

/**
 * 权限验证，通过中间件实现
 * Class Auth
 * @package app\common\controller
 */
class Auth
{
    public $unAuthAction=[
        'index/login/login'
    ];
    public $uid;
    public function __construct()
    {
        if($this->isAuthNecessary()){
            $this->tokenVerify();
        }else{
            return true;
        }

    }
    public function tokenVerify()
    {
        if(config('token.token_verify') === true ){
            //因token验证机制只会验证一次 所以此处可以使用new实例化
            $token  =   new Token();
            $token->tokenVerify(app('input')->token);
            $this->uid  =   $token->uid;
        }
    }
    public function isAuthNecessary():bool
    {
        $authName   =   app('input')->authName;
        if(in_array($authName,$this->unAuthAction)){
            return false;
        }else{
            return true;
        }
    }


}