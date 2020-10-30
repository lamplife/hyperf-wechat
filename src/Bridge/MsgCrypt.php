<?php

declare(strict_types = 1);

/**
 * Author: 狂奔的螞蟻 <www.firstphp.com>
 * Date: 2020/10/29
 * Time: 10:01 PM
 */

namespace Firstphp\HyperfWechat\Bridge;


class MsgCrypt
{

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $encodingAesKey;

    /**
     * @var string
     */
    protected $appId;

    /**
     * @var string
     */
    protected $k;

    /**
     * @var string
     */
    protected $iv;


    /**
     * 构造函数
     *
     * @param $token 公众号消息校验Token
     * @param $encodingAesKey  公众号消息加解密Key
     * @param $appId  公众号的appId
     */
    public function __construct(string $token, string $encodingAesKey, string $appId)
    {
        $this->token = $token;
        $this->encodingAesKey = $encodingAesKey;
        $this->appId = $appId;
        $this->key = base64_decode($encodingAesKey . "=");
        $this->iv = substr($this->key, 0, 16);
    }


    /**
     * 消息加密
     *
     * @param $replyMsg
     * @param $timeStamp
     * @param $nonce
     */
    public function encryptMsg($replyMsg, $timestamp = null, $nonce = null)
    {
        //加密
        $array = $this->encrypt($replyMsg, $this->appId);
        if ($array[0] != 0) {
            return $array;
        }
        $timestamp = $timestamp ?: time();
        $encrypt = $array[1];

        //生成安全签名
        $array = $this->_getSHA1($this->token, $timestamp, $nonce);
        if ($array[0] != 0) {
            return $array;
        }
        $signature = $array[1];

        //生成发送的xml
        $encryptMsg = $this->_generateXml($encrypt, $signature, $timestamp, $nonce);

        return [ErrorCode::OK, $encryptMsg];
    }


    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @return string 加密后的密文
     */
    public function encrypt($text, $appid)
    {
        try {
            //获得16位随机字符串，填充到明文之前
            $random = $this->getRandomStr();
            $text = $random . pack("N", strlen($text)) . $text . $appid;

            $iv = substr($this->key, 0, 16);
            //使用自定义的填充方式对明文进行补位填充
            $pkc_encoder = new PKCS7Encoder();
            $text = $pkc_encoder->encode($text);
            $encrypted = openssl_encrypt($text, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $iv);
            return array(ErrorCode::OK, base64_encode($encrypted));
        } catch (\Exception $e) {
            return array(ErrorCode::EncryptAESError, null);
        }
    }


    /**
     * 消息解密
     *
     * @param string $signature
     * @param string $timestamp
     * @param string $nonce
     * @param string $postData
     * @return array|bool|string
     */
    public function decryptMsg($signature = '', $timestamp = '', $nonce = '', $postData = '')
    {
        if (strlen($this->encodingAesKey) != 43) {
            return [ErrorCode::IllegalAesKey, null];
        }

        if (!$postData) {
            return [ErrorCode::ParseXmlError, null];
        }

        //提取密文
        $array = $this->_extractXml($postData);
        if ($array[0] != 0) {
            return $array;
        }

        $timestamp = $timestamp ?: time();

        $encrypt = $array[1];

        //验证安全签名
        $array = $this->_getSHA1($this->token, (string)$timestamp, (string)$nonce);
        if ($array[0] != 0) {
            return $array;
        }

        $checkSignature = $array[1];
        if ($signature != $checkSignature) {
            return [ErrorCode::ValidateSignatureError, null];
        }

        $result = $this->decrypt($encrypt, $this->appId);

        return $result;
    }


    /**
     * 对密文进行解密
     * @param string $encrypted 需要解密的密文
     * @return string 解密得到的明文
     */
    public function decrypt($encrypted, $appid)
    {
        try {
            //解密
            if (function_exists('openssl_decrypt')) {
                $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $this->key, OPENSSL_ZERO_PADDING, $this->iv);
            } else {
                $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($encrypted), MCRYPT_MODE_CBC, $this->iv);
            }
        } catch (\Exception $e) {
            return array(ErrorCode::DecryptAESError, null);
        }

        try {
            //去除补位字符
            $pkc_encoder = new PKCS7Encoder;
            $result = $pkc_encoder->decode($decrypted);

            // 容错
            if (empty($result)) {
                return "";
            }

            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16) {
                return "";
            }

            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid = substr($content, $xml_len + 4);
        } catch (\Exception $e) {
            return array(ErrorCode::IllegalBuffer, null);
        }
        if ($from_appid != $appid) {
            return array(ErrorCode::ValidateAppidError, null);
        }

        return array(0, $xml_content);
    }


    /**
     * 加密明文补位填充
     *
     * @param $text
     * @return string
     */
    private function _encode($text)
    {
        //计算需要填充的位数
        $amount_to_pad = ErrorCode::BLOCK_SIZE - (strlen($text) % ErrorCode::BLOCK_SIZE);
        $amount_to_pad = $amount_to_pad ?: ErrorCode::BLOCK_SIZE;

        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = '';
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }


    /**
     * 解密明文补位删除
     *
     * @param $text
     * @return string
     */
    private function _decode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }


    /**
     * 计算签名
     *
     * @param string $token
     * @param string $timestamp
     * @param string $nonce
     * @return array|bool
     */
    private function _getSHA1(string $token, string $timestamp, string $nonce)
    {
        try {
            $tmpArr = [$token, $timestamp, $nonce];
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            return [ErrorCode::OK, $tmpStr];
        } catch (\Exception $e) {
            return [ErrorCode::ComputeSignatureError, null];
        }
    }


    private function _getRandomStr()
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz'), 0, 16);
    }


    /**
     * 提取出xml数据包中的加密消息
     * @param string $xmltext 待提取的xml字符串
     * @return string 提取出的加密消息字符串
     */
    public function _extractXml($xmltext)
    {
        try {
            $xml = new \DOMDocument();
            $xml->loadXML($xmltext);
            $array_e = $xml->getElementsByTagName('Encrypt');
            $encrypt = $array_e->item(0)->nodeValue;
            return [ErrorCode::OK, $encrypt];
        } catch (\Exception $e) {
            return [ErrorCode::PARSE_XML_ERROR, null];
        }
    }


    /**
     * 生成xml消息
     * @param string $encrypt 加密后的消息密文
     * @param string $signature 安全签名
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     */
    private function _generateXml($encrypt, $signature, $timestamp, $nonce)
    {
        $format = "<xml>
        <Encrypt><![CDATA[%s]]></Encrypt>
        <MsgSignature><![CDATA[%s]]></MsgSignature>
        <TimeStamp>%s</TimeStamp>
        <Nonce><![CDATA[%s]]></Nonce>
        </xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }


    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    function getRandomStr()
    {
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }


}