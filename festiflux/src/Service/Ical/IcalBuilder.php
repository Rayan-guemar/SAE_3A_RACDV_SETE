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
        fwrite($file, 'BEGIN:VCALENDAR');
        fwrite($file, "\r\n".'METHOD:REQUEST');
        fwrite($file, "\r\n".'PRODID:' . $this->provid );
        fwrite($file, "\r\n".'VERSION:2.0');
        fwrite($file, "\r\n".'CALSCALE:GREGORIAN');

        foreach ($this->events as $event) {
            fwrite($file, $event->toVEvent());
        }

        fwrite($file, "\r\n".'END:VCALENDAR');
    }
}