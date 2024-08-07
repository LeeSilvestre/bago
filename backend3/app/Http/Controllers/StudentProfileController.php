<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentProfile;

class StudentProfileController extends Controller
{
    public function allStudents()
    {
        return response()->json(StudentProfile::all(), 200);
    }

    public function showAStudent($id)
    {
        $student = StudentProfile::find($id);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
        return response()->json($student, 200);
    }

    public function createStudentP(Request $request)
    {
        $student = StudentProfile::create($request->all());
        return response()->json($student, 201);
    }

    public function updateStudentP(Request $request, $id)
    {
        $student = StudentProfile::find($id);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
        $student->update($request->all());
        return response()->json(['message' => 'Student updated successfully'], 200);
    }

    public function deleteStudentP($id)
    {
        $student = StudentProfile::find($id);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully'], 200);
    }
}