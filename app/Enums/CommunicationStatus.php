<?php

namespace App\Enums;

enum CommunicationStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case SCHEDULED = 'scheduled';
    
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Borrador',
            self::SENT => 'Enviado',
            self::SCHEDULED => 'Programado',
        };
    }
} 