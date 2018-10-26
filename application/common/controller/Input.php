<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15 0015
 * Time: 16:36
 */

namespace app\common\controller;
use app\common\exception\BaseException;
use app\common\exception\CommonException;
use app\common\lib\exception\ApiException;
use think\Controller;
use think\Exception;
use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use traits\controller\Jump;

/**
 * 公共变量处理
 * Class Input
 * @package app\common\controller
 */

class Input extends Controller
{
    //*POST和GET变量
    public $input;
    //*token
    public $token;
    //*被请求的模型
    public $module;
    //*被请求的控制器
    public $controller;
    //*被请求的方法
    public $action;
    //*请求方式
    public $method;
    //*需要验证变量的方法
    //*后期改为module/controller/action方式
    protected $valiArray   =   [
        'reg',
        'sendRegCode'
        ,'recharge'
    ];
    public function __construct()
    {
        $this->input        =       \think\facade\Request::param();
        $this->module       =       \think\facade\Request::module();
        $this->controller   =       \think\facade\Request::controller();
        $this->action       =       \think\facade\Request::action();
        $this->method       =       \think\facade\Request::method();
        //*加载语言包 application/common/lang/ 目录下
        $this->setLang();
        //*获取token
        $this->getToken();
        //*变量验证
        $this->token    =   'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ4aWFvaGFyaWppLmNvbSIsInVpZCI6MSwiaWF0IjoxNTQwNTY0NzI3LCJleHAiOjE1NDA1OTM1Mjd9.9IZeFLnbne2F5nv1A8ktwU7E-QEjfTY4dCCvBRWRj8c';
        if($this->isValidateNecessary()){

            $this->inputValidate();
        }else{
            return true;
        }
        return true;
    }

    /**判断当前请求方法是否需要变量验证
     * @return bool
     */
    public function isValidateAction():bool
    {
        //todo:根据数据库权限表来判断是否需求验证数据
        $validateAction =   [
            'index/index/index'
        ];
        if(true === config('app.url_convert')){
            $action     =   $this->module.'/'.strtolower($this->controller).'/'.strtolower($this->action);
        }else{
            $action     =   $this->module.'/'.$this->controller.'/'.$this->action;
        }
        if(in_array($action,$validateAction)){
            return true;
        }else{
            return false;
        }
    }

    /**请求中是否包含输入变量
     * @return bool
     */
    public function isValidateNecessary():bool
    {
//        if(empty($this->input)){
//            return false;
//        }
        return true;
    }

    /**自动验证变量
     * @return array|bool
     * @throws CommonException
     */
    public function inputValidate()
    {
        $validateNmae   =   $this->controller.'Validate';

        $validate = app()->validate($validateNmae);
        if(!$validate){

            throw new CommonException('-2',lang(-2));
        }

        if($validate && !$validate->scene($this->action)->check($this->input)){
            return  $validate->getError();
        }else{
            return true;
        }
    }

    /**设置语言包，检测输入变量中是否包含lang
     * @return array
     */
    protected function setLang(){
       if(  false ===   array_key_exists('lang',$this->input)){
          return Lang::load( '../application/common/lang/zh-cn.php');
       }elseif (array_key_exists($this->input['lang'],['zh-cn','en-us'])){
           $langFile    =   '../application/common/lang/'.$this->input['lang'].'.php';
           return  Lang::load( $langFile);
       }else{
           return  Lang::load( '../application/common/lang/zh-cn.php');
       }
    }

    /**获取token值
     * @return bool
     * @throws CommonException
     */
    protected function getToken(){
        if(true === config('token.token_verify')){
                switch(true){
                    case config('token.token_position')=='param':
                        $this->token     =   $this->input[config('token.token_name')]    ??  false;
                        unset($this->input[config('token.token_name')]);
                        return true;
                        break;
                    case config('token.token_position')=='cookie':
                        $this->token    =   cookie(config('token.token_name'))  ??  false;
                        return true;
                        break;
                    case  config('token.token_position')=='header':
                        $this->token    =   \think\facade\Request::header(config('token.token_name'))   ??  false;
                        return true;
                        break;
                    default:
                        throw new CommonException(-1,lang(-1));
                }
        }else{
            $this->token    =   false;
            return true;
        }
    }





}