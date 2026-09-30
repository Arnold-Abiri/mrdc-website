<?php

namespace App\Enums;

enum ScopeType: string
{
    case Global = 'global';
    case Department = 'department';
    case Own = 'own';
}
