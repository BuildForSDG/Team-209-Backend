<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncident;
use App\Http\Resources\IncidentCollection;
use App\Http\Resources\IncidentResource;
use App\Http\Resources\ReportCollection;
use App\Incident;
use App\Libs\Geocoder;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        /** @phpstan-ignore-next-line */
        $incidents = QueryBuilder::for(Incident::class)->allowedSorts([
            "address",
            "created_at",
            "updated_at"
        ])->allowedIncludes('reports')->jsonPaginate();

        return (new IncidentCollection($incidents))
            ->response()
            ->header("Content-Type", "application/vnd.api+json");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreIncident $request
     * @return JsonResponse
     */
    public function store(StoreIncident $request)
    {
        $lat  = floatval($request->validated()["data"]["attributes"]["latitude"]);
        $long = floatval($request->validated()["data"]["attributes"]["longitude"]);

        $incident = new Incident;
        $incident->location = new Point($lat, $long);

        $geocoderResponse = (new Geocoder)->reverse($lat, $long);

        $incident->postcode = $geocoderResponse["address"]["postcode"];
        $incident->address = $geocoderResponse["display_name"];
        $incident->area = Incident::createGeoPolygon($geocoderResponse["geojson"]["coordinates"]);
        $incident->save();

        return (new IncidentResource($incident->refresh()))
            ->response()
            ->header("Content-Type", "application/vnd.api+json")
            ->header("Location", route("incidents.show", ["incident" => $incident]));
    }

    /**
     * Display the specified resource.
     *
     * @param Incident $incident
     * @return JsonResponse
     */
    public function show($incident)
    {
        $query = QueryBuilder::for(Incident::where('id', $incident))
            ->allowedIncludes('reports')
            ->firstOrFail();

        return (new IncidentResource($query))
            ->response()
            ->header("Content-Type", "application/vnd.api+json");
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Incident $incedent
     * @return Response
     */
//    public function update(Request $request, Incident $incedent)
//    {
//        //
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Incident $incident
     * @return Response
     * @throws \Exception
     */
    public function destroy(Incident $incident)
    {
        $incident->delete();
        return response(null, 204);
    }

    public function relatedReports(Incident $incident)
    {
        return new ReportCollection($incident->reports);
    }
}
