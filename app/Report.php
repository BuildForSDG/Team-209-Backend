<?php

namespace App;

use App\Libs\Geocoder;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use SpatialTrait;

    protected $guarded = ["id"];
    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $spatialFields = [
        'location',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($report) {

            // Delete Related Attachments
            $attachmentTypes = ["images", "audios"];
            foreach ($attachmentTypes as $type) {
                Storage::deleteDirectory("public/$type/uploads/attachments/$report->id");
            }
        });
    }

    public function incident()
    {
        return $this->belongsTo('App\Incident', 'incident_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function attachments()
    {
        return $this->hasMany('App\ReportsAttachment');
    }

    public function createIncident($latitude, $longitude)
    {
        $incident = new Incident;
        $incident->location = new Point($latitude, $longitude);

        $geocoderResponse = (new Geocoder)->reverse($latitude, $longitude);

        $incident->postcode = $geocoderResponse["address"]["postcode"];
        $incident->address = $geocoderResponse["display_name"];
        $incident->area = Incident::createGeoPolygon($geocoderResponse["geojson"]["coordinates"]);
        $incident->save();

        return $incident->refresh();
    }
}
