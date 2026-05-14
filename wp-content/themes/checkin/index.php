<?php get_template_part('templates/template', 'header') ?>

<div class="my-waiting">
    <img src="<?php echo PART_IMAGES . 'loading_pr2.gif' ?>" style=" width: 150px" />
</div>

<div class="content">


    <div class="content-info">
        <div id="load-new">
            <img src="<?php echo PART_IMAGES . 'bg/event-bg.jpg' ?>" />
        </div>
        <div id="barcode-error">條 碼 不 正 確! </div>
        <div id="barcode-unactive">您 的 帳 號 還 沒 啟 用! </div>

        <div id="guest-main">

            <div class="guest-img">
                <div id="guest-picture"> </div>
            </div>

            <div class="guest-info">
                <div class="guest_name">
                    <label id="guest_name"></label>
                    <label id="guest_position"></label>
                </div>

                <div class="guest_contact">

                    <label id="guest_country" class="guest_contact_content"></label>
                </div>

                <div class="guest_contact">
                    <label class="guest_contact_title"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free v5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"/></svg>電郵</label>
                    <label id="guest_email" class="guest_contact_content"></label>
                </div>
                <div class="guest_contact">
                    <label class="guest_contact_title"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free v5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"/></svg>電話</label>
                    <label id="guest_phone" class="guest_contact_content"></label>
                </div>
                <div class="check-in-success">
                    <label><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free v5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"/></svg>報到成功</label>
                </div>


                <div id="last-check-in-time"> </div>
            </div>
        </div>

        <div class="ad">
            <img src=" <?php echo PART_IMAGES . 'digiwin_logo.png' ?>" alt="ctcvn_logo" title="ctcvn_logo" /> </br>
            <h3>鼎捷軟件(越南)維護製作</h3>
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
            if (barcode != '') {
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
                                jQuery('#last-check-in').append("<label>" + data.info
                                    .TotalTimes + "</label>");
                                // jQuery('#last-check-in-time').append("<label>時間 : </label> <label>" + data.info
                                //     .LastCheckIn + "</label>");
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
                            }, 600);
                              jQuery('#load-new').css('display', 'none');
                              jQuery('#guest-main').css('display', 'flex');

                        } else if (data.status === 'error') {
                            jQuery("#txt-barcode").val('');
                            jQuery('#guest-main, #last-check-in, #last-check-in-time, #barcode-unactive, #guest_name').css('display',
                                'none');
                            jQuery('#barcode-error').css('display', 'block');
                            jQuery('#load-new').css('display', 'none');
                            window.setTimeout(function() {
                                jQuery('.my-waiting').css('display', 'none');
                            }, 100);
                        } else if (data.status === "unactive") {
                            jQuery("#txt-barcode").val('');
                            jQuery('#guest-main, #last-check-in, #last-check-in-time, #barcode-error, #guest_name').css('display',
                                'none');
                            jQuery('#barcode-unactive').css('display', 'block');
                              jQuery('#load-new').css('display', 'none');
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
        }
    });
</script>