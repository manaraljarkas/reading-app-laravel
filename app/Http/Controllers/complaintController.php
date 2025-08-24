<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Http\Requests\AddComplaintRequest;
use Illuminate\Support\Facades\Auth;

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

    public function createComplaint(AddComplaintRequest $request)
    {
        $user = Auth::user();
        $reader = $user->reader;

        if (!$reader) {
            return response()->json([
                'message' => 'Reader profile not found.',
            ], 404);
        }

        $complaint = $reader->complaints()->create($request->validated());

        return response()->json([
            'message' => 'Complaint submitted successfully.',
            'complaint' => $complaint
        ], 201);
    }
    public function destroy($compalintId)
    {
        $user = Auth::user();
        $complaint = Complaint::findOrfail($compalintId);
        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'complaint not found'
            ]);
        }
        $complaint->delete();
        return response()->json([
            'success' => true,
            'message' => 'complaint deleted successfuly'
        ]);
    }
}
