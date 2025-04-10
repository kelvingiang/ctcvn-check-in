<?php $page = getParams('page'); ?>
<div class="report_head" style="height: 60px">
    <ul style="margin:  15px 0">
        <li>
            <a class="button button-primary" href="<?php echo "admin.php?page=$page&action=check_in_start" ?>">開始報到時間</a>
        </li>
        <li>
            <a class="button button-primary" href="<?php echo "admin.php?page=$page&action=export_guests" ?>">導出理監事名單</a>
        </li>
    </ul>
    <hr />
    <ul>
        <li>
            <a class="button button-primary" href="<?php echo "admin.php?page=$page&action=create_qrcode" ?>">批次產生 QRCode</a>
        </li>
        <li>
            <a class="button button-primary" href="<?php echo "admin.php?page=$page&action=open_qrcode_folder" ?>">打開 QRCode 資料夾</a>
        </li>
    </ul>
    <hr />
    <ul>
        <li>
            <a class="button btn-delete" href="#" onclick="myFunction()">導入理監事 <i style="font-size:12px"> 舊的資料都被刪除</i></a>
        </li>
        <li>
            <a class="button button-primary" href="<?php echo "admin.php?page=$page&action=import_guests_info" ?>">導入補充理監事資料</a>
        </li>
    </ul>

</div>


<script type="text/javascript">
    function myFunction() {
        if (confirm("注意 ：導入時會把現有的理監事刪除 ！ ")) {
            location.href = "<?php echo "admin.php?page=$page&action=import_guests" ?>";
        } else {
            window.stop();
        }
    }
</script>