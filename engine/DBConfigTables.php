<?php

namespace RPGCAtlas;

final class DBConfigTables
{
    public string $clubs;
    public string $visitlog;

    public string $poi;

    public string $poi_types;

    public function __construct()
    {
        $this->poi = 'poi';
        $this->poi_types = 'poi_types';
        // $this->visitlog = 'rpgcrf_clubs_visitlog';
    }

}