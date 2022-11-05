<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\Company;

class CompanyController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // hris.com/api/company?id=1
        if($id)
        {
            $company = Company::with(['users'])->find($id);
            if($company)
            {
                return ResponseFormatter::success($company);
            }

            return ResponseFormatter::error('Company not found');
        }

        $companies = Company::with(['users']);

        if($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Companies Found'
        );
    }
}
