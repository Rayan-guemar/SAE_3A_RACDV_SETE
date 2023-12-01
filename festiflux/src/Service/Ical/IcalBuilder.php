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

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }


    public function add(Event $event) {
        $this->events[] = $event;
    }

    public function build() {
        $file = fopen('icals/' . $this->filename . '.ics', 'w');
        fwrite($file, "BEGIN:VCALENDAR\r\n");
        fwrite($file, "VERSION:2.0\r\n");
        fwrite($file, "CALSCALE:GREGORIAN\r\n");
        fwrite($file, "PRODID:" . $this->provid . "\r\n"); 

        foreach ($this->events as $event) {
            fwrite($file, $event->toVEvent() . "\r\n");
        }

        fwrite($file, "END:VCALENDAR\r\n");
    }
}