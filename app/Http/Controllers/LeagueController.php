<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataLayer;

class LeagueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dl = new DataLayer();
        $list = $dl->getAllLeagues();
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
        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:png', 'max:2048'],
        ]);

        $dl = new DataLayer();
        // NOTE: pass the UploadedFile (or null) to the model layer
        $league = $dl->addLeague($validated['name'], $request->file('logo'));

        return response()->json([
            'success' => (bool) $league,
            'id' => $league?->id,
        ]);
    }
    public function show(string $id)
    {
        $dl = new DataLayer();
        $league = $dl->getLeagueById($id);
        return response()->json($league);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:png', 'max:2048'],
        ]);

        $dl = new DataLayer();
        $success = $dl->updateLeague($id, $validated['name'] ?? null, $request->file('logo'));

        return response()->json([
            'success' => $success,
            'redirect_url' => route('manage.database')
        ]);
    }



    public function destroy(string $id)
    {
        $dl = new DataLayer();
        $result = $dl->deleteLeague($id);
        return response()->json([
            'success' => $result,
            'redirect_url' => route('manage.database'),
        ]);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('input');

        $dl = new DataLayer();
        $list = $dl->searchLeagues($searchTerm);
        return response()->json($list);
    }
}
