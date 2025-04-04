<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class MunicipalityController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:municipalities,name',
                'name_fr' => 'required|string|max:255|unique:municipalities,name_fr',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            $municipality = Municipality::create($request->only(['name', 'name_fr']));

            return response()->json(['status' => 'success', 'message' => 'تمت إضافة البلدية بنجاح', 'municipality' => $municipality]);
        } catch (Exception $e) {
            Log::error('خطأ في إضافة البلدية: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء إضافة البلدية.'], 500);
        }
    }


    public function show()
    {
        try {
            $municipalities = Municipality::all();
            return response()->json(['status' => 'success', 'municipalities' => $municipalities]);

        } catch (Exception $e) {
            Log::error('خطأ في عرض البلدية: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء عرض بيانات البلدية.'], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $municipality = Municipality::find($id);

            if (!$municipality) {
                return response()->json(['status' => 'error', 'message' => 'البلدية غير موجودة'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255|unique:municipalities,name,' . $id,
                'name_fr' => 'sometimes|string|max:255|unique:municipalities,name_fr,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            $municipality->update($request->only(['name', 'name_fr']));

            return response()->json(['status' => 'success', 'message' => 'تم تحديث البلدية بنجاح', 'municipality' => $municipality]);
        } catch (Exception $e) {
            Log::error('خطأ في تحديث البلدية: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء تحديث البلدية.'], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $municipality = Municipality::find($id);

            if (!$municipality) {
                return response()->json(['status' => 'error', 'message' => 'البلدية غير موجودة'], 404);
            }

            $municipality->delete();

            return response()->json(['status' => 'success', 'message' => 'تم حذف البلدية بنجاح']);
        } catch (Exception $e) {
            Log::error('خطأ في حذف البلدية: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حذف البلدية.'], 500);
        }
    }
}
