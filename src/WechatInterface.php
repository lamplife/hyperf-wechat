<?php

declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: 狂奔的螞蟻 <www.firstphp.com>
 * Date: 2020/10/29
 * Time: 下午16:48
 */

namespace Firstphp\HyperfWechat;


interface WechatInterface
{


    /**
     * 获取access_token
     *
     * @return mixed
     */
    public function getAccessToken();


    /**
     * 获取模板列表
     *
     * @param string $accessToken
     * @return mixed
     */
    public function getTemplateList(string $accessToken);


    /**
     * 发送模板消息
     *
     * @param string $accessToken
     * @param array $data
     * @return mixed
     */
    public function sendTemplateMessage(string $accessToken, array $data);


    /**
     * 发送客服消息
     *
     * @param string $accessToken
     * @param array $message
     * @return mixed
     */
    public function customSend(string $accessToken, array $message);


    /**
     * 获取推送内容
     *
     * @param string|null $postData
     * @param array $getData
     * @return mixed
     */
    public function getReceiveXml(string $postData = "", array $getData = []);


    /**
     * 获取用户信息
     *
     * @param string $accessToken
     * @param string $openid
     * @return mixed
     */
    public function getUserInfo(string $accessToken, string $openid);


    /**
     * 创建二维码ticket
     *
     * @param string $accessToken
     * @param array $params
     * @return mixed
     */
    public function getQrcodeTicke(string $accessToken, array $params);


    /**
     * 通过ticket换取二维码
     *
     * @param string $ticket
     * @return mixed
     */
    public function showqrcode(string $ticket);


    /**
     * xml 转 array
     *
     * @param string $xml
     * @return mixed
     */
    public function fromXml(string $xml = "");

}