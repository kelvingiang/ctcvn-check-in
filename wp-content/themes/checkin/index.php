<?php get_template_part('templates/template', 'header') ?>

<div class="my-waiting">
    <img src="<?php echo PART_IMAGES . 'loading_pr2.gif' ?>" style=" width: 150px" />
</div>

<div class="content">
    <div class="content-form">
        <div class="f-check-in">
            <form name="check-form" id="check-form" method="post" action="">
                <div class="input-wrapper">
                    <input type="text" id="txt-barcode" name="txt-barcode" placeholder="輸入條碼" required />
                    <button type="button" id="btn-submit" name="btn-submit">提交</button>
                </div>
            </form>
        </div>
        <div class="ad-space">
            <img src="<?php echo PART_IMAGES . 'digiwin_logo.png'; ?>" /> </br>
            <h3>鼎捷軟件(越南)維護製作</h3>
        </div>
    </div>
    <div class="content-info">
        <div class="content-welcome">
            <div>
                <div class="guest_name">
                    <label id="guest_name">&nbsp;</label>
                </div>
            </div>
        </div>
        <div id="barcode-error">條 碼 不 正 確 ! </div>
        <div id="barcode-unactive">您 的 帳 號 還 沒 啟 用! </div>
        <div id="guest-main">
            <div class="guest-img">
                <div id="guest-picture"> </div>
            </div>

            <div class="guest-info">
                <!-- <div class="guest_name">
                    <label id="guest_name">&nbsp;</label>
                </div> -->
                <div>
                    <label>職 稱 : </label>
                    <label id="guest_position">&nbsp;</label>
                </div>
                <div>
                    <label>分 會 : </label>
                    <label id="guest_country">&nbsp;</label>
                </div>
                <div>
                    <label>電 郵 : </label>
                    <label id="guest_email">&nbsp; </label>
                </div>
                <div>
                    <label>電 話 : </label>
                    <label id="guest_phone">&nbsp;</label>
                </div>
                <div>
                    <label>備 註 :</label>
                    <label class="guest_note">&nbsp;</label>
                </div>
                <div id="last-check-in"> </div>
                <div id="last-check-in-time"> </div>
            </div>
        </div>
    </div>
</div>

<?php get_template_part('templates/template', 'footer') ?>


<script type="text/javascript">
    jQuery(document).ready(function() {

        jQuery("#txt-barcode").focus();

        jQuery('#btn-submit').click(function(e) { //     console.log(objInfo);
            e.preventDefault();
            submitAction();
        });

        jQuery('#txt-barcode').keydown(function(e) {
            if (e.key === "Enter" || e.keyCode === 13) {
                e.preventDefault(); // 避免表單自動提交
                submitAction();
            }
        });

        function submitAction() {
            var barcode = jQuery('#txt-barcode').val().trim();
            jQuery('.my-waiting').css('display', 'block');


            jQuery.ajax({
                url: '<?php echo get_template_directory_uri() . '/ajax/updata-checkin.php' ?>', // lay doi tuong chuyen sang dang array
                type: 'post', //                data: $(this).serialize(),
                data: {
                    id: barcode
                },
                dataType: 'json',
                success: function(
                    data) { // set ket qua tra ve  data tra ve co thanh phan status va message
                    if (data.status === 'done') {
                        jQuery("#guest_name").show();
                        jQuery("#txt-barcode").val('');
                        //window.location.reload();  
                        jQuery('#barcode-error, #barcode-unactive').css('display', 'none');
                        jQuery('#last-check-in, #last-check-in-time, #guest-main').css('display', 'flex');
                        jQuery('#last-check-in').children().remove();
                        jQuery('#last-check-in-time').children().remove();
                        if (data.info.TotalTimes !== "0") {
                            jQuery('#last-check-in').append("<label>次數 : </label> <label>" + data.info
                                .TotalTimes + " 次  </label>");
                            jQuery('#last-check-in-time').append("<label>時間 : </label> <label>" + data.info
                                .LastCheckIn + "</label>");
                        }
                        jQuery('#guest_name').text(data.info.FullName);
                        jQuery('#guest_position').text(data.info.Position);
                        jQuery('#guest_country').text(data.info.Country);
                        jQuery('#guest_email').text(data.info.Email);
                        jQuery('#guest_phone').text(data.info.Phone);
                        jQuery('#guest_note').text(data.info.Note);
                        jQuery('#guest-pic').remove();
                        jQuery('#guest-picture').append(data.info.Img);
                        //window.location.reload();
                        window.setTimeout(function() {
                            jQuery('.my-waiting').css('display', 'none');
                        }, 100);

                    } else if (data.status === 'error') {
                        jQuery("#txt-barcode").val('');
                        jQuery('#guest-main, #last-check-in, #last-check-in-time, #barcode-unactive, #guest_name').css('display',
                            'none');
                        jQuery('#barcode-error').css('display', 'block');
                        window.setTimeout(function() {
                            jQuery('.my-waiting').css('display', 'none');
                        }, 100);
                    } else if (data.status === "unactive") {
                        jQuery("#txt-barcode").val('');
                        jQuery('#guest-main, #last-check-in, #last-check-in-time, #barcode-error, #guest_name').css('display',
                            'none');
                        jQuery('#barcode-unactive').css('display', 'block');
                        window.setTimeout(function() {
                            jQuery('.my-waiting').css('display', 'none');
                        }, 100);
                    }
                },
                error: function(xhr) {
                    console.log(xhr.reponseText);
                    //console.log(data.status);
                }
            });
        }
    });
</script>