<?php

namespace App\Service;

class Slugger
{
    // hyphen = tiret
    private $use_hyphen;

    public function __construct($use_hyphen)
    {
        $this->use_hyphen = $use_hyphen;
    }

    public function slugify($strToConvert)
    {
        $separator = $this->use_hyphen ? '-' : '';

        $chaine = preg_replace('/[^a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*/', $separator, strtolower(trim(strip_tags($strToConvert))));

        return substr($chaine, 0, 50);
    }
}