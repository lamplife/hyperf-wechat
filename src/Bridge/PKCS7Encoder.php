<?php

declare(strict_types = 1);

/**
 * Author: 狂奔的螞蟻 <www.firstphp.com>
 * Date: 2020/10/29
 * Time: 10:04 PM
 */

namespace Firstphp\HyperfWechat\Bridge;


class PKCS7Encoder
{


    /**
     * 对需要加密的明文进行填充补位
     *
     * @param string $text 需要进行填充补位操作的明文
     * @return string
     */
    function encode($text)
    {
        $block_size = ErrorCode::BLOCK_SIZE;
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = $block_size - ($text_length % $block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = $block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }


    /**
     * 对解密后的明文进行补位删除
     *
     * @param string $text 解密后的明文
     * @return string 删除填充补位后的明文
     */
    function decode($text)
    {
        if ($text) {
            $pad = ord(substr($text, -1));
            if ($pad < 1 || $pad > 32) {
                $pad = 0;
            }
            return substr($text, 0, (strlen($text) - $pad));
        }
    }

}