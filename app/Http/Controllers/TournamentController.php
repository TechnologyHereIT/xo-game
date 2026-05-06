<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TournamentController extends Controller
{
    // TournamentController.php
    public function index() {
        $tournaments = \App\Models\Tournament::withCount('participants')->latest()->get();
        return view('tournaments.index', compact('tournaments'));
    }
    public function show($id) {
        $tournament = \App\Models\Tournament::with('participants.user')->findOrFail($id);
        return view('tournaments.show', compact('tournament'));
    }
}
