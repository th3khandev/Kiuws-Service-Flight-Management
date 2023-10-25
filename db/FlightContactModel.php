<?php

namespace Kiuws_Service_Flight_Management\DB;

class FlightContactModel extends FlightManagementDB {
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
    public $name;
    /**
     * @var string
     */
    public $last_name;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $phone_country_code;
    /**
     * @var string
     */
    public $phone_number;

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
    public function save()
    {
        $data = [
            'flight_id'             => $this->flight_id,
            'name'                  => $this->name,
            'last_name'             => $this->last_name,
            'email'                 => $this->email,
            'phone_country_code'    => $this->phone_country_code,
            'phone_number'          => $this->phone_number
        ];
        $format = [
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        ];
        $this->wpdb->insert($this->flight_management_contacts_table, $data, $format);
        $this->id = $this->wpdb->insert_id;
        return $this->id;
    }

    /**
     * Get contacts by flight id
     * @param int $flight_id
     * @return self
     */
    public function get_contact_by_flight_id($flight_id) {
        $sql = "SELECT * FROM $this->flight_management_contacts_table WHERE flight_id = $flight_id";
        $result = $this->wpdb->get_row($sql);
        return $result;
    }
}