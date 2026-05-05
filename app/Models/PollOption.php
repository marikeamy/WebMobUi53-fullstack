<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollOption extends Model
{
    //Latavel a une protection contre les attaques de mass assignment -> qqun qui envoit des données malveillantes
    //pour remplir des champs qu'il devrait pas pouvoir modifier
    //Ici, on dit "seul label peut être rempli de cette façon" avec fillable.
    protected $fillable = ['label'];

    /**
     * Get the poll that owns the option.
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the votes for this option.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }
}
