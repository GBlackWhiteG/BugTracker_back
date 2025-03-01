<?php

namespace App\Enums;

enum BugStatus: string
{
    case NEW = "new";
    case WORK = "work";
    case TEST = "test";
    case CLOSED = "closed";
}
