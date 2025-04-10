<?php
require_once(DIR_MODEL . 'model-check-in-setting.php');

class Controller_Check_In_Setting
{
    private $_model;
    public function __construct()
    {
        add_action('admin_menu', array($this, 'Create'));
        $this->_model = new Admin_Model_Check_In_Setting();
    }

    // PHAN TAO MENU CON TRONG MENU CHA CUNG LA POST TYPE
    public function Create()
    {
        $parent_slug = 'check_in_page';
        $page_title  = '報到設定';
        $menu_title  = '報到設定';
        $capability  = 'manage_categories';
        $menu_slug   = 'check_in_setting_page';
        // $icon = PART_ICON . '/staff-icon.png';  // THAM SO THU 6 LA LINK DEN ICON DAI DIEN
        $position = 18;
        add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, array($this, 'dispatchActive'), $position);
    }

    public function dispatchActive()
    {
        //        echo __METHOD__;
        $action = getParams('action');
        switch ($action) {
            case 'check_in_start':
                $this->CheckInStart();
                break;
            case 'export_guests':
                $this->ExportGuestsAction();
                break;
            case 'import_guests':
                $this->ImportGuestsAction();
                break;
            case 'import_guests_info':
                $this->ImportGuestsInfoAction();
                break;
            case 'create_qrcode':
                $this->CreateQRCodeAction();
                break;
            case 'open_qrcode_folder':
                $this->openFOlderAction();
                break;
            default:
                $this->displayPage();
                break;
        }
    }

    public function displayPage()
    {
        require_once(DIR_VIEW . 'view-check-in-setting.php');
    }

    public function openFOlderAction()
    {
        //== mở folder bằng explorer ==================================
        $directory = get_post_meta(1, '_part_text', true);
        
        // 检查目录是否存在
        if (is_dir($directory)) {
            // 使用 explorer 命令打开资源管理器并显示目录
            exec("start explorer $directory");
        } else {
            echo "目录 $directory 不存在";
        }
        ToBack(1);
    }

    public function ExportGuestsAction()
    {

        $this->_model->ExportGuests();
    }

    public function CheckInStart()
    {
        if (isPost()) {
            update_option("Waiting_text", $_POST['txtWait']);
            update_option("Title_text", $_POST['txtTitle']);
            update_post_meta(1, '_part_text', $_POST['txtPart']);

            $paged = max(1, getParams('page'));
            $url = 'admin.php?page=' . $_REQUEST['page'] . '&paged=' . $paged . '&msg=1';
            wp_redirect($url);
        }
        require_once(DIR_VIEW . 'view-check-in-waiting.php');
    }

    // Import Group Function 

    public function ImportGuestsInfoAction()
    {
        if (isPost()) {
            $errors = array();
            $file_name = $_FILES['myfile']['name'];
            $file_size = $_FILES['myfile']['size'];
            $file_tmp = $_FILES['myfile']['tmp_name'];
            $file_type = $_FILES['myfile']['type'];

            $file_trim = ((explode('.', $_FILES['myfile']['name'])));
            $trim_name = strtolower($file_trim[0]);
            $trim_type = strtolower($file_trim[1]);
            //$name = $_SESSION['login'];
            // $cus_name = 'avatar-'.$name . '.' . $trim_type;  //tao name moi cho file tranh trung va mat file

            $extensions = array("xls", "xlsx");
            if (in_array($trim_type, $extensions) === false) {
                $errors[] = "extension not allowed, please choose a excel file.";
            }
            if ($file_size > 20097152) {
                $errors[] = 'File size must be excately 20 MB';
            }
            if (empty($errors)) {
                $path = DIR_FILE;
                move_uploaded_file($file_tmp, ($path . $file_name));

                $excelList = $path . $file_name;
                // require_once(DIR_MODEL . 'model_check_in_setting.php');
                // $model = new Model_Check_In_Setting();
                $this->_model->ImportGuestsAdditional($excelList);

                //                ToBack();
            }
        }
        require_once(DIR_VIEW . 'view-guests-import.php');
    }

    public function ImportGuestsAction()
    {
        if (isPost()) {
            $errors = array();
            $file_name = $_FILES['myfile']['name'];
            $file_size = $_FILES['myfile']['size'];
            $file_tmp = $_FILES['myfile']['tmp_name'];
            $file_type = $_FILES['myfile']['type'];

            $file_trim = ((explode('.', $_FILES['myfile']['name'])));
            $trim_name = strtolower($file_trim[0]);
            $trim_type = strtolower($file_trim[1]);
            //$name = $_SESSION['login'];
            // $cus_name = 'avatar-'.$name . '.' . $trim_type;  //tao name moi cho file tranh trung va mat file

            $extensions = array("xls", "xlsx");
            if (in_array($trim_type, $extensions) === false) {
                $errors[] = "extension not allowed, please choose a excel file.";
            }
            if ($file_size > 20097152) {
                $errors[] = 'File size must be excately 20 MB';
            }
            if (empty($errors)) {
                $path = DIR_FILE;
                move_uploaded_file($file_tmp, ($path . $file_name));
                $excelList = $path . $file_name;
                $this->_model->ImportGuests($excelList);
                ToBack();
            }
        }
        require_once(DIR_VIEW . 'view-guests-import.php');
    }


    public function CreateQRCodeAction()
    {
        $this->_model->create_QRCode();
        ToBack();
    }



}
