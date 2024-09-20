<?php

namespace Xbigdaddyx\Beverly\Enums;

enum CartonBoxStatus:string {
    case OPENED  = 'Opened';
    case CLOSED = 'Closed';
    case VALIDATED = 'Validated';
    case TRANSFERED = 'Transfered';

    public function description(): string
    {
        return match($this)
        {
            self::OPENED => 'the carton box has been opened',
            self::CLOSED => 'the carton box has been closed',
            self::VALIDATED => 'the carton box has been validated',
            self::TRANSFERED => 'the carton box has been transfered',
        };
    }
}
