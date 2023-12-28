<?php

namespace Kiuws_Service_Flight_Management\DB;

class FlightManagementDB {
    public $wpdb;
    public $flight_management_table;
    public $charset_collate;
    public $flight_management_taxes_table;
    public $flight_management_segments_table;
    public $flight_management_passengers_table;
    public $flight_management_contacts_table;
    public $flight_management_payment_info_table;
    public $flight_management_airports_table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->flight_management_table = $wpdb->prefix . 'flight_management';
        $this->flight_management_taxes_table = $this->flight_management_table . '_taxes';
        $this->flight_management_segments_table = $this->flight_management_table . '_segments';
        $this->flight_management_passengers_table = $this->flight_management_table . '_passengers';
        $this->flight_management_contacts_table = $this->flight_management_table . '_contacts';
        $this->flight_management_payment_info_table = $this->flight_management_table . '_payment_info';
        $this->flight_management_airports_table = $this->flight_management_table . '_airports';
        $this->charset_collate = $wpdb->get_charset_collate();
    }

    private function createFlightManagementTable () {
        $sql = "CREATE TABLE `$this->flight_management_table` (`id` INT(11) NULL AUTO_INCREMENT, `departure_date_time` DATETIME NOT NULL , `arrival_date_time` DATETIME NOT NULL , `origin_airport_code` VARCHAR(5) NOT NULL, `origin` VARCHAR(255) NOT NULL , `destination_airport_code` VARCHAR(5) NOT NULL , `destination` VARCHAR(255) NOT NULL , `duration` VARCHAR(100) NOT NULL , `stops` INT NOT NULL , `adults` INT NOT NULL , `children` INT NOT NULL, `inf` INT NOT NULL, `base_fare` DECIMAL(10,2) NOT NULL , `total_taxes` DECIMAL(10,2) NOT NULL , `extra` DECIMAL(10,2) NOT NULL, `total` DECIMAL(10,2) NOT NULL, `currency_code` VARCHAR(5) NOT NULL, `status` INT(1) NOT NULL DEFAULT(1), `trip_type` INT(1) NOT NULL DEFAULT(1), `booking_id` VARCHAR(200) NOT NULL, `ticket_time_limit` VARCHAR(200) NOT NULL, `price_info_response` TEXT NOT NULL, PRIMARY KEY (`id`)) $this->charset_collate;";
        dbDelta($sql);
    }

    private function createFlightManagementTaxesTable () {
        $sql = "CREATE TABLE `$this->flight_management_taxes_table` (`id` INT(11) NULL AUTO_INCREMENT, `flight_id` INT(11) NOT NULL , `tax_code` VARCHAR(5) NOT NULL , `amount` DECIMAL(10,2) NOT NULL , `currency_code` VARCHAR(5) NOT NULL, PRIMARY KEY (`id`)) $this->charset_collate;";
        dbDelta($sql);
    }

    private function createFlightManagementSegmentsTable () {
        $sql = "CREATE TABLE `$this->flight_management_segments_table` (`id` INT(11) NULL AUTO_INCREMENT, `flight_id` INT(11) NOT NULL , `departure_date_time` DATETIME NOT NULL , `arrival_date_time` DATETIME NOT NULL , `origin_airport_code` VARCHAR(5) NOT NULL, `destination_airport_code` VARCHAR(5) NOT NULL, `duration` VARCHAR(100) NOT NULL , `airline_code` VARCHAR(5) NOT NULL, `airline_name` VARCHAR(200) NOT NULL, `flight_number` VARCHAR(100) NOT NULL, `res_book_desig` VARCHAR(3) NOT NULL, PRIMARY KEY (`id`)) $this->charset_collate;";
        dbDelta($sql);
    }

    private function createFlightManagementPassengersTable () {
        $sql = "CREATE TABLE `$this->flight_management_passengers_table` (`id` INT(11) NULL AUTO_INCREMENT, `flight_id` INT(11) NOT NULL , `type` VARCHAR(5) NOT NULL, `name` VARCHAR(50) NOT NULL, `last_name` VARCHAR(50) NOT NULL, `email` VARCHAR(50) NOT NULL, `gender` VARCHAR(1) NOT NULL, `birth_date` DATE NOT NULL, `document_type` VARCHAR(5) NOT NULL, `document_number` VARCHAR(50) NOT NULL, `phone_country_code` VARCHAR(5) NOT NULL, `phone_number` VARCHAR(50) NOT NULL, PRIMARY KEY (`id`)) $this->charset_collate;";
        dbDelta($sql);
    }

    private function createFlightManagementContactsTable () {
        $sql = "CREATE TABLE `$this->flight_management_contacts_table` (`id` INT(11) NULL AUTO_INCREMENT, `flight_id` INT(11) NOT NULL, `name` VARCHAR(50) NOT NULL, `last_name` VARCHAR(50) NOT NULL, `email` VARCHAR(50) NOT NULL, `phone_country_code` VARCHAR(5) NOT NULL, `phone_number` VARCHAR(50) NOT NULL, PRIMARY KEY (`id`)) $this->charset_collate;";
        dbDelta($sql);
    }

    private function createFlightManagementPaymentInfo () {
        $sql = "CREATE TABLE `$this->flight_management_payment_info_table` (`id` INT(11) NULL AUTO_INCREMENT, `flight_id` INT(11) NOT NULL, `card_number` VARCHAR(50) NULL, `card_holder_name` VARCHAR(50) NULL, `card_holder_document_number` VARCHAR(50) NULL, `card_holder_email` VARCHAR(50) NOT NULL,`reference` VARCHAR(50) NOT NULL, `currency` VARCHAR(5) NULL, `type` VARCHAR(100), `date` DATE, PRIMARY KEY (`id`)) $this->charset_collate;";
        dbDelta($sql);
    }

    private function createFlightManagementAirportsTable () {
        $sql = "CREATE TABLE `$this->flight_management_airports_table` (`id` INT(11) NULL AUTO_INCREMENT, `code` VARCHAR(5) NOT NULL, `name` VARCHAR(255) NOT NULL, `city_name` VARCHAR(255) NOT NULL, `country_name` VARCHAR(255) NOT NULL, PRIMARY KEY (`id`)) $this->charset_collate;";
        dbDelta($sql);
    }

    public function initDB() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $this->createFlightManagementTable();
        $this->createFlightManagementTaxesTable();
        $this->createFlightManagementSegmentsTable();
        $this->createFlightManagementPassengersTable();
        $this->createFlightManagementContactsTable();
        $this->createFlightManagementPaymentInfo();
        $this->createFlightManagementAirportsTable();
    }
}