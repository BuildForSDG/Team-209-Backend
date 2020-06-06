<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportsAttachment extends Model
{
    protected $guarded = ["id"];
    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function report()
    {
        return $this->belongsTo('App\Report', 'report_id');
    }

    public static function uploadAttachment($file, $type, $reportId)
    {
        $fileName = Str::random() . '.' . $file->clientExtension();
        $file->storeAs("public/$type/uploads/attachments/$reportId", $fileName, ["visibility" => "public"]);
        return $fileName;
    }

    public function getFileAttribute($value)
    {
        return Storage::url("public/$this->type/uploads/attachments/$this->report_id/$value");
    }
}
