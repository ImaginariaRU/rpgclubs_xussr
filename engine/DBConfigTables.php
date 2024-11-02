<?php

namespace RPGCAtlas;

final class DBConfigTables
{
    public string $visitlog;

    public string $poi;

    public string $poi_types;

    public string $tickets;

    public function __construct()
    {
        $this->poi = 'poi';
        $this->poi_types = 'poi_types';
        $this->tickets = 'tickets';
        $this->visitlog = 'visitlog';
    }

}