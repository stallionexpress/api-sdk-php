<?php

namespace MyParcelCom\Sdk\Resources\Interfaces;

use DateTime;

interface OpeningHourInterface extends JsonInterface
{
    /**
     * @param string $day
     * @return $this
     */
    public function setDay($day);

    /**
     * @return string
     */
    public function getDay();

    /**
     * @param DateTime $open
     * @return $this
     */
    public function setOpen(DateTime $open);

    /**
     * @return DateTime
     */
    public function getOpen();

    /**
     * @param DateTime $closed
     * @return $this
     */
    public function setClosed(DateTime $closed);

    /**
     * @return DateTime
     */
    public function getClosed();
}
