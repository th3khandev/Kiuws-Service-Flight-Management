<?php 

namespace Kiuws_Service_Flight_Management\DB;

class FlightPaymentInfoModel extends FlightManagementDB {
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
    public $card_number;

    /**
     * @var string
     */
    public $card_holder_name;

    /**
     * @var string
     */
    public $card_holder_document_number;

    /**
     * @var string
     */
    public $card_holder_email;

    /**
     * @var string
     */
    public $reference;

    /**
     * @var string
     */
    public $currency;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Save function
     * @return int
     */
    public function save() {
        $data = [
            'flight_id' => $this->flight_id,
            'card_number' => $this->card_number,
            'card_holder_name' => $this->card_holder_name,
            'card_holder_document_number' => $this->card_holder_document_number,
            'card_holder_email' => $this->card_holder_email,
            'reference' => $this->reference,
            'currency' => $this->currency,
        ];

        $format = [
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
        ];

        
        $this->wpdb->insert($this->flight_management_payment_info_table, $data, $format);
        $this->id = $this->wpdb->insert_id;

        return $this->id;
    }
}