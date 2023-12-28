<?php 

namespace Kiuws_Service_Flight_Management\DB;

class FlightManagementAirportModel extends FlightManagementDB {
    /**
     * @var int $id
     */
    public $id;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $code
     */
    public $code;

    /**
     * @var string $city_name
     */
    public $city_name;

    /**
     * @var string $state_name
     */
    public $state_name;

    /**
     * @var string $country_name
     */
    public $country_name;

    /**
     * @var string $table_name
     */
    public $table_name;

    /**
     * class construntor
     */
    public function __construct() {
        parent::__construct();
        $this->table_name = $this->flight_management_airports_table;
    }

    /**
     * Create airport instance
     * @param mixed $data
     * @return FlightManagementAirportModel
     */
    private function createAirportInstance($data) {
        $airport = new FlightManagementAirportModel();
        $airport->id = $data->id;
        $airport->name = $data->name;
        $airport->code = $data->code;
        $airport->city_name = $data->city_name;
        $airport->state_name = $data->state_name;
        $airport->country_name = $data->country_name;
        return $airport;
    }

    /**
     * Get all airports
     * @return array
     */
    public function getAirports($orderBy = 'id', $order = 'ASC') {
        $sql = "SELECT * FROM $this->table_name ORDER BY $orderBy $order";
        $airports = $this->wpdb->get_results($sql);
        $airports = array_map([$this, 'createAirportInstance'], $airports);
        return $airports;
    }

    /**
     * Get airport by code
     * @param string $code
     * @return FlightManagementAirportModel
     */
    public function getAirportByCode($code) {
        $sql = "SELECT * FROM $this->table_name WHERE code = '$code'";
        $airport = $this->wpdb->get_row($sql);
        $airport = $this->createAirportInstance($airport);
        return $airport;
    }

    /**
     * Get airport by id
     * @param int $id
     * @return FlightManagementAirportModel
     */
    public function getAirportById($id) {
        $sql = "SELECT * FROM $this->table_name WHERE id = $id";
        $airport = $this->wpdb->get_row($sql);
        $airport = $this->createAirportInstance($airport);
        return $airport;
    }

    /**
     * Save airport
     * @param FlightManagementAirportModel $airport
     * @return int
     */
    public function save () {
        $data = [
            'code'          => $this->code,
            'name'          => $this->name,
            'city_name'     => $this->city_name,
            'state_name'    => $this->state_name,
            'country_name'  => $this->country_name,
        ];
        $format = [
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
        ];
        $this->wpdb->insert($this->table_name, $data, $format);
        return $this->wpdb->insert_id;
    }

    /**
     * Update airport
     * @param FlightManagementAirportModel $airport
     * @return int
     */
    public function update () {
        $data = [
            'code' => $this->code,
            'name' => $this->name,
            'city_name' => $this->city_name,
            'state_name' => $this->state_name,
            'country_name' => $this->country_name,
        ];
        $where = [
            'id' => $this->id,
        ];
        $format = [
            '%s',
            '%s',
            '%s',
            '%s',
        ];
        $where_format = [
            '%d',
        ];
        $this->wpdb->update($this->table_name, $data, $where, $format, $where_format);
        return $this->wpdb->insert_id;
    }

    /**
     * Delete airport
     * @param int $id
     * @return int
     */
    public function delete () {
        $where = [
            'id' => $this->id,
        ];
        $where_format = [
            '%d',
        ];
        $this->wpdb->delete($this->table_name, $where, $where_format);
        return $this->wpdb->insert_id;
    }
    
}