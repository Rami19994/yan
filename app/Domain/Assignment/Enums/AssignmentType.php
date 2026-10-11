<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Enums;

enum AssignmentType: string
{
    case AdminToMaster = 'admin_to_master';
    case MasterToCashier = 'master_to_cashier';
}
