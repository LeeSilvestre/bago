<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;

class RentalController extends Controller
{
    public function allRental()
    {
        return response()->json(Rental::all(), 200);
    }

    public function showARental($id)
    {
        $book = Rental::find($id);
        if (!$book) {
            return response()->json(['error' => 'Rental record not found'], 404);
        }
        return response()->json($book, 200);
    }
}
