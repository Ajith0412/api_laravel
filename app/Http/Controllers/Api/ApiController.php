<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{
    // User Register (POST, formdata)
    public function register(Request $request)
    {

        // data validation
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed"
        ]);

        // User Model
        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        // Response
        return response()->json([
            "status" => true,
            "message" => "User registered successfully"
        ]);
    }

    // User Login (POST, formdata)
    public function login(Request $request)
    {

        // data validation
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // JWTAuth
        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if (!empty($token)) {

            return response()->json([
                "status" => true,
                "message" => "User logged in succcessfully",
                "token" => $token
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "Invalid details"
        ]);
    }

    // User Profile (GET)
    public function profile()
    {

        $userdata = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "data" => $userdata
        ]);
    }

    // To generate refresh token value
    public function refreshToken()
    {

        $newToken = auth()->refresh();

        return response()->json([
            "status" => true,
            "message" => "New access token",
            "token" => $newToken
        ]);
    }

    // User Logout (GET)
    public function logout()
    {

        auth()->logout();

        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }


    public function student_register(Request $request)
    {
        // Data validation
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "roll_number" => "required|unique:students",
            "class" => "required",
            "section" => "required",
            "date_of_birth" => "required|date",
            "address" => "required"
        ]);

        // Create Student Details
        Student::create([
            "name" => $request->name,
            "email" => $request->email,
            "roll_number" => $request->roll_number,
            "class" => $request->class,
            "section" => $request->section,
            "date_of_birth" => $request->date_of_birth,
            "address" => $request->address
        ]);

        // Response
        return response()->json([
            "status" => true,
            "message" => "Student registered successfully"
        ]);
    }

    public function student_list()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Fetch the associated student details using the relationship
        $student = $user->student;

        if ($student) {
            return response()->json([
                "status" => true,
                "message" => "Profile data",
                "data" => $student
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Student data not found for this user"
            ], 404);
        }
    }

    public function edit_student(Request $request, $id)
    {
        // Data validation
        $request->validate([
            "name" => "sometimes|required",
            "email" => "sometimes|required|email|unique:students,email," . $id,
            "roll_number" => "sometimes|required|unique:students,roll_number," . $id,
            "class" => "sometimes|required",
            "section" => "sometimes|required",
            "date_of_birth" => "sometimes|required|date",
            "address" => "sometimes|required"
        ]);

        // Fetch the student details using the student ID
        $student = Student::find($id);

        if ($student) {
            // Update the student details with the new data
            $student->update([
                "name" => $request->name ?? $student->name,
                "email" => $request->email ?? $student->email,
                "roll_number" => $request->roll_number ?? $student->roll_number,
                "class" => $request->class ?? $student->class,
                "section" => $request->section ?? $student->section,
                "date_of_birth" => $request->date_of_birth ?? $student->date_of_birth,
                "address" => $request->address ?? $student->address,
            ]);

            return response()->json([
                "status" => true,
                "message" => "Student profile updated successfully",
                "data" => $student
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Student data not found"
            ], 404);
        }
    }

    public function delete_student($id)
    {
        // Find the student by ID
        $student = Student::find($id);

        if ($student) {
            // Delete the student
            $student->delete();

            return response()->json([
                "status" => true,
                "message" => "Student deleted successfully"
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Student not found"
            ], 404);
        }
    }
}
