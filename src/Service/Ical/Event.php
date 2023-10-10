<?php

namespace App\Service\Ical;

class Event {
    private string $uid;
    private string $title;
    private ?string $description;
    private \DateTime $start;
    private \DateTime $end;
    private array $duration; // array of the form [days, hours, minutes]
    private ?string $location;

    public function __construct(string $uid, string $title, ?string $description, \DateTime $start, \DateTime $end, ?string $location) {
        $this->uid = $uid;
        $this->title = $title;
        $this->description = $description;
        $this->start = $start;
        $this->end = $end;
        $this->location = $location;
    }

    public function getUid(): string {
        return $this->uid;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getStart(): \DateTime {
        return $this->start;
    }

    public function getEnd(): \DateTime {
        return $this->end;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function toVEvent() : string {

        $str = "BEGIN:VEVENT\r\n";
        $str .= "UID:" . $this->uid . "\r\n";
        $str .= "DTSTAMP:" . $this->start->format('Ymd\THis\Z') . "\r\n";
        $str .= "DTSTART:" . $this->start->format('Ymd\THis\Z') . "\r\n";
        $str .= "DTEND:" . $this->end->format('Ymd\THis\Z') . "\r\n";
        $str .= "SUMMARY:" . $this->title . "\n";
        if ($this->description) {
            $str .= "DESCRIPTION:" . $this->description . "\r\n";
        }
        if ($this->location) {
            $str .= "LOCATION:" . $this->location . "\r\n";
        }
        $str .= "END:VEVENT\r\n";
        return $str;
    }
}