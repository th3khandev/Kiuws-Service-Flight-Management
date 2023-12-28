<?php

namespace Kiuws_Service_Flight_Management\Includes;

use Kiuws_Service_Flight_Management\DB\FlightManagementAirportModel;
use WP_List_Table;

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class AirportsTable extends WP_List_Table {
    public function __construct() {
        parent::__construct(array(
            'singular' => 'Areopuerto',
            'plural'   => 'Areopuertos',
            'ajax'     => false,
        ));
    }

    public function get_columns() {
        $columns = [
            'id'            => 'ID',
            'code'          => 'Código',
            'name'          => 'Nombre',
            'city_name'     => 'Ciudad',
            'state_name'    => 'Estado/Región',
            'country_name'  => 'País',
            'actions'       => 'Acciones',
        ];
        return $columns;
    }

    public function column_id($item) {
        return $item->id;
    }

    public function column_code($item) {
        return $item->code;
    }

    public function column_name($item) {
        return $item->name;
    }

    public function column_city_name($item) {
        return $item->city_name;
    }

    public function column_state_name($item) {
        return $item->state_name;
    }

    public function column_country_name($item) {
        return $item->country_name;
    }

    public function column_actions($item) {
        $actions = [
            'view' => sprintf('<a href="admin.php?page=flight-management-airports&action=edit&airport_id=%s">Editar</a>', $item->id),
            'delete' => sprintf('<a href="admin.php?page=flight-management-airports&action=delete&airport_id=%s">Eliminar</a>', $item->id),
        ];
        return $this->row_actions($actions);
    }

    public function get_sortable_columns() {
        $sortable_columns = array(
            'id'            => array('id', false),
            'code'          => array('code', false),
            'name'          => array('name', false),
            'city_name'     => array('city_name', false),
            'state_name'    => array('state_name', false),
            'country_name'  => array('country_name', false),
        );
        return $sortable_columns;
    }


    public function prepare_items() {
        // Aquí debes configurar tus datos y consultas, reemplaza esto con tus datos reales.
        $flightManagementModel = new FlightManagementAirportModel();
        
        $orderby = 'id';
        $order = 'DESC';

        if (isset($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
            $order = strtoupper($_GET['order']);
        }
        $data = $flightManagementModel->getAirports($orderby, $order);

        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        $per_page = $this->get_items_per_page('airports_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items = count($data);

        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
        $this->items = $data;

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);
    }
}
