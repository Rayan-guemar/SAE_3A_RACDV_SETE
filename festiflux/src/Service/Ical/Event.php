<?php

namespace App\Service\Ical;

use App\Entity\Tache;
use DateTimeZone;

class Event {
    private string $uid;
    private string $title;
    private ?string $description;
    private \DateTime $start;
    private \DateTime $end;
    private array $duration; // array of the form [days, hours, minutes]
    private ?string $location;
    const DT_FORMAT = 'Ymd\THis\Z';

    public function __construct(string $uid, string $title, ?string $description, \DateTime $start, \DateTime $end, ?string $location) {
        $this->uid = $this->escape_string($uid);
        $this->title = $this->escape_string($title);
        $this->description = $this->escape_string($description);
        $this->start = $start;
        $this->end = $end;
        $this->location = $this->escape_string($location);
    }
    private function escape_string($str) {
        return preg_replace('/([\,;])/','\\\$1', $str);
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

    public function toVEvent(): string {
        $str = "\r\n".'BEGIN:VEVENT';
        $str .= "\r\n".'DTSTAMP;TZID=Europe/Paris:' . $this->start->format(self::DT_FORMAT) ;
        $str .= "\r\n".'DTSTART;TZID=Europe/Paris:' . $this->start->format(self::DT_FORMAT) ;
        $str .= "\r\n".'DTEND;TZID=Europe/Paris:' . $this->end->format(self::DT_FORMAT) ;
        $str .= "\r\n".'SUMMARY:' . $this->title ;
        if ($this->location) {
            $str .= "\r\n".'LOCATION:' . $this->location;
        }else{
            $str .= "\r\n".'LOCATION:';
        }
        if ($this->description) {
            $str .= "\r\n".'DESCRIPTION:' . $this->description;
        }else{
            $str .= "\r\n".'DESCRIPTION:';
        }
        $str .= "\r\n".'UID:' . $this->uid ;

        $str .= "\r\n".'END:VEVENT';
        return $str;
    }


    public static function fromTache(Tache $tache) {
        $uid = $tache->getId();
        $title = $tache->getNom();
        $description = $tache->getDescription();
        $start = $tache->getCrenaux()->getDateDebut();
        $end = $tache->getCrenaux()->getDateDebut();
        $location = $tache->getLieu();
        return new Event($uid, $title, $description, $start, $end, $location);
    }
}
