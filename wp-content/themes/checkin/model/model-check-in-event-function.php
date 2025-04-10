<?php

class Model_Check_In_Event_Function
{
    private $tbl_guests;
    private $tbl_check_in;
    private $tbl_event;

    public function __construct()
    {
        global $wpdb;
        $this->tbl_guests = $wpdb->prefix . 'guests';
        $this->tbl_check_in = $wpdb->prefix . 'guests_check_in';
        $this->tbl_event = $wpdb->prefix . 'guests_check_in_event';
    }

    public function getAll()
    {
        global $wpdb;
        $sql = "SELECT * FROM $this->tbl_event";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    public function getItem($id)
    {
        global $wpdb;
        $sql = "SELECT * FROM $this->tbl_event WHERE ID = $id";
        $row = $wpdb->get_row($sql, ARRAY_A);
        return $row;
    }

    public function getActiveItem()
    {
        global $wpdb;
        $sql = "SELECT * FROM $this->tbl_event WHERE status = 1";
        $row = $wpdb->get_row($sql, ARRAY_A);
        return $row;
    }

    public function saveItem($arrData = array(), $option = array())
    {
        global $wpdb;
        $data = array(
            'title' => $arrData['txt_title'],
        );

        if ($option == 'edit') {
            // thêm phần tử vào array 
            $data['update_date'] = date('Y-m-d');
            $where = array('ID' => absint($arrData['hidden_ID']));
            $wpdb->update($this->tbl_event,  $data, $where);
        } else if ($option == 'add') {
            $data['create_date'] = date('Y-m-d');
            $wpdb->insert($this->tbl_event, $data);
        }
    }

    public function activeItem($arrData = array(), $option = array())
    {
        global $wpdb;
        $sql = "UPDATE $this->tbl_event SET `status` =  '0' ";
        $wpdb->query($sql);

        $data = array('status' => 1);
        $where = array('id' => absint($arrData['id']));
        $wpdb->update($this->tbl_event, $data, $where);
    }

    public function resetItem($arrData = array(), $option = array())
    {
        global $wpdb;
        $id = $arrData['id'];

        $delDetail = "DELETE FROM $this->tbl_check_in WHERE event_id = $id";
        $wpdb->query($delDetail);
    }

    public function trashItem($arrData = array(), $option = array())
    {
        global $wpdb;

        $trash = $arrData['action'] == 'trash' ? '1' : '0';
        if (!is_array($arrData['id'])) {
            $data = array('trash' => $trash);
            $where = array('ID' => absint($arrData['id'][0]));
            $wpdb->update($this->tbl_event, $data, $where);
        } else {
            $arrData['id'] = array_map('absint', $arrData['id']);
            $ids = join(',', $arrData['id']);
            $sql = "UPDATE $this->tbl_event SET `trash` =  $trash   WHERE ID IN ($ids)";
            $wpdb->query($sql);
        }
    }

    public function deleteItem($arrData = array(), $option = array())
    {

        global $wpdb;
        $id = $arrData['id'];

        $delDetail = "DELETE FROM $this->tbl_check_in WHERE event_id = $id";
        $wpdb->query($delDetail);

        $delEvent = "DELETE FROM $this->tbl_event WHERE ID = $id";
        $wpdb->query($delEvent);
    }

    public function exportCheckIn($id)
    {
        $data = $this->ReportJoinViewByID($id);
        export_excel_check_in($data);
    }

    public function ReportJoinViewByID($id)
    {
        global $wpdb;

        $sql = "SELECT * FROM $this->tbl_check_in AS a 
        LEFT JOIN $this->tbl_guests AS b on a.guests_ID = b.ID
        WHERE a.event_ID = $id 
        GROUP BY a.guests_ID";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }
}
