<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('transaction.{id}', function ($id) {
    return (int) $id;
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});