<?php

namespace Kiuws_Service_Flight_Management\Includes;

use Kiuws_Service_Flight_Management\DB\FlightManagementModel;
use WP_List_Table;

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ReservationTable extends WP_List_Table {
    public function __construct() {
        parent::__construct(array(
            'singular' => 'reservacion',
            'plural'   => 'reservaciones',
            'ajax'     => false,
        ));
    }

    public function get_columns() {
        $columns = [
            'booking_id' => 'ID',
            'departure_date_time' => 'Fecha de salida',
            'arrival_date_time' => 'Fecha de llegada',
            'origin' => 'Origen',
            'destination' => 'Destino',
            'duration' => 'Duración',
            'adults' => 'Adultos',
            'children' => 'Niños',
            'stops' => 'Escalas',
            'total' => 'Total',
            'trip_type' => 'Ida y vuelta',
            'status' => 'Status',
            'actions' => 'Acciones',
        ];
        return $columns;
    }

    public function column_booking_id($item) {
        return $item->booking_id;
    }

    public function column_departure_date_time($item) {
        return $item->departure_date_time;
    }

    public function column_arrival_date_time($item) {
        return $item->arrival_date_time;
    }

    public function column_origin($item) {
        return $item->origin;
    }

    public function column_destination($item) {
        return $item->destination;
    }

    public function column_duration($item) {
        return $item->duration;
    }

    public function column_adults($item) {
        return $item->adults;
    }

    public function column_children($item) {
        return $item->children;
    }

    public function column_stops($item) {
        return $item->stops;
    }

    public function column_total($item) {
        return $item->total;
    }

    public function column_status($item) {
        $text = '';
        $class = '';
        switch ($item->status) {
            case FlightManagementModel::STATUS_PENDING:
                $text = 'Pendiente';
                $class = 'status-pending';
                break;
            case FlightManagementModel::STATUS_COMPLETED:
                $text = 'Confirmado';
                $class = 'status-completed';
                break;
            case FlightManagementModel::STATUS_CANCELLED:
                $text = 'Cancelado';
                $class = 'status-cancelled';
                break;
            case FlightManagementModel::STATUS_BOOKED:
                $text = 'Reservado';
                $class = 'status-booked';
                break;
            case FlightManagementModel::STATUS_PAID:
                $text = 'Pagado';
                $class = 'status-payed';
                break;
        }
        return sprintf('<span class="status %s">%s</span>', $class, $text);
    }

    public function column_trip_type($item) {
        return $item->trip_type == FlightManagementModel::TRIP_TYPE_ROUND_TRIP ? 'SI' : 'NO';
    }

    public function column_actions($item) {
        $actions = [
            'view' => sprintf('<a href="admin.php?page=flight-management&booking_id=%s">Ver</a>', $item->booking_id),
        ];

        if ($item->status == FlightManagementModel::STATUS_PENDING || $item->status == FlightManagementModel::STATUS_BOOKED) {
            $actions['delete'] = sprintf('<a href="javascript:void(0)" data-id="%s" class="cancel-booking">Cancelar</a>', $item->booking_id);
        }

        if ($item->status == FlightManagementModel::STATUS_PAID) {
            $actions['confirm'] = sprintf('<a href="javascript:void(0)" data-id="%s" class="confirm-booking">Confirmardo</a>', $item->booking_id);
        }
        return $this->row_actions($actions);
    }

    public function get_sortable_columns() {
        $sortable_columns = array(
            'booking_id'            => array('booking_id', false),
            'departure_date_time'   => array('departure_date_time', false),
            'arrival_date_time'     => array('arrival_date_time', false),
            'origin'                => array('origin', false),
            'destination'           => array('destination', false),
            'duration'              => array('duration', false),
            'adults'                => array('adults', false),
            'children'              => array('children', false),
            'stops'                 => array('stops', false),
            'total'                 => array('total', false),
            'trip_type'             => array('trip_type', false),
            'status'                => array('status', false),
        );
        return $sortable_columns;
    }


    public function prepare_items() {
        // Aquí debes configurar tus datos y consultas, reemplaza esto con tus datos reales.
        $flightManagementModel = new FlightManagementModel();
        
        $orderby = 'id';
        $order = 'DESC';

        if (isset($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
            $order = strtoupper($_GET['order']);
        }
        $data = $flightManagementModel->getAllFlights($orderby, $order);

        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        $per_page = $this->get_items_per_page('reservaciones_per_page', 10);
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
