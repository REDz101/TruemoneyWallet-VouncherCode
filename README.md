# # TruemoneyWallet-VouncherCode
### **ฟรีห้ามขาย!**

**วิธีใช้**

	<?php
	require "Vouchercode_class.php";
	$voucher = new VoucherCode();
	print_r($voucher->VoucherCode("mobile","UrlCode"));

**ตัวอย่าง**

	<?php
	require "Vouchercode_class.php";
	$voucher = new VoucherCode();
	print_r($voucher->VoucherCode("064xxxxxxx","https://gift.truemoney.com/campaign/?v=wXpTYMgUYR87jGScha"));
