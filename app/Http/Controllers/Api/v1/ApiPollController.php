<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ApiPollController extends Controller
{
    /**
     * Display a listing of the authenticated user's polls.
     */
    public function index(Request $request)
    {
        $polls = $request->user()->polls()->with('options')->orderBy('created_at', 'desc')->get();
        return $polls;
    }

    /**
     * Display the specified poll by its secret token.
     */
    public function show(string $token)
    {
        $poll = Poll::with(['options' => function ($query) {
            $query->withCount('votes');
        }])->where('secret_token', $token)->first();

        if (!$poll) {
            return response()->json(['message' => 'Poll not found.'], 404);
        }

        return $poll;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'question' => 'required|string|max:5000',
            'allowMultipleChoices' => 'required|boolean',
            'allowVoteChange' => 'required|boolean',
            'resultsPublic' => 'required|boolean',
            'duration' => 'nullable|integer',
            'is_draft' => 'required|boolean',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string'
        ]);

        $user = $request->user();
        $poll = new Poll();

        $poll->title = $validated['title'];
        $poll->question = $validated['question'];
        $poll->allow_multiple_choices = $validated['allowMultipleChoices'];
        $poll->allow_vote_change = $validated['allowVoteChange'];
        $poll->results_public = $validated['resultsPublic'];
        $poll->duration = $validated['duration'];
        //Str:uuid() génère une chaine aléatoire unique.
        $poll->secret_token = Str::uuid();
        $poll->is_draft = $validated['is_draft'];
        $poll->user()->associate($user);

        $poll->save();

        //Récupérer les versions validées des options et les assigner au poll
        //Fait après le save parce que sinon le poll a pas encore d'id, donc on peut pas lui assigner des options
        foreach($validated['options'] as $option) {
            $poll->options()->create(['label' => $option]);
        }

        if($poll->is_draft === false) {
            $poll->started_at = now();
            $poll->ends_at = (now()->addSeconds($poll->duration));
        }

        $poll->save();
        return $poll;
    }

    /**
     * Remove the specified poll.
     */
    public function remove(Request $request, int $id)
    {
        $poll = Poll::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$poll) {
            return response()->json(['message' => 'Poll not found.'], 404);
        }

        $poll->delete();

        return response()->json(['message' => 'success'], 200);
    }

public function update(Request $request, int $id)
    {
        //get poll
        $poll = Poll::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$poll) {
            return response()->json(['message' => 'Poll not found.'], 404);
        }

        //validate new data
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'question' => 'required|string|max:5000',
            'allowMultipleChoices' => 'required|boolean',
            'allowVoteChange' => 'required|boolean',
            'resultsPublic' => 'required|boolean',
            'duration' => 'nullable|integer',
            'is_draft' => 'required|boolean',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string'
        ]);

        $user = $request->user();

        $poll->title = $validated['title'];
        $poll->question = $validated['question'];
        $poll->allow_multiple_choices = $validated['allowMultipleChoices'];
        $poll->allow_vote_change = $validated['allowVoteChange'];
        $poll->results_public = $validated['resultsPublic'];
        $poll->duration = $validated['duration'];
        $poll->is_draft = $validated['is_draft'];

        $poll->save();

        //on supprime toutes les options avant de les ajouter à nouveau dans la boucle
        //On pourrait vérifier quelles options sont nouvelles pour ne modifier qu'elles mais c'est trop complexe
        $poll->options()->delete();

        //Récupérer les versions validées des options et les assigner au poll
        //Fait après le save parce que sinon le poll a pas encore d'id, donc on peut pas lui assigner des options
        foreach($validated['options'] as $option) {
            $poll->options()->create(['label' => $option]);
        }

        //on set started_art et ends_at à nouveau si is_draft est false
        if($poll->is_draft === false) {
            $poll->started_at = now();
            $poll->ends_at = (now()->addSeconds($poll->duration));
        }

        $poll->save();
        return $poll;
    }
}
