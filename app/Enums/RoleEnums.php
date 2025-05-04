<?php

namespace App\Enums;

enum RoleEnums: string
{
    case SuperAdmin = 'super_admin';
    case ADMIN = 'admin';
    case AGENT = 'agent';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::SuperAdmin => 'super_admin',
            self::AGENT => 'Agent',
        };
    }

    public static function superAdminValue(): string
    {
        return self::SuperAdmin->value;
    }
}
