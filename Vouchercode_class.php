<?php

    /**
     * CLASS BY REDz#0001
     * Truemoney wallet VoucherCode
     * Date 1/16/2021 11:21 AM Last Update
     */

    class VoucherCode {
        public function fetch($method = null , $url = null , $headers = array() , $data = null ) {
            $this->url = $url;
            $this->headers = $headers;
            $this->data = $data;
            $this->method = $method;
            $fetch = curl_init();
            $headers = array("Content-Type" => "application/json");
            curl_setopt_array($fetch , [
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

        public function buildHeaders ($array) {
            $headers = array();
            foreach ($array as $key => $value) { $headers[] = $key.": ".$value; }
            return $headers;
        }

        public function VoucherCode($Mobile = null, $voucher_code = null) {
            $this->Mobile = $Mobile;
            $this->VoucherCode = $voucher_code;
            
            if(isset($this->VoucherCode) === true && $this->VoucherCode === "") {
                $res = [
                    "status" => [
                        "message" => "No voucher delivery found.",
                        "reason" => "NO_VOUCHER_FOUND",
                    ]
                ];
                return $res;
            } else if (isset($this->Mobile) === true && $this->Mobile === "") {
                $res = [
                    "status" => [
                        "message" => "Can't find phone number",
                        "reason" => "NO_NUMBER_FOUND",
                    ]
                ];
                return $res;
            } else {
                $gift = str_replace("https://gift.truemoney.com/campaign/?v=","", $this->VoucherCode);
                if(strlen($gift) <= 0) {
                    $res = [
                        "status" => [
                            "message" => "Vouncher code cannot be empty",
                            "reason" => "NO_VOUNCHER_FOUND",
                        ]
                    ];
                    return $res;
                }
                $res = json_decode($this->fetch("POST" , "https://gift.truemoney.com/campaign/vouchers/{$gift}/redeem", null, json_encode(array("mobile" => $this->Mobile, "voucher_hash" => $this->VoucherCode))), true);
                if($res["status"]["code"] == "SUCCESS") {
                    $res = [
                        "status" => [
                            "message" => "SUCCESS",
                            "amount" => $res["data"]["voucher"]["redeemed_amount_baht"],
                        ]
                    ];
                    return $res;
                } else {
                    $res = [
                        "status" => [
                            "message" => "FAIL",
                            "reason" => $res["status"]["message"],
                        ]
                    ];
                    return $res;
                }
            }
        }
    }
