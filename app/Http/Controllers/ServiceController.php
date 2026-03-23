<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return Service::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'duration'=>'required|integer',
            'price'=>'required|numeric'
        ]);
        return Service::create($request->all());
    }
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->update($request->all());
        return $service;
    }
}
