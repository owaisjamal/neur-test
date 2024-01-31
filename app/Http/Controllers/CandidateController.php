<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\Mail;

class CandidateController extends Controller
{
    public function index()
    {
        $candidates = Candidate::all();
        $wallet = Wallet::where('company_id', 1)->first();
        $coins = $wallet->coins ?? 0;
        $desiredStrengths = ['PHP', 'Laravel', 'Vue.js', 'TailwindCSS']; // Add your desired strengths
        $desiredSoftSkills = ['Diplomacy', 'Team player', 'Leadership']; // Add your desired soft skills
        $mvpCandidates = Candidate::whereIn('id', [1, 3])->get();

        return view('candidates.index', compact('candidates', 'coins', 'desiredStrengths', 'desiredSoftSkills', 'mvpCandidates'));
    }
    public function contact($candidateId)
    {
        try {
            $wallet = Wallet::where('company_id', 1)->first();
            $candidate = Candidate::find($candidateId);


            if (!$candidate) {
                return response()->json(['message' => 'Unable to contact candidate.'], 400);
            }

            if ($wallet->coins < 5) {
                return response()->json(['message' => 'Please add more coins to contact candidate.', 'coins' => $wallet->coins], 403);
            }
            // Send email to the candidate (replace 'your-email-template' with the actual email template)
            Mail::to($candidate->email)->send(new \App\Mail\ContactCandidate($candidate));

            // Mark the candidate as hired
            $candidate->update(['contacted' => true]);
            // Charge the company 5 coins
            $wallet->decrement('coins', 5);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 403);
        }

        // Optionally, you might want to log the contact action or perform other actions
        return response()->json(['message' => 'Candidate contacted successfully.', 'coins' => $wallet->coins]);
    }

    public function hire($candidateId)
    {
        try {
            $wallet = Wallet::where('company_id', 1)->first();
            $candidate = Candidate::find($candidateId);
            if (!$candidate->contacted) {
                return response()->json(['message' => 'Contact a candidate first.'], 403);
            }

            // Send email to the candidate (replace 'your-email-template' with the actual email template)
            Mail::to($candidate->email)->send(new \App\Mail\HireCandidate($candidate));

            // Mark the candidate as hired
            $candidate->update(['hired' => true]);

            // Put back 5 coins in the company's wallet
            $wallet->increment('coins', 5);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 403);
        }

        // Optionally, you might want to log the hire action or perform other actions
        return response()->json(['message' => 'Candidate hired successfully.', 'coins' => $wallet->coins]);
    }
}
