<?php

namespace Kiuws_Service_Flight_Management\DB;

class FlightTaxModel extends FlightManagementDB {
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
    public $tax_code;
    /**
     * @var float
     */
    public $amount;
    /**
     * @var string
     */
    public $currency_code;

    /**
     * @param int $flight_id
     * @param string $tax_code
     * @param float $amount
     * @param string $currency_code
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
            'flight_id'     => $this->flight_id,
            'tax_code'      => $this->tax_code,
            'amount'        => $this->amount,
            'currency_code' => $this->currency_code
        ];
        $format = [
            '%d',
            '%s',
            '%f',
            '%s'
        ];
        $this->wpdb->insert($this->flight_management_taxes_table, $data, $format);
        $this->id = $this->wpdb->insert_id;
        return $this->id;
    }

    /**
     * @param int $flight_id
     * @return array
     */
    public function get_taxes_by_flight_id($flight_id)
    {
        $sql = "SELECT * FROM $this->flight_management_taxes_table WHERE flight_id = $flight_id";
        return $this->wpdb->get_results($sql);
    }
}