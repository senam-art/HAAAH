<?php
require_once __DIR__ . '/../classes/event_class.php';

class EventController {
    private $event;

    public function __construct() {
        $this->event = new Event();
    }

    public function create($postData) {
        // We only grab what we need. The venue details stay in the venue table.
        $data = [
            'title' => $postData['title'],
            'sport' => $postData['sport'],
            'format' => $postData['format'],
            'event_date' => $postData['event_date'],
            'event_time' => $postData['event_time'],
            'venue_id' => $postData['venue_id'], // The foreign key
            'cost_per_player' => $postData['cost_per_player'],
            'min_players' => $postData['min_players']
        ];

        return $this->event->createEvent($data);
    }
}