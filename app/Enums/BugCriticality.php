<?php

namespace App\Enums;

enum BugCriticality: string
{
    case LOW = "low";
    case MEDIUM = "medium";
    case HIGH = "high";
    case BLOCKER = "blocker";
    case CRITICAL = "critical";
}
