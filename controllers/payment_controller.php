<?php
require_once __DIR__ . '/guest_controller.php';

/**
 * Calculates payment details based on the transaction type
 */
function get_checkout_details_ctr($event_id, $type) {
    // 1. Fetch Event Data
    $event = get_event_details_ctr($event_id);
    
    if (!$event) {
        return false;
    }

    // 2. Initialize Default Response
    $data = [
        'event' => $event,
        'tx_ref' => "HAAAH-" . uniqid() . "-" . time(),
        'page_title' => '',
        'item_name' => '',
        'item_desc' => '',
        'amount_to_pay' => 0.00,
        'button_text' => ''
    ];

    // 3. Apply Business Logic
    if ($type === 'organizer_fee') {
        // SCENARIO A: Organizer Publishing Event
        // Logic: 20% of Venue Cost (Must match your create_event.js config)
        $venue_cost = floatval($event['venue_cost']);
        $commitment_fee = $venue_cost * 0.20; 

        $data['page_title'] = "Publish Event";
        $data['item_name'] = "Organizer Commitment Fee";
        $data['item_desc'] = "Refundable deposit + commission after game completion.";
        $data['amount_to_pay'] = $commitment_fee;
        $data['button_text'] = "Pay GHS " . number_format($commitment_fee, 2) . " & Publish";

    } else {
        // SCENARIO B: Player Joining Game
        // Logic: Cost per player + 10% Service Fee
        $cost_per_player = floatval($event['cost_per_player']);
        $service_fee = $cost_per_player * 0.10; 
        $total = $cost_per_player + $service_fee;

        $data['page_title'] = "Join Squad";
        $data['item_name'] = "Player Spot";
        $data['item_desc'] = "Reserves 1 slot for " . $event['title'];
        $data['amount_to_pay'] = $total;
        $data['button_text'] = "Pay GHS " . number_format($total, 2) . " & Join";
    }

    return $data;
}
?>