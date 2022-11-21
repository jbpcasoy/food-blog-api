<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $domain
     * @return \Illuminate\Http\Response
     */
    public function show($domain)
    {
        $tenant = Tenant::find($domain);
        if (!$tenant) {
            return response(["success" => false, "data" => null, "errorMessage" => "Tenant not found."], 404);
        }

        return $tenant;
    }
}
