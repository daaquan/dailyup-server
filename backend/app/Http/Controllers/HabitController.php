<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function index()
    {
        return Habit::all();
    }

    public function store(Request $request)
    {
        $habit = Habit::create([
            'title' => $request->get('title'),
            'checked' => false,
        ]);

        return response()->json($habit, 201);
    }

    public function destroy(Habit $habit)
    {
        $habit->delete();
        return response()->json(null, 204);
    }

    public function toggle(Habit $habit)
    {
        $habit->checked = !$habit->checked;
        $habit->save();
        return $habit;
    }
}
