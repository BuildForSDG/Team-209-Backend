<?php

namespace App;

use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class Incident extends Model
{
    use SpatialTrait;

    protected $guarded = ["id"];
    protected $dateFormat = 'Y-m-d H:i:s.u';
    protected $dates = [
        "deactivated"
    ];

    protected $spatialFields = [
        'location',
        'area'
    ];

    public static function createGeoPolygon(array $LineStringCoordinates)
    {
        $pointsArray = [];

        foreach ($LineStringCoordinates as $lineStringCoordinate) {
            $pointsArray[] = new Point($lineStringCoordinate[1], $lineStringCoordinate[0]);
        }
        $pointsArray[] = new Point($LineStringCoordinates[0][1], $LineStringCoordinates[0][0]);

        return new Polygon([new LineString($pointsArray)]);
    }
}
