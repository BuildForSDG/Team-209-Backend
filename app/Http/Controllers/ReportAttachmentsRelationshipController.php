<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportAttachmentIdentifierResource;
use App\Report;

class ReportAttachmentsRelationshipController extends Controller
{
    public function index(Report $report)
    {
        return ReportAttachmentIdentifierResource::collection($report->attachments);
    }
}
