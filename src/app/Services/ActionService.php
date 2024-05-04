<?php

namespace App\Services;

use App\Models\Action;
use App\Models\Request;

class ActionService
{
    public function getActionOfDocuments($documentIds)
    {
        return Action::getByDocuments($documentIds)->latest()->get();
    }
}
