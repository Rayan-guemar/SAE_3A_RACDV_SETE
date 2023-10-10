<?php

namespace App\Service\Ical;

class IcalBuilder {
    private array $events;
    private string $filename;
    private string $provid;

    public function __construct(string $filename) {
        $this->events = [];
        $this->filename = $filename;
        $this->provid = '-//Festiflux.org/NONSGML Festiflux Calendar V1.1//EN';
    }

    public function add(Event $event) {
        $this->events[] = $event;
    }

    public function build() {
        $file = fopen('icals/' . $this->filename . '.ics', 'w');
        fwrite($file, "BEGIN:VCALENDAR\n");
        fwrite($file, "VERSION:2.0\n");
        fwrite($file, "CALSCALE:GREGORIAN\n");
        fwrite($file, "PRODID:" . $this->provid . "\n"); 

        foreach ($this->events as $event) {
            fwrite($file, $event->toVEvent() . "\n");
        }
        
        fwrite($file, "END:VCALENDAR\n");
    }
}