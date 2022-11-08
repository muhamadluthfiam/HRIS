<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\Responsibility;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;

class ResponsibilityController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $responsibilityQuery = Responsibility::query();

        // hris.com/api/company?id=1
        if($id) {
            
            $responsibility = $responsibilityQuery->find($id);

            if($responsibility){
                return ResponseFormatter::success($responsibility);
            }

            return ResponseFormatter::error('Responsibility not found');
        }

        $responsibilities = $responsibilityQuery->where('role_id', $request->role_id);

        if($name) {
            $responsibilities->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'Responsibilities Found'
        );
    }

    public function create(CreateResponsibilityRequest $request)
    {
        try {

            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id
            ]);

            if(!$responsibility) {
                throw new Exception('Responsibility not created');
            }

            return ResponseFormatter::success($responsibility, 'Responsibility created');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }

    }

    public function destroy($id) {
        try {
            $responsibility = Responsibility::find($id);

            if(!$responsibility) {
                throw new Exception('Responsibility not Found');
            }

            $responsibility->delete();

            return ResponseFormatter::success('Responsibility deletes');

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
