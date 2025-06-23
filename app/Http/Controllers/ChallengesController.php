<?php

namespace App\Http\Controllers;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengesController extends Controller
{
   public function index(){

    $readerId = Auth::id();
    
     $challenges = Challenge::select('challenges.id','challenges.title', 'description', 'points', 'challenges.created_at', 'duration','reader_challenges.percentage')
     ->join('reader_challenges','challenges.id','=','reader_challenges.challenge_id')->
     where('reader_challenges.reader_id','=',$readerId)->get();

     $now = now();

     $challenges = $challenges->map(function ($challenge) use ($readerId, $now) {
      $startDate = Carbon::parse($challenge->created_at);
      $endDate = $startDate->copy()->addDays($challenge->duration);
      $now = Carbon::now();
      $timeLeft = $now->diffInDays($endDate, false);

      $timeLeft = $timeLeft > 0 ? intval($timeLeft) : 0;



         return [
            'id' => $challenge->id,
            'title' => $challenge->title,
            'description' => $challenge->description,
            'points' => $challenge->points,
            'time_left' => $timeLeft,
            'percentage' => $challenge->percentage,
            ];
        });

        return response()->json($challenges);
   }
}
