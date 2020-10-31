<?php

declare(strict_types = 1);

/**
 * Author: 狂奔的螞蟻 <www.firstphp.com>
 * Date: 2020/10/29
 * Time: 10:10 PM
 */

namespace Firstphp\HyperfWechat\Bridge;


class ErrorCode
{

    const BLOCK_SIZE = 32;

    const OK = 0;
    const ValidateSignatureError = -40001;
    const ParseXmlError = -40002;
    const ComputeSignatureError = -40003;
    const IllegalAesKey = -40004;
    const ValidateAppidError = -40005;
    const EncryptAESError = -40006;
    const DecryptAESError = -40007;
    const IllegalBuffer = -40008;
    const EncodeBase64Error = -40009;
    const DecodeBase64Error = -40010;
    const GenReturnXmlError = -40011;

}