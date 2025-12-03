<?php
// controllers/event_controller.php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/classes/event_class.php';

/**
 * Controller function to create a new event.
 * Instantiates the Event model and calls the creation method.
 * * @param array $data Array containing sanitized event details
 * @return array Result from the model ['success' => bool, 'event_id' => int, ...]
 */
function create_event_ctr($data) {
    // Instantiate the Event Model class
    $eventModel = new Event();
    
    // Call the create method in the class
    return $eventModel->createEvent($data);
}

/**
 * Controller function to get event details.
 * Useful for the payment/checkout flow.
 * * @param int $event_id
 * @return array|false Event details or false if not found
 */
function get_event_details_ctr($event_id) {
    $eventModel = new Event();
    return $eventModel->getEventById($event_id);
}

// Add other functional wrappers here as needed (e.g., delete_event_ctr, update_event_ctr)
?>