<?php

/**
 * Class by REDzTrue/REDzSEA
 * Original code by REDzTrue/REDzSEA
 * Response code by Kumihoaomkung
 * Truemoney wallet VoucherCode
 * New update GetPoint
 * 4/2/2021 Last Update
 * https://github.com/REDzTrue/TruemoneyWallet-VouncherCode/
 */

class VoucherCode
{
    public function fetch($method = null, $url = null, $headers = array(), $data = null)
    {
        $this->url = $url;
        $this->headers = $headers;
        $this->data = $data;
        $this->method = $method;
        // ส่งข้อมูล
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
        // Reponse
        $this->response = curl_exec($fetch);
        // จบการส่งข้อมูล
        curl_close($fetch);
        // ส่งข้อมูลกลับ
        return $this->response;
    }

    // Headers
    public function buildHeaders($array)
    {
        $headers = array();
        foreach ($array as $key => $value) {
            $headers[] = $key . ": " . $value;
        }
        return $headers;
    }

    // VoucherCode
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
            $res = json_decode($this->fetch("POST", "https://gift.truemoney.com/campaign/vouchers/{$this->gift}/redeem", null, json_encode(array("mobile" => $this->Mobile, "voucher_hash" => $this->VoucherCode))), true);
            if ($res["status"]["code"] == "SUCCESS") {
                $this->point = $res["data"]["voucher"]["redeemed_amount_baht"];
                return $this->success(200, "คุณได้รับเงินจากซองนี้แล้ว", $this->point);
            } elseif ($res['status']['code'] === "CANNOT_GET_OWN_VOUCHER") {
                return $this->error(301, "คุณไม่สามารถใส่ซองของขวัญของคุณเองได้");
            } elseif ($res['status']['code'] === "TARGET_USER_NOT_FOUND") {
                return $this->error(302, "ไม่พบชื่อผู้ใช้เบอร์ Wallet นี้");
            } elseif ($res['status']['code'] === "INTERNAL_ERROR") {
                return $this->error(500, "ไม่มีซองนี้ในระบบ กรุณาตรวจสอบลิงก์นี้อีกครั้ง");
            } elseif($res['status']['code'] === "VOUCHER_OUT_OF_STOCK"){
                return $this->error(420, "ซองนี้ได้มีคนรับไปแล้ว");
            } else {
                return $res;
            }
        }
    }

    public function GetPoint() {
        return $this->response(["point"=>$this->point]);
    }

    public function GetMobile() {
        return $this->response(["phone"=>$this->Mobile]);
    }

    public function GetGiftcode() {
        return $this->response(["code"=>$this->gift]);
    }

    public function empty($data) {
        if(isset($data) === true && $data === "") {
            return true;
        }
    }

    public function response($array) {
        return json_encode($array);
    }

    public function success($code, $response, $amount) {
        return json_encode(["code"=>$code, "message"=>$response, "amount"=>$amount]);
    }

    public function error($code, $response) {
        return json_encode(["code"=>$code, "message"=>$response]);
    }

}

?>
