<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 16/10/10
 * Time: 11:49
 */

namespace App\Admin\Common;

use App\Admin\Model\User;
use CK\Core\Component;
use CK\Util\Cookie;
use CK\Util\Session;

/**
 * 后台公共用户验证模块
 * Class Auth
 *
 * @package App\Admin\Common
 */
class Auth extends Component{
    private $_user;
    private $_is_login;
    private $_cookie_name='m';

    public function __construct() {
        $this->_user = Session::inst()->get('user');
        if (empty($this->_user)) {
            $this->check();
        }
    }

    /**
     * 检查是否有登录记录
     */
    public function check() {
        $usr_id = Cookie::inst()->get($this->_cookie_name);
        if (!empty($usr_id)) {
            $user = User::inst()->getUserById($usr_id);
            if ($user) {
                $this->setUserStatus($user);
            }
        }
    }

    /**
     * 检查是否已经登录
     * @return bool
     */
    public function isLogin() {
        if (!isset($this->_is_login)) {
            $this->_is_login = isset($this->_user) && !empty($this->_user) ? true : false;
        }
        return $this->_is_login;
    }

    /**
     * 登录系统
     * @param string $username 用户名
     * @param string $password 密码
     *
     * @return bool|array
     */
    public function login($username,$password) {
        $user = User::inst()->queryUser($username,$this->cipherPwd($password));
        if (empty($user)) {
            return false;
        } else {
            $this->setUserStatus($user);
            return $user;
        }
    }

    /**
     * 微信登录系统
     * @param $wx_id
     *
     * @return array|bool
     */
    public function wxLogin($wx_id) {
        $user = User::inst()->getUserByWx($wx_id);
        if (empty($user)) {
            return false;
        } else {
            $this->setUserStatus($user);
            return $user;
        }
    }

    /**
     * 刷新当前用户信息
     */
    public function refreshUser() {
        $this->check();
    }

    /**
     * 设置用户登录状态
     * @param $user
     */
    public function setUserStatus($user) {
        $user['grp_pur'] = explode(',',$user['grp_pur']);
        $this->_user = $user;
        Session::inst()->set('user',$user);
        Cookie::inst()->set($this->_cookie_name,$user['acc_id']);
    }

    /**
     * 登出系统
     */
    public function logout() {
        Session::inst()->clear();
        return Cookie::inst()->remove($this->_cookie_name);
    }

    /**
     * 得到当前登录用户信息
     * @param bool|false $json 是否以 JSON 编码返回
     *
     * @return null|array
     */
    public function getUser($json=false) {
        return $json?json_encode($this->_user):$this->_user;
    }

    /**
     * 将密码变为加密字符
     * @param $pwd
     *
     * @return string
     */
    public function cipherPwd($pwd) {
        return md5('_&^2'.sha1($pwd).'_%');
    }
}