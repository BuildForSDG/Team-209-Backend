<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportAttachments;
use App\Http\Resources\ReportAttachmentCollection;
use App\Http\Resources\ReportAttachmentResource;
use App\ReportsAttachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

class ReportAttachmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        /** @phpstan-ignore-next-line */
        $query = QueryBuilder::for(ReportsAttachment::class)->allowedSorts(
            [
                "created_at",
                "updated_at",
                'type'
            ]
        )->allowedIncludes('report')
            ->jsonPaginate();

        return (new ReportAttachmentCollection($query))
            ->response()
            ->header("Content-Type", "application/vnd.api+json");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreReportAttachments $request
     * @return JsonResponse
     */
    public function store(StoreReportAttachments $request)
    {
        $attachmentTypes = ["images", "audios"];
        $reportId = $request->report_id;

        foreach ($attachmentTypes as $attachmentType) {
            if (!$request->hasfile($attachmentType)) {
                continue;
            }

            foreach ($request->file($attachmentType) as $attachmentItem) {
                $attachment = new ReportsAttachment();

                $attachment->report_id = $reportId;
                $attachment->type = $attachmentType;
                $attachment->file = $attachment->uploadAttachment($attachmentItem, $attachmentType, $reportId);

                $attachment->save();
            }
        }

        $attachments = ReportsAttachment::where("report_id", "=", $reportId)->get();

        return (new ReportAttachmentCollection($attachments))
            ->response()
            ->header("Content-Type", "application/vnd.api+json");
//            ->header("Location", route("reports.attachments", ["report" => $reportId]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ReportsAttachment  $reportsAttachment
     * @return Response
     */
    public function show(ReportsAttachment $reportsAttachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ReportsAttachment  $reportsAttachment
     * @return Response
     */
//    public function edit(ReportsAttachment $reportsAttachment)
//    {
//        //
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ReportsAttachment  $reportsAttachment
     * @return Response
     */
    public function update(Request $request, ReportsAttachment $reportsAttachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ReportsAttachment  $reportsAttachment
     * @return Response
     */
    public function destroy(ReportsAttachment $reportsAttachment)
    {
        //
    }

    public function relatedReports(ReportsAttachment $attachment)
    {
        return new ReportAttachmentResource($attachment->report);
    }
}
