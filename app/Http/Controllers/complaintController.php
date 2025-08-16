<?php

namespace App\Http\Controllers;

use \Exception;
use App\Models\Complaint;
use Illuminate\Http\Request;
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

    public function store(\App\Http\Requests\AddComplaintRequest $request)
    {
        $user = Auth::user();
        // $complaint = Complaint::create([
        //     'subject' => $request->subject,
        //     'description' => $request->description,
        //     'reader_id' => $user->reader->id
        // ]);
        // return response()->json([
        //     'message' => 'Thank you! Your Complaint has been submitted successfully.'
        // ]);
        try {
            $complaint = Complaint::create([
                'subject' => $request->subject,
                'description' => $request->description,
                'reader_id' => $user->reader->id
            ]);
            return response()->json([
                'message' => 'Thank you! Your Complaint has been submitted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
