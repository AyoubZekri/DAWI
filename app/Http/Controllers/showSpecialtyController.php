<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\specialties;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class showSpecialtyController extends Controller
{
    public function index()
    {
        $specialties = specialties::all()->map(function ($specialty) {
            $specialty->specialy_img = $specialty->specialy_img
                ? asset('storage/' . $specialty->specialy_img)
                : null;
            return $specialty;
        });

        return response()->json([
            'status' => 'success',
            'specialties' => $specialties,
        ]);
    }
}
