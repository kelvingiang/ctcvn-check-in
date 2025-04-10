<?php
require_once DIR_MODEL . 'model-check-in-report.php';
$model = new Admin_Model_Check_In_Report();
$id = getParams('id');
$data = $model->ReportJoinViewByID($id);
$actionEvent = $model->getActionEventById($id);
?>
<div class="check-in-event-title">
    <?php echo $actionEvent['title'] ?? null ?>
</div>

<div class="check-in-head">

    <div class="check-in-total">
        <?php echo __('總數') . ' : ' . count($data); ?>
    </div>
    <div>
        <a class="button button-primary" href="<?php echo "admin.php?page=check_in_event_page&action=export&id=$id" ?>">導出記錄</a>
    </div>


</div>

<div class="check-in-content">
    <div class="check-in-content-row header-style">
        <div></div>
        <div>姓名</div>
        <div>分會</div>
        <div>職稱</div>
        <div>電話</div>
        <div>E-mail</div>
        <div>時間</div>
        <div>日期</div>
    </div>
    <?php foreach ($data as $key => $val) {
        require_once DIR_CODES . 'my-list.php';
        $myList = new Codes_My_List();
        $country = $myList->get_country($val['country']);
    ?>
        <div class="check-in-content-row">
            <div><?php echo $key + 1 ?></div>
            <div><?php echo $val['full_name'] ?></div>
            <div><?php echo $country ?></div>
            <div><?php echo $val['position'] ?></div>
            <div><?php echo $val['phone'] ?></div>
            <div><?php echo $val['email'] ?></div>
            <div><?php echo $val['time']  ?></div>
            <div><?php echo $val["date"] ?></div>
        </div>
    <?php } ?>
</div>