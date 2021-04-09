<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 15/9/12
 * Time: 10:57
 */

namespace Common\Component;


use Common\Model\Mpinfo;
use CK\Api\Weixin;
use CK\Core\Component;
use Common\Component\RemoteWUB;

/**
 * Class Platform
 * 微信平台类
 * @package App\Common\Component
 */
class Platform extends Component{

    /**
     * 得到当前可用的微信公众号 ACCESS_TOKEN
     */
    public function getAccessToken() {
        $info = Mpinfo::inst()->get();
        if (!empty($info['access_token']) && CK_NOW < intval($info['token_exp'])) {
            return $info['access_token'];
        } else {
//            $token = Weixin::inst()->getAccessToken();
            $token = RemoteWUB::inst()->getAccessToken();
            $update = [
                'access_token'=>$token['access_token'],
                'token_exp'=>CK_NOW + $token['expires_in']
            ];
            Mpinfo::inst()->update($update);
            return $token['access_token'];
        }
    }

    /**
     * 得到当前可用的 JsapiTicket
     * @return mixed
     * @throws \CK\Ex\ExWeixin
     * @throws \Exception
     */
    public function getJsapiTicket() {
        $info = Mpinfo::inst()->get();
        if (!empty($info['jsapi_ticket']) && CK_NOW < intval($info['ticket_exp'])) {
            return $info['jsapi_ticket'];
        } else {
            $token = $this->getAccessToken();
            $result = Weixin::inst()->getJsapiTicket($token);
            $update = [
                'jsapi_ticket'=>$result['ticket'],
                'ticket_exp'=>CK_NOW + $result['expires_in']
            ];
            Mpinfo::inst()->update($update);
            return $result['ticket'];
        }
    }

    /**
     * 得到当前可用的 CardTicket
     * @return mixed
     * @throws \CK\Ex\ExWeixin
     * @throws \Exception
     */
    public function getCardTicket() {
        $info = Mpinfo::inst()->get();
        if (!empty($info['card_ticket']) && CK_NOW < intval($info['card_exp'])) {
            return $info['card_ticket'];
        } else {
            $token = $this->getAccessToken();
            $result = Weixin::inst()->getCardTicket($token);
            $update = [
                'card_ticket'=>$result['ticket'],
                'card_exp'=>CK_NOW + $result['expires_in']
            ];
            Mpinfo::inst()->update($update);
            return $result['ticket'];
        }
    }
    
    

    /**
     * 得到 JSAPI 签名
     *
     * @param $data
     *
     * 格式 [
     *      //jsapi_ticket
     *      'jsapi_ticket'=>'',
     *      //随机字符串
     *      'noncestr'=>'',
     *      //时间截
     *      'timestamp'=>'',
     *      //当前调用的URL
     *      'url'=>'',
     * ]
     *
     * @return string
     */
    public function jsapiSign($data) {
        ksort($data);
        $str = '';
        foreach ($data as $key => $value) {
            $str .= $key.'='.$value.'&';
        }
        $str = rtrim($str,'&');
        return sha1($str);
    }

    /**
     * 取得card API 签名
     * @param $data
     * 格式 [
     *      'code' => '',
     *      'timestamp' => '',
     *      'card_id' => '',
     *      'api_ticket' => '',
     *      'nonce_str' => '',
     *      'openid'=>''
     * ]
     * @return string
     */
    public function cardSign($data) {
        $values = array_values($data);
        sort($values,SORT_STRING);
        return sha1(join('',$values));
    }
}