<?php

namespace Kiuws_Service_Flight_Management\DB;

class FlightSegmentModel extends FlightManagementDB {
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $flight_id;
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
    public $destination_airport_code;
    /**
     * @var string
     */
    public $duration;
    /**
     * @var string
     */
    public $airline_code;
    /**
     * @var string
     */
    public $airline_name;
    /**
     * @var string
     */
    public $flight_number;
    /**
     * @var string
     */
    public $res_book_desig;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Save function
     * @return int
     */
    public function save() {
        $data = [
            'flight_id'                 => $this->flight_id,
            'departure_date_time'       => $this->departure_date_time,
            'arrival_date_time'         => $this->arrival_date_time,
            'origin_airport_code'       => $this->origin_airport_code,
            'destination_airport_code'  => $this->destination_airport_code,
            'duration'                  => $this->duration,
            'airline_code'              => $this->airline_code,
            'airline_name'              => $this->airline_name,
            'flight_number'             => $this->flight_number,
            'res_book_desig'            => $this->res_book_desig
        ];
        $format = [
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        ];
        $this->wpdb->insert($this->flight_management_segments_table, $data, $format);
        $this->id = $this->wpdb->insert_id;
        return $this->id;
    }

    /**
     * Get by flight id
     * @param int $flight_id
     * @return array
     */
    public function get_by_flight_id($flight_id) {
        $sql = "SELECT * FROM $this->flight_management_segments_table WHERE flight_id = $flight_id";
        $result = $this->wpdb->get_results($sql);
        return $result;
    }
}