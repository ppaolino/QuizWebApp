<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataLayer;
use Illuminate\Support\Facades\Log;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('input');

        $dl = new DataLayer();
        $list = $dl->search($searchTerm);
        return response()->json($list);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dl = new DataLayer();
        $id = $dl->addPlayer($request->input('name'), $request->input('position'));
        return response()->json([
            'success' => $id != null,
            'redirect_url' => route('manage.database'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dl = new DataLayer();
        $player = $dl->getPlayerById($id);
        return response()->json($player);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dl = new DataLayer();
        $del = $dl->updatePlayer($request->id, $request->input('name'), $request->input('position'));
        return response()->json([
            'success' => $del,
            'redirect_url' => route('manage.database')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dl = new DataLayer();
        $result = $dl->deletePlayer($id);
        return response()->json([
            'success' => $result,
            'redirect_url' => route('manage.database'),
        ]);
    }
}



