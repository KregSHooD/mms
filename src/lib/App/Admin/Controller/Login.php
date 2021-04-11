<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 16/10/14
 * Time: 20:31
 */

namespace App\Admin\Controller;


use App\Admin\Common\Auth;
use CK\Core\Controller;

/**
 * 登录功能
 * Class Login
 * @res true
 *
 * @package App\Res\Controller
 */
class Login extends Controller{

    /**
     * 验证是否登录
     * @res true
     * @return bool
     */
    public function auth() {
        $flag = Auth::inst()->isLogin();
        if ($flag) {
            $user = Auth::inst()->getUser();
            return $this->filterUser($user);
        } else {
            return false;
        }
    }


    /**
     * 后台用户登录
     * @param $username
     * @param $password
     * @res true
     *
     * @return bool
     */
    public function adminLogin($username, $password) {
        $flag = Auth::inst()->login($username, $password);
        return $flag ? $this->filterUser($flag) : $flag;
    }

    /**
     * 登出系统
     * @res true
     * @return bool
     */
    public function logout() {
        return Auth::inst()->logout();
    }

    /**
     * 过滤不返回的用户字段
     * @param $user
     *
     * @return mixed
     */
    private function filterUser($user) {
        unset($user['acc_id']);
        unset($user['wx_id']);
        unset($user['acc_password']);
        unset($user['acc_email']);
        unset($user['grp_pur']);
        return $user;
    }
}