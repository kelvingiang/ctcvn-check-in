<?php
require_once(DIR_MODEL . 'model-check-in-function.php');


class Controller_Check_In
{
    private $_model = null;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'create'));
        $this->_model = new Model_Check_In_Function();
    }

    public function create()
    {
        // THEM 1 NHOM MENU MOI VAO TRONG ADMIN MENU
        $page_title = "報到系統"; // TIEU DE CUA TRANG 
        $menu_title = "報到系統";  // TEN HIEN TRONG MENU
        // CHON QUYEN TRUY CAP manage_categories DE role ADMINNITRATOR VÀ EDITOR DEU THAY DUOC
        $capability = 'manage_categories'; // QUYEN TRUY CAP DE THAY MENU NAY
        $menu_slug = 'check_in_page'; // TEN slug TEN DUY NHAT KO DC TRUNG VOI TRANG KHAC GAN TREN THANH DIA CHI OF MENU
        // THAM SO THU 5 GOI DEN HAM HIEN THI GIAO DIEN TRONG MENU
        $icon = PART_IMAGES . 'icon/staff-icon.png';  // THAM SO THU 6 LA LINK DEN ICON DAI DIEN
        $position = 3; // VI TRI HIEN THI TRONG MENU

        add_menu_page($page_title, $menu_title, $capability, $menu_slug, array($this, 'dispatchActive'), $icon, $position);
    }

    // Phan dieu huong 
    public function dispatchActive()
    {
        $action = getParams('action');
        switch ($action) {
            case 'add':
            case 'edit':
                $this->saveAction();
                break;
            case 'delete':
                $this->deleteAction();
                break;
            case 'trash':
                $this->trashAction();
                break;
            case 'uncheckin':
                $this->checkinAction();
                break;
            case 'restore':
                $this->restoreAction();
                break;
            default:
                $this->displayPage();
                break;
        }
    }

    public function createUrl()
    {
        echo $url = 'admin.php?page=' . getParams('page');

        //filter_status
        if (getParams('filter_branch') != '0') {
            $url .= '&filter_branch=' . getParams('filter_branch');
        }

        if (mb_strlen(getParams('s'))) {
            $url .= '&s=' . getParams('s');
        }

        return $url;
    }

    //---------------------------------------------------------------------------------------------
    // Cmt CAC CHUC NANG THEM XOA SUA VA HIEN THI
    //---------------------------------------------------------------------------------------------
    // CAC DISPLAY PAGE
    public function displayPage()
    {
        // LOC DU LIEU KHI action = -1 CO NGHIA LA DANG LOI DU LIEU (CHO 2 TRUONG HOP search va filter)
        if (getParams('action') == -1) {
            $url = $this->createUrl();
            wp_redirect($url);
        }
        // NEN TACH ROI HTML VA CODE WP RA CHO DE QUAN LY
        require_once(DIR_VIEW . 'view-check-in.php');
    }

    // THEM MOI ITEM
    public function saveAction()
    {
        // KIEM TRA PHUONG THUC GET HAY POST
        if (isPost()) {
            $option = getParams('action');
            $this->_model->saveItem($_POST, $option);
            ToBack(1);
        }
        require_once(DIR_VIEW . 'from-check-in.php');
        
    }

  

    public function checkinAction()
    {
        global $wpdb;
        
        $params = getParams();
        $table = $wpdb->prefix . 'guests_check_in';
        
        $sqlDetail = "SELECT * FROM $table WHERE guests_id =" . $params['id'] . " AND event_id = " . $params['event_id'];
        $detail = $wpdb->get_row($sqlDetail, ARRAY_A);
       
        if (count($detail) == ' ') {
            $this->_model->checkin($params);
        }else{
            $this->_model->uncheckin($params);
        }
        ToBack();
    }

    // XOA DU LIEU
    public function deleteAction()
    {
        //$arrParam = getParams();
        $this->_model->deleteItem(getParams());
        ToBack();
    }

    public function restoreAction()
    {
        $this->_model->restoreItem(getParams());
        ToBack();
    }

    public function trashAction()
    {
        $this->_model->trashItem(getParams());
        ToBack();
    }
}
