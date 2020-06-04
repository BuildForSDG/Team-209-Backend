<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportIdentifierResource;
use App\Incident;
use Illuminate\Http\Request;

class IncidentReportsRelationshipController extends Controller
{
    public function index(Incident $incident)
    {
        return ReportIdentifierResource::collection($incident->reports);
    }
}
