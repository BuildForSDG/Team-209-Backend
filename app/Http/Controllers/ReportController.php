<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReport;
use App\Http\Resources\IncidentResource;
use App\Http\Resources\ReportAttachmentCollection;
use App\Http\Resources\ReportAttachmentResourceCollection;
use App\Http\Resources\ReportCollection;
use App\Http\Resources\ReportResource;
use App\Http\Resources\UserResource;
use App\Incident;
use App\Libs\Geocoder;
use App\Report;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        /** @phpstan-ignore-next-line */
        $reports = QueryBuilder::for(Report::class)->allowedSorts([
            "address",
            "created_at"
        ])->allowedIncludes(["user", 'incident', 'attachments'])
            ->jsonPaginate();

        return (new ReportCollection($reports))
            ->response()
            ->header("Content-Type", "application/vnd.api+json");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreReport $request
     * @return JsonResponse
     */
    public function store(StoreReport $request)
    {
        $lat  = floatval($request->validated()["data"]["attributes"]["latitude"]);
        $long = floatval($request->validated()["data"]["attributes"]["longitude"]);

        $report = new Report;
        $report->description = $request->validated()["data"]["attributes"]["description"];
        $report->user_id = auth()->id();

        $report->location = new Point($lat, $long);
        $geocoderResponse = (new Geocoder)->reverse($lat, $long);

        $report->address = $geocoderResponse["display_name"];
        /** @phpstan-ignore-next-line */
        $incident = Incident::where("postcode", "=", $geocoderResponse["address"]["postcode"])->active()->first();

        if (!$incident) {
            $incident = $report->createIncident($lat, $long);
        }

        $report->incident_id = $incident->id;
        $report->save();

        return (new ReportResource($report->refresh()))
            ->response()
            ->header("Content-Type", "application/vnd.api+json")
            ->header("Location", route("reports.show", ["report" => $report]));
    }

    /**
     * Display the specified resource.
     *
     * @param int $report
     * @return JsonResponse
     */
    public function show($report)
    {
        $query = QueryBuilder::for(Report::where('id', $report))
            ->allowedIncludes(["user", 'incident', 'attachments'])
            ->firstOrFail();

        return (new ReportResource($query))
            ->response()
            ->header("Content-Type", "application/vnd.api+json");
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Report $report
     * @return Response
     */
//    public function update(Request $request, Report $report)
//    {
//        //
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Report $report
     * @return Response
     */
    public function destroy(Report $report)
    {
        $report->delete();
        return response(null, 204);
    }

    public function relatedUser(Report $report)
    {
        return new UserResource($report->user);
    }

    public function relatedIncident(Report $report)
    {
        return new IncidentResource($report->incident);
    }

    public function relatedAttachments(Report $report)
    {
        return new ReportAttachmentCollection($report->attachments);
    }
}
