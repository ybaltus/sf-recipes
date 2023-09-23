<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('min_to_hour', [$this, 'minutesToHour']),
        ];
    }

    public function minutesToHour(float $value): string
    {
        if($value < 60 || !$value) {
            return $value;
        }

        $hours = floor($value / 60);
        $minutes = $value % 60;

        if($minutes < 10){
            $minutes = '0'.$minutes;
        }

        return sprintf('%sh%s', $hours, $minutes);
    }
}