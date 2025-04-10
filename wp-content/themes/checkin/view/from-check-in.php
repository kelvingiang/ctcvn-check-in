<style type="text/css">
    #show-img {
        width: 600px;
        height: 400px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
    }
</style>
<?php
require_once(DIR_MODEL . 'model-check-in-function.php');
$model = new Model_Check_In_Function();
$id = isset($_GET['id']) ? $_GET['id'] : null;
if ($id !== null) {
    $data = $model->get_item(getParams());
}
?>

<?php
$insert_id = $model->saveItem();
if (!empty($insert_id)) {
?>
    <div style=" background-color: #FFADAD; color: white; min-height: 150px; margin-left: -20px; margin-bottom: 50px; padding-left: 20px">
        <?php
        foreach ($insert_id as $val) {
            echo $val;
        }
        ?>
    </div>
<?php } ?>
<form action="" method="post" enctype="multipart/form-data" id="f-guests" name="f-guests">

    <input type='hidden' id='hidden_ID' name='hidden_ID' value='<?php echo $data['ID'] ?? null; ?>' />
    <input type='hidden' id='hidden_img' name='hidden_img' value='<?php echo $data['img'] ?? null; ?>' />
    <input type='hidden' id='hidden_country' name='hidden_country' value='<?php echo $data['country'] ?? null; ?>' />
    <input type='hidden' id='hidden_fullname' name='hidden_fullname' value='<?php echo $data['full_name'] ?? null; ?>' />
    <input type='hidden' id='hidden_appcode' name='hidden_appcode' value='<?php echo $data['app_code'] ?? null; ?>' />
    <input type='hidden' id='hidden_barcode' name='hidden_barcode' value='<?php echo $data['barcode'] ?? null; ?>' />

    <div class="row-one-column">
        <div class="cell-title">照片</div>
        <div class="cell-text">
            <input type="file" id="guests_img" name="guests_img" accept=".png, .jpg, .jpeg, .bmp" />
        </div>
    </div>

    <div class="row-two-column" style="height: 420px;">
        <div class="col">
            <?php
            if (empty($data['img'])) {
                $guest_img = 'no-image.jpg';
            } else {
                $guest_img = $data['img'];
            }
            ?>
            <div id="show-img" style="background-image: url('<?php echo PART_IMAGES . 'guests/' . $guest_img; ?>');">
            </div>
        </div>

        <?php if (getParams('action') != 'add') { ?>
            <div class="col">

                <div class="cell-title ">二維碼</div>
                <div class="cell-text">
                    <?php $barcodeImgName = $data['full_name'] . '-' . $data['barcode']; ?>

                    <img id="img_barcode" name="img_barcode" src='<?php echo PART_IMAGES . 'qrcode' . DS .  $data['barcode'] . '.png'; ?>' style="width: 70px">
                    <a href="<?php echo PART_IMAGES . 'qrcode' . DS . $data['barcode'] . '.png' ?>" download="<?php echo $barcodeImgName . '.png' ?>" style="font-weight:  bold; text-decoration: none; color: blue">
                        下載二維碼檔案
                    </a>

                </div>

            </div>
        <?php } ?>
    </div>

    <div class="row-one-column">
        <div class="cell-title">App Code</div>
        <div class="cell-text">
            <input type="text" name="txt_appcode" class="my-input" <?php echo get_current_user_id() == 1 ? '' : 'readonly' ?> value="<?php echo $data['app_code'] ?? null ?>" />
        </div>
    </div>

    <div class="row-one-column">
        <div class="cell-title">姓名</div>
        <div class="cell-text">
            <input type="text" name="txt_fullname" class="my-input" required value="<?php echo $data['full_name'] ?? null ?>" />
        </div>
    </div>


    <div class="row-two-column">
        <div class="col">
            <div class="cell-title">分會</div>
            <div class="cell-text">
                <select id="sel_Country" name="sel_country" class="my-input">
                    <?php
                    require_once DIR_CODES . 'my-list.php';
                    $myList = new Codes_My_List();
                    foreach ($myList->countryList() as $key => $val) {
                        if (!empty($data['country'])) {
                            $ss = $data['country'] == $key ? 'selected' : '';
                        } else {
                            $ss = '';
                        }
                    ?>
                        <option value='<?php echo $key ?>' <?php echo $ss   ?>>
                            <?php echo $val  ?> </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col">
            <div class="cell-title ">職稱</div>
            <div class="cell-text"><input type="text" id="txt_position" name="txt_position" class="my-input" value='<?php echo $data['position'] ?? null ?>' /></div>
        </div>
    </div>

    <div class="row-two-column">
        <div class="col">
            <div class="cell-title ">
                E-mail
                <label style=' font-weight: bold; color: red;padding-left: 10px' id='error-email'></label>
            </div>
            <div class="cell-text">
                <input type="text" id="txt_email" name="txt_email" class="my-input email" value='<?php echo $data['email'] ?? null ?>' />
                
            </div>
        </div>

        <div class="col">
            <div class="cell-title ">電話</div>
            <div class="cell-text"><input type="text" id="txt_phone" name="txt_phone" class="my-input" class='type-phone-more' value='<?php echo $data['phone'] ?? null;  ?>' /></div>
        </div>

    </div>
    <div class="row-one-column">
        <div class="cell-title">備註</div>
        <div class="cell-text">
            <textarea id="txt_note" name="txt_note" rows="5" cols="150"><?php echo $data['note'] ?? null ?></textarea>
        </div>
    </div>

    <div class="btn-add-space">
        <input name="submit" id="submit" class="button button-primary" value="發 表" type="submit">
    </div>
</form>

<script type="text/javascript">
    // show hinh anh truoc khi up len
    jQuery(function() {
        jQuery("#guests_img").on("change", function() {
            var files = !!this.files ? this.files : [];
            if (!files.length || !window.FileReader)
                return; // no file selected, or no FileReader support

            if (/^image/.test(files[0].type)) { // only image file
                var reader = new FileReader(); // instance of the FileReader
                reader.readAsDataURL(files[0]); // read the local file

                reader.onloadend = function() { // set image data as background of div
                    jQuery("#show-img").css("background-image", "url(" + this.result + ")");
                };
            }
        });
    });
</script>