<?php
require_once(DIR_MODEL . 'model-check-in-event-function.php');

class Controller_Check_In_Event
{
    private $_model;
    public function __construct()
    {
        add_action('admin_menu', array($this, 'Create'));
        $this->_model = new Model_Check_In_Event_Function();
    }

    // PHAN TAO MENU CON TRONG MENU CHA CUNG LA POST TYPE
    public function Create()
    {
        $parent_slug = 'check_in_page';
        $page_title = __('報到事件');
        $menu_title = __('報到事件');
        $capability = 'manage_categories';
        $menu_slug = 'check_in_event_page';
        // $icon = PART_ICON . '/staff-icon.png';  // THAM SO THU 6 LA LINK DEN ICON DAI DIEN
        $position = 20;
        add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, array($this, 'dispatchActive'), $position);
    }

    public function dispatchActive()
    {
        //        echo __METHOD__;
        $action = getParams('action');
        switch ($action) {
            case 'add':
            case 'edit':
                $this->saveAction();
                break;
            case 'active':
                $this->activeAction();
                break;
            case 'view':
                $this->viewAction();
                break;
            case 'trash':
            case 'restore':
                $this->trashAction();
                break;
            case 'delete':
                $this->deleteAction();
                break;
            case 'reset':
                $this->resetAction();
                break;    
            case 'export':
                $this->exportAction();
                break;
            default:
                $this->displayPage();
                break;
        }
    }

    public function displayPage()
    {
        require_once(DIR_VIEW . 'view-check-in-event.php');
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
        require_once(DIR_VIEW . 'from-check-in-event.php');
    }

    public function resetAction(){
        $this->_model->resetItem(getParams());
        ToBack();
    }

    public function trashAction()
    {
        $this->_model->trashItem(getParams());
        ToBack();
    }

    public function deleteAction(){
        $this->_model->deleteItem(getParams());
        ToBack();
        
    }

    public function activeAction()
    {
        $this->_model->activeItem(getParams());
        ToBack();
    }

    public function viewAction()
    {
        require_once(DIR_VIEW . 'view-check-in-event-detail.php');
    }

    public function exportAction()
    {
        $id = getParams('id');
        $this->_model->ExCheckInToExcelByID($id);
    }
}
