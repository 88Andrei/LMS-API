<?php

namespace App\Enums;

enum QuestionType: string
{
    case MULTIPLE = 'multiple';
    case SINGLE = 'single';
    case OPEN = 'open';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
