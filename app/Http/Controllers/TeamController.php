<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataLayer;

class TeamController extends Controller
{
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'leagueId' => ['nullable', 'int', 'max:100'],
            'logo' => ['required', 'image', 'mimes:png', 'max:2048'],
        ]);

        $dl = new DataLayer();
        // NOTE: pass the UploadedFile (or null) to the model layer
        $league = $dl->addTeam($validated['name'], $validated['leagueId'],$request->file('logo'));

        return response()->json([
            'success' => (bool) $league,
            'id' => $league?->id,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dl = new DataLayer();
        $team = $dl->getTeamById($id);
        return response()->json($team);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:png', 'max:2048'],
        ]);

        $dl = new DataLayer();
        $success = $dl->updateTeam($id, $validated['name'] ?? null, $request->file('logo'));

        return response()->json([
            'success' => $success,
            'redirect_url' => route('manage.database')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dl = new DataLayer();
        $result = $dl->deleteTeam($id);
        return response()->json([
            'success' => $result,
            'redirect_url' => route('manage.database'),
        ]);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('input');

        $dl = new DataLayer();
        $list = $dl->searchTeams($searchTerm);
        return response()->json($list);
    }
}
