<?php

declare(strict_types=1);

namespace App\Domain\Competition\Enums;

enum DrawMode: string
{
    case Random = 'random';
    case AdminSelected = 'admin_selected';
}
