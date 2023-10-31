<?php

namespace App\Service\Ical;

use App\Entity\Tache;

class Event {
    private string $uid;
    private string $title;
    private ?string $description;
    private \DateTime $start;
    private \DateTime $end;
    private array $duration; // array of the form [days, hours, minutes]
    private ?string $location;
    private ?int $nbrBenevoles;

    public function __construct(string $uid, string $title, ?string $description, \DateTime $start, \DateTime $end, ?string $location, ?int $nbrBenevoles) {
        $this->uid = $uid;
        $this->title = $title;
        $this->description = $description;
        $this->start = $start;
        $this->end = $end;
        $this->location = $location;
        $this->nbrBenevoles = $nbrBenevoles;
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

    /**
     * @return int|null
     */
    public function getNbrBenevoles(): ?int
    {
        return $this->nbrBenevoles;
    }


    public function toVEvent(): string {

        $str = "BEGIN:VEVENT\r\n";
        $str .= "UID:" . $this->uid . "\r\n";
        $str .= "DTSTAMP:" . $this->start->format('Ymd\THis\Z') . "\r\n";
        $str .= "DTSTART;TZID=Europe/Paris:" . $this->start->format('Ymd\THis\Z') . "\r\n";
        $str .= "DTEND;TZID=Europe/Paris:" . $this->end->format('Ymd\THis\Z') . "\r\n";
        $str .= "SUMMARY:" . $this->title . "\n";
        if ($this->description) {
            $str .= "DESCRIPTION:" . $this->description ;
            if ($this->nbrBenevoles){
                $str .= "NOMBRE BENEVOLES:" . $this->nbrBenevoles . "\r\n";
            }else{
                $str.="\r\n";
            }
        }
        if ($this->location) {
            $str .= "LOCATION:" . $this->location . "\r\n";
        }

        $str .= "END:VEVENT\r\n";
        return $str;
    }

    public static function fromTache(Tache $tache) {
        $uid = $tache->getId();
        $title = $tache->getNom();
        $description = $tache->getDescription();
        $start = $tache->getCrenaux()->getDateDebut();
        $end = $tache->getCrenaux()->getDateDebut();
        $location = $tache->getLieu();
        $nbrBenevoles = $tache->getNombreBenevole();
        return new Event($uid, $title, $description, $start, $end, $location, $nbrBenevoles);
    }
}
