# # TruemoneyWallet-VouncherCode
# เติมเงินด้วยซองของขวัญ PHP

[Website](https://minefunny.net/) | [Thx_Responsecode](https://github.com/kumihoaomkung/true-money-gift)

ฟรีห้ามขาย

## ตัวอย่าง index.html
```html
<form action="link.php" method="post">
    <input type="text" name="link" id="link" placeholder="ใส่ลิงค์" require>
    <button type="submit">เติม</button>
</form>
```
## ตัวอย่าง request.php
```php
// ตัวอย่างรับค่าลิงค์
    if(isset($_POST['link'])){
        require "Vouchercode_class.php";
        $tm = new VoucherCode();
        //                                ใส่เบอร์โทร        รับค่าลิงค์
        $request = $tm->VoucherCode('06XXXXXXXX', $_POST['link']);
        print_r($request['message']);
    }
```
