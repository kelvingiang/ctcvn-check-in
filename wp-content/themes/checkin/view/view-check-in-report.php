<?php
require_once DIR_CODES . 'my-list.php';
$myList = new Codes_My_List();

require_once(DIR_MODEL . 'model-check-in-report.php');
$model = new Admin_Model_Check_In_Report();
//$list = $model->ReportView();
$registerTotal = $model->RegisterTotal();
$listGuests = $model->ReportjoinView();
//$listMember = $model->ReportjoinViewMember();
//$list = array_merge($listGuests,$listMember);

$page = getParams('page');
$branch = $model->ReportBranchView();

//sap xep div>u tu mang trong mang
uasort($branch, 'sort_by_order');
function sort_by_order($a, $b)
{
    //            return $a['percent'] - $b['percent'];
    return $b['percent'] - $a['percent'];
}
?>

<div>
    <div class="check-in-total">
        <label><?php echo ' 登記總數 : ' . $registerTotal['COUNT(ID)']; ?></label>
         <label><?php echo '出席總數 : ' . count($listGuests); ?></label>
    </div>

    <div id="bao-cao">
        <div class=" bao-cao-row bao-cao-header">
            <div><label>分會</label></div>
            <div><label>登記</label></div>
            <div><label>出席</label></div>
            <div><label>比率</label></div>
        </div>

        <?php foreach ($branch as $key => $val) {
        ?>
            <div class="bao-cao-row">
                <div> <label><?php echo $val['code']; ?></label></div>
                <div> <label><?php echo $val['register']; ?></label></div>
                <div> <label><?php echo $val['arrived']; ?></label></div>
                <div> <label><?php echo $val['percent']; ?> %</label></div>
            </div>
        <?php
        }
        ?>
    </div>
</div>

<div class="check-in-content">
    <div class="check-in-content-row header-style">
        <div></div>
        <div><label>姓名</label></div>
        <div><label>分會</label></div>
        <div><label>職稱</label></div>
        <div><label>電話</label></div>
        <div><label>E-mail</label></div>
        <div><label>時間</label></div>
        <div><label>日期</label></div>
    </div>
    <?php foreach ($listGuests as $key => $val) { ?>
        <div class="check-in-content-row">
            <div><label><?php echo $key + 1 ?></label></div>
            <div><label><?php echo $val['full_name'] ?></label></div>
            <div><label><?php echo $myList->get_country($val['country']) ?></label></div>
            <div><label><?php echo $val['position'] ?></label></div>
            <div><label><?php echo $val['phone'] ?></label></div>
            <div><label><?php echo $val['email'] ?></label></div>
            <div><label><?php echo $val['time'] ?></label></div>
            <div><label><?php echo $val['date']; ?></label></div>
        </div>
    <?php } ?>
</div>