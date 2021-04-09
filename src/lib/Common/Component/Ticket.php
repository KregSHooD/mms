<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 16/11/2
 * Time: 20:38
 */

namespace Common\Component;


use CK\Core\Component;

/**
 * 微信卡券的操作库
 * Class Ticket
 *
 * @package Common\Component
 */
class Ticket extends Component{

    /**
     * 创建一个排期卡券
     * @param $data
     *  [
     *      'rep_name'=>'', //剧目名称
     *      'service_phone'=>'', //客服电话
     *      'event_time'=>'', //排期演出时间
     *      'rep_address'=>'', //排期演出地点
     *      'sub_title'=>'', //券名
     *  ]
     *
     * @return bool
     * @throws \Exception
     */
    public function createTicket($data) {
        $remark = '演出时间:'.date('Y-m-d H:i',$data['event_time'])."\n".'演出地点:'.$data['rep_address'];
        
        $desc = "使用时请向检票员出示此券\n请务必准时入场";

        $card = [
            'card'=>[
                'card_type'=>'MEETING_TICKET',
                'meeting_ticket'=>[
                    'base_info'=>[
                        'logo_url'=>'http://img.weiwubao.com/assets/ticket/logo420.jpg',
                        'brand_name'=>'有票儿',
                        'code_type'=>'CODE_TYPE_QRCODE',
                        'title'=>$data['rep_name'],
                        'sub_title'=>$data['sub_title'],
                        'color'=>'Color020',
                        'notice'=>'使用时向检票员出示此券',
                        'service_phone'=>$data['service_phone'] or '023-66666666',//读取剧目客服电话
                        'description'=>$desc,
                        'date_info'=>[
                            "type"=> 1,
                            "begin_timestamp"=> CK_NOW ,
                            "end_timestamp"=> $data['event_time']+3*3600 //演出时间加3小时
                        ],
                        'sku'=>[
                            'quantity'=>10000
                        ],
                        //                        'get_limit'=>'',
                        'use_custom_code'=>true,
                        'bind_openid'=>true,
                        'can_share'=>false,
                        'can_give_friend'=>true,
                        'custom_url_name'=>'有票儿',
                        'custom_url'=>APP_DOMAIN.'/wap',
                        'custom_url_sub_title'=>'有票儿'
                    ],
                    'meeting_detail'=>$remark
                ]
            ]
        ];

        return WxCard::inst()->createCard($card);
    }

    /**
     * 更新一张卡券的座位信息
     * @param $data
     *  [
     *      'code'=>'', //单票的CODE
     *      'card_id'=>'', //排期卡券的ID
     *      'zone'=>'', //剧场区域
     *      'entrance'=>'', //剧场入口
     *      'seat_number'=>'1排4号', //座位信息 几排几号
     *  ]
     * @return bool
     * @throws \Exception
     */
    public function updateTicket($data) {
        $update = [
            'code'=>$data['code'],
            'card_id'=>$data['card_id'],
            'zone'=>$data['zone'],
            'entrance'=>$data['entrance'],
            'seat_number'=>$data['seat_number']
        ];

        return WxCard::inst()->updateMeeting($update);
    }
}