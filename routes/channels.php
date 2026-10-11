<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public channel: the draw must be watchable on an unauthenticated projector/TV
// screen (/draw/live/{competition}), so this is intentionally not gated by an
// authorization callback the way a private/presence channel would be.
Broadcast::channel('draw.{competitionId}', fn () => true);
