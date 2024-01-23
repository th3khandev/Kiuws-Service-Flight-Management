<?php

namespace Kiuws_Service_Flight_Management\DB;

class FlightPassengerModel extends FlightManagementDB {
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
    public $gender;

    /**
     * @var string
     */
    public $birth_date;

    /**
     * @var string
     */
    public $document_type;

    /**
     * @var string
     */
    public $document_number;

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
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $nationality_code;

    /**
     * @var string
     */
    public $nationality_name;

    /**
     * @var string
     */
    public $passport_issue_date;

    /**
     * @var string
     */
    public $passport_expiration_date;

    /**
     * Types constants
     */
    const TYPE_ADULT = 'adult';
    const TYPE_CHILD = 'child';
    const TYPE_INFANT = 'inf';

    /**
     * Get types
     * @return array
     */
    public static function get_types() {
        return [
            self::TYPE_ADULT => 'Adulto',
            self::TYPE_CHILD => 'Niño',
            self::TYPE_INFANT => 'Infante'
        ];
    }

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
            'name'                      => $this->name,
            'last_name'                 => $this->last_name,
            'email'                     => $this->email,
            'phone_country_code'        => $this->phone_country_code,
            'phone_number'              => $this->phone_number,
            'gender'                    => $this->gender,
            'birth_date'                => $this->birth_date,
            'document_type'             => $this->document_type,
            'document_number'           => $this->document_number,
            'type'                      => $this->type,
            'nationality_code'          => $this->nationality_code,
            'nationality_name'          => $this->nationality_name,
            'passport_issue_date'       => $this->passport_issue_date,
            'passport_expiration_date'  => $this->passport_expiration_date
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
            '%s',
            '%s'
        ];
        $this->wpdb->insert($this->flight_management_passengers_table, $data, $format);
        $this->id = $this->wpdb->insert_id;
        return $this->id;
    }

    /**
     * Get passengers by flight id
     * @param int $flight_id
     * @return array
     */
    public function get_passengers_by_flight_id($flight_id) {
        $sql = "SELECT * FROM $this->flight_management_passengers_table WHERE flight_id = $flight_id";
        $result = $this->wpdb->get_results($sql);
        return $result;
    }
}