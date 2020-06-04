<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportIdentifierResource;
use App\User;
use Illuminate\Http\Request;

class UserReportsRelationshipController extends Controller
{
    public function index(User $user)
    {
        return ReportIdentifierResource::collection($user->reports);
    }
}
