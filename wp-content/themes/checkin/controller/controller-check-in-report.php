<?php
require_once(DIR_MODEL . 'model-check-in-setting.php');
class Controller_Check_In_Report
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'Create'));
    }

    // PHAN TAO MENU CON TRONG MENU CHA CUNG LA POST TYPE
    public function Create()
    {
        $parent_slug = 'check_in_page';
        $page_title = __('報到統計');
        $menu_title = __('報到統計');
        $capability = 'manage_categories';
        $menu_slug = 'check_in_report_page';
        add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, array($this, 'dispatchActive'));
    }

    public function dispatchActive()
    {
        //        echo __METHOD__;
        $action = getParams('action');
        switch ($action) {
            default:
                $this->displayPage();
                break;
        }
    }

    public function displayPage()
    {
        require_once(DIR_VIEW . 'view-check-in-report.php');
    }


}
