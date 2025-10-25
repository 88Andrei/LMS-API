<?php

namespace App\Enums;

enum FileType: string
{
    case IMAGE = 'image';
    case DOCUMENT = 'document';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case OTHER = 'other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}