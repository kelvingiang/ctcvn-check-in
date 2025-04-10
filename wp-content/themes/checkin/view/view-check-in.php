<?php
require_once(DIR_MODEL . 'model-check-in.php');
$show_list = new Model_Check_In();
$show_list->prepare_items();
$lbl = '';
$page =  getParams('page');
$linkAdd  = admin_url('admin.php?page=' . $page . '&action=add');  // TAO LINH CHO ADD NEW
$lblAdd    = "新增";
if (getParams('msg') == 1) {
    $msg = '<div class="updated notice notice-success is-dismissible"><p> ' . __('Data adjustment is successful') . ' </p></div>';
}
?>
<div class="wrap">
    <h2 style="font-weight: bold">
        <?php echo esc_html__($lbl); ?>
        <a href="<?php echo esc_url($linkAdd); ?>" class="add-new-h2"><?php echo esc_html__($lblAdd); ?></a>
    </h2>
    <?php echo @$msg; ?>
    <form action="" method="post" name="<?php echo $page; ?>" id="<?php echo $page; ?>">
        <?php $show_list->search_box('查詢', 'search_id') ?>
        <?php $show_list->views(); ?>
        <?php $show_list->display(); ?>
    </form>
</div>