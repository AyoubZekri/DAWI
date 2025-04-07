<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\specialties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SpecialtyController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:specialties,name',
            'name_fr' => 'required|string|max:255|unique:specialties,name_fr',
            'specialy_img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $imagePath = null;


            if ($request->hasFile('specialy_img')) {
                $imagePath = $request->file('specialy_img')->store('specialties_images', 'public');
            }


            $specialty = specialties::create([
                'name' => $request->name,
                'name_fr' => $request->name_fr,
                'specialy_img' => $imagePath,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إضافة التخصص بنجاح',
                'specialty' => $specialty,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء إضافة التخصص',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $specialty = specialties::find($id);

        if (!$specialty) {
            return response()->json(['message' => 'التخصص غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:specialties,name,' . $id,
            'name_fr' => 'sometimes|string|max:255|unique:specialties,name_fr,' . $id,
            'specialy_img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if ($request->hasFile('specialy_img')) {
                if ($specialty->specialy_img) {
                    Storage::disk('public')->delete($specialty->specialy_img);
                }
                $specialty->specialy_img = $request->file('specialy_img')->store('specialties_images', 'public');
            }

            if ($request->filled('name')) {
                $specialty->name = $request->input('name');
            }

            if ($request->filled('name_fr')) {
                $specialty->name_fr = $request->input('name_fr');
            }

            $specialty->save();
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث التخصص بنجاح',
                'specialty' => $specialty,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تحديث التخصص',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        $specialty = specialties::find($id);

        if (!$specialty) {
            return response()->json(['message' => 'التخصص غير موجود'], 404);
        }

        try {
            if ($specialty->specialy_img) {
                Storage::disk('public')->delete($specialty->specialy_img);
            }

            $specialty->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف التخصص بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء حذف التخصص',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
