<?php

namespace Kiuws_Service_Flight_Management\DB;

class FlightManagementModel extends FlightManagementDB {
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $departure_date_time;
    /**
     * @var string
     */
    public $arrival_date_time;
    /**
     * @var string
     */
    public $origin_airport_code;
    /**
     * @var string
     */
    public $origin;
    /**
     * @var string
     */
    public $destination_airport_code;
    /**
     * @var string
     */
    public $destination;
    /**
     * @var string
     */
    public $duration;
    /**
     * @var int
     */
    public $stops;
    /**
     * @var int
     */
    public $adults;
    /**
     * @var int
     */
    public $children;
    /**
     * @var float
     */
    public $base_fare;
    /**
     * @var float
     */
    public $total_taxes;
    /**
     * @var float
     */
    public $total;
    /**
     * @var string
     */
    public $currency_code;
    /**
     * @var int
     */
    public $status;
    /**
     * @var string
     */
    public $booking_id;
    /**
     * @var string
     */
    public $ticket_time_limit;
    /**
     * @var string
     */
    public $price_info_response;

    /**
     * Status of flight
     */
    const STATUS_PENDING = 1;
    const STATUS_BOOKED = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_PAYED = 4;
    const STATUS_COMPLETED = 5;

    /**
     * table name
     */
    private $table_name;

    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct();
        $this->table_name = $this->flight_management_table;
    }

    /**
     * Get flight by id
     */
    public function getFlightById($id) {
        $sql = "SELECT * FROM $this->table_name WHERE id = $id";
        $result = $this->wpdb->get_row($sql);
        return $result;
    }

    /**
     * Save flight
     */
    public function save() {
        $data = [
            'departure_date_time'       => $this->departure_date_time,
            'arrival_date_time'         => $this->arrival_date_time,
            'origin_airport_code'       => $this->origin_airport_code,
            'origin'                    => $this->origin,
            'destination_airport_code'  => $this->destination_airport_code,
            'destination'               => $this->destination,
            'duration'                  => $this->duration,
            'stops'                     => $this->stops,
            'adults'                    => $this->adults,
            'children'                  => $this->children,
            'base_fare'                 => $this->base_fare,
            'total_taxes'               => $this->total_taxes,
            'total'                     => $this->total,
            'currency_code'             => $this->currency_code,
            'status'                    => $this->status,
            'booking_id'                => $this->booking_id,
            'ticket_time_limit'         => $this->ticket_time_limit,
            'price_info_response'       => $this->price_info_response
        ];
        $format = [
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%d',
            '%d',
            '%f',
            '%f',
            '%f',
            '%s',
            '%d',
            '%s',
            '%s',
            '%s'
        ];
        $this->wpdb->insert($this->table_name, $data, $format);
        $this->id = $this->wpdb->insert_id;
        return $this->id;
    }

    /**
     * Get all flights
     * @return self[]
     */
    public function getAllFlights($orderBy = 'id', $order = 'DESC') {
        $sql = "SELECT * FROM $this->table_name";
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy $order";
        }
        $result = $this->wpdb->get_results($sql);
        return $result;
    }
}