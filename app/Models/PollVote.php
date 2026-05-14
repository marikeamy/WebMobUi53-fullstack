<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollVote extends Model
{
    //rappel, $fillable protège contre le mass assignment. Sans lui, Laravel refure qu'on passe un tableau de valeurs
    //direct à create().
    protected $fillable = ['poll_id', 'poll_option_id', 'user_id'];

    /**
     * Get the poll that owns the vote.
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the user that cast the vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the option chosen.
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }
}
