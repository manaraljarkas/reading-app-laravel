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

    public function createComplaint(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $reader = Auth::user()->reader;

        $complaint = Complaint::create([
            'subject'     => $request->subject,
            'description' => $request->description,
            'reader_id'   => $reader->id,
        ]);

        return response()->json([
            'message' => 'Complaint created successfully',
            'complaint' => $complaint,
        ], 201);
    }
}
