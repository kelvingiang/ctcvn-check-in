<form action="" method="post" id="checkInTime" name="checkInTime">
    <div>
        <div class="wait_row">
            <!-- <div style="width: 10%">
                <label>商會名稱</label>
            </div>
            <div style=" width: 90%">
                <input type="text" name="txtTitle"
                    id="txtTitle"
                    value="<?php echo get_option('Title_text'); ?>"
                    style=" width:550px; margin-right: 10px; height: 30px" />
            </div>
            <div style="height: 10px; width: 100%"></div> -->
            <div style="width: 10%">
                <label>刷卡時間</label>
            </div>
            <div style=" width: 90%">
                <input type="text" name="txtWait"
                    id="txtWait"
                    value="<?php echo get_option('Waiting_text'); ?>"
                    style=" width:550px; margin-right: 10px; height: 30px" />
            </div>
            <div style="height: 10px; width: 100%"></div>
            <div style="width: 10%">
                <label>QRCode 資料夾</label>
            </div>
            <div style=" width: 90%">
                <input type="text" name="txtPart"
                    id="txtPart"
                    value="<?php echo  get_post_meta(1, '_part_text', true); ?>"
                    style=" width:550px; margin-right: 10px; height: 30px" />
            </div>
        </div>
        <div style=" width: 65%; text-align: right">
            <input name="submit" id="submit"
                class="button button-primary"
                value="發 表" type="submit"
                style="margin-top: 30px">
        </div>
    </div>
</form>

<style>
    .wait_row {
        margin-left: 50px;
        margin-top: 20px
    }

    .wait_row div {
        float: left;
    }

    .wait_row label {
        font-weight: bold;
        font-size: 14px;
        line-height: 2.3;
    }
</style>