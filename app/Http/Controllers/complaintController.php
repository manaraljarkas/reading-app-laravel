<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Http\Requests\AddComplaintRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;


class ComplaintController extends Controller
{
    public function getComplaints()
    {
        $reader = Auth::user();
        $complaints = Complaint::select('reader_id', 'subject', 'created_at')->with('reader')->get();

        $complaints = $complaints->map(function ($complaint) {
            return [
                'reader_id' => $complaint->reader_id,
                'reader_name' => $complaint->reader?->first_name,
                'complaint' => $complaint->subject,
                'date' => $complaint->created_at,
            ];
        });
        return response()->json($complaints);
    }

    public function createComplaint(AddComplaintRequest $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $reader = $user->reader;
        if (!$reader) {
            return response()->json(['message' => 'Reader profile not found.'], 404);
        }

        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:500',
        ]);

        $data['reader_id'] = $reader->id;

        $complaint = Complaint::create($data);

        return response()->json([
            'message'   => 'Complaint submitted successfully.',
            'complaint' => $complaint,
        ], 201);
    }
}
