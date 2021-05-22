<?php

/**
 * Class by REDzTrue/REDzSEA
 * Original code by REDzTrue/REDzSEA
 * Response code by Kumihoaomkung
 * Truemoney wallet VoucherCode
 * 5/7/2021 Last Update
 * https://github.com/REDzTrue/TruemoneyWallet-VouncherCode/
 */

class TMN {

    public function fetch($method = null, $url = null, $headers = array(), $data = null) {
        $this->url = $url;
        $this->headers = $headers;
        $this->data = $data;
        $this->method = $method;
        $fetch = curl_init();
        $headers = ["Content-Type" => "application/json"];
        curl_setopt_array($fetch, [
            CURLOPT_URL => $this->url,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_PROXY => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $this->buildHeaders($headers),
            CURLOPT_POSTFIELDS => $data
        ]);
        $this->response = curl_exec($fetch);
        curl_close($fetch);
        return $this->response;
    }

    public function buildHeaders($array)
    {
        $headers = array();
        foreach ($array as $key => $value) {
            $headers[] = $key . ": " . $value;
        }
        return $headers;
    }

    public function VoucherCode($Mobile = null, $voucher_code = null)
    {
        $this->Mobile = $Mobile;
        $this->VoucherCode = $voucher_code;

        if ($this->empty($this->VoucherCode) == true) {
            return $this->error(500, "ไม่มีซองนี้ในระบบ กรุณาตรวจสอบลิงก์นี้อีกครั้ง");
        } else if ($this->empty($this->Mobile) == true) {
            return $this->error(307, "ไม่พบเบอร์โทรศัพน์ของผู้รับโปรดเเจ้งผู้ควบคุมเพื่อเเก้ไขปัญหานี้");
        } else {

            $this->gift = str_replace("https://gift.truemoney.com/campaign/?v=", "", $this->VoucherCode);

            if (strlen($this->gift) <= 0) {
                return $this->error(306, "ลิงค์ของซองนี้ไม่ถูกต้อง กรุณาตรวจสอบลิงก์นี้อีกครั้ง");
            }

            // ส่งข้อมูลซอง
            $res = json_decode($this->fetch("POST"
                                            , "https://gift.truemoney.com/campaign/vouchers/{$this->gift}/redeem"
                                            , null
                                            , json_encode(array("mobile" => $this->Mobile
                                            , "voucher_hash" => $this->VoucherCode)))
                                            , true);
            
            $stats = $res["status"]["code"];
            
            switch ($stats) {
                case "SUCCESS":
                    $this->point = $res["data"]["voucher"]["redeemed_amount_baht"];
                    return $this->success(200, "คุณได้รับเงินจากซองนี้แล้ว", $this->point);
                    break;

                case "CANNOT_GET_OWN_VOUCHER":
                    return $this->error(301, "คุณไม่สามารถใส่ซองของขวัญของคุณเองได้");
                    break;
                
                case "TARGET_USER_NOT_FOUND":
                    return $this->error(302, "ไม่พบชื่อผู้ใช้เบอร์ Wallet นี้");
                    break;

                case "INTERNAL_ERROR":
                    return $this->error(500, "ไม่มีซองนี้ในระบบ กรุณาตรวจสอบลิงก์นี้อีกครั้ง");
                    break;

                case "VOUCHER_OUT_OF_STOCK":
                    return $this->error(420, "ซองนี้ได้มีคนรับไปแล้ว");
                    break;

                case "VOUCHER_NOT_FOUND":
                    return $this->error(700, "ไม่พบซองของขวัญ");
                    break;
                
                case "VOUCHER_EXPIRED":
                    return $this->error(705, "ซองของขวัญนี้หมดอายุแล้ว");
                    break;

                default:
                    return $res;
                    break;
            }

        }
    }

    public function GetPoint() {
        return $this->response([ "point" => $this->point ]);
    }

    public function GetMobile() {
        return $this->response([ "phone" => $this->Mobile ]);
    }

    public function GetGiftcode() {
        return $this->response([ "code" => $this->gift ]);
    }

    public function empty($data) {
        if(isset($data) === true && $data === "") {
            return true;
        }
    }

    public function success($code, $response, $amount) {
        return json_encode([
            "code"=>$code
            , "message"=>$response
            , "amount"=>$amount
            ]);
    }

    public function error($code, $response) {
        return json_encode([
            "code"=>$code
            ,"message"=>$response
            ]);
    }

}
