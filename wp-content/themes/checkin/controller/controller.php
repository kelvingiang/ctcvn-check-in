<?php

class Controller_Main
{

    private $_controller_name = 'controller_options';
    private $_controller_options = array();

    public function __construct()
    {
        $defaultOption = array(
            'controller-check-in' => TRUE,
            'controller-check-in-report' => true,
            'controller-check-in-event' => true,
            'controller-check-in-setting' => true,
        );

        $this->_controller_options = get_option($this->_controller_name, $defaultOption);

        $this->page_check_in();
        $this->page_check_in_report();
        $this->page_check_in_event();
        $this->page_check_in_setting();

        add_action('admin_init', array($this, 'do_output_buffer'));
    }

    /* FUNCTION NAY GIAI VIET CHUYEN TRANG BI LOI  */

    public function do_output_buffer()
    {
        ob_start();
    }

    public function page_check_in()
    {
        if ($this->_controller_options['controller-check-in'] == TRUE) {
            require_once(DIR_CONTROLLER . 'controller-check-in.php');
            new Controller_Check_In();
        }
    }

    public function page_check_in_report()
    {
        if ($this->_controller_options['controller-check-in-report'] == TRUE) {
            require_once(DIR_CONTROLLER . 'controller-check-in-report.php');
            new Controller_Check_In_Report();
        }
    }

    public function page_check_in_event()
    {
        if ($this->_controller_options['controller-check-in-event'] == TRUE) {
            require_once(DIR_CONTROLLER . 'controller-check-in-event.php');
            new Controller_Check_In_Event();
        }
    }

    public function page_check_in_setting()
    {
        if ($this->_controller_options['controller-check-in-setting'] == TRUE) {
            require_once(DIR_CONTROLLER . 'controller-check-in-setting.php');
            new Controller_Check_In_Setting();
        }
    }


  

 
}
