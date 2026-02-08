<?php

namespace App\Enum;

enum LocationTypeEnum: string
{
    case Country = 'country';   
    case Region = 'region';     
    case Province = 'province'; 
    case Attraction = 'attraction'; 
    public function isCountry(): bool
    {
        return $this === self::Country;
    }
    public function isRegion(): bool
    {
        return $this === self::Region;
    }
    public function isProvince(): bool
    {
        return $this === self::Province;
    }
    public function isAttraction(): bool
    {
        return $this === self::Attraction;
    }
}