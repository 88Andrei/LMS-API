<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case STUDENT = 'student';

    public static function values(): array
    {
        return array_map(fn (UserRole $role) => $role->value, self::cases());
    }

    public static function labels(): array
    {
        return [
            self::ADMIN->value => 'Administrator',
            self::TEACHER->value => 'Teacher',
            self::STUDENT->value => 'Student',
        ];
    }
}
