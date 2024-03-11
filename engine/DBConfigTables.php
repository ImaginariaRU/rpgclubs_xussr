<?php

namespace RPGCAtlas;

final class DBConfigTables
{
    public string $clubs;
    public string $visitlog;

    public function __construct()
    {
        $this->clubs = 'rpgcrf_clubs';
        $this->visitlog = 'rpgcrf_clubs_visitlog';
    }

}