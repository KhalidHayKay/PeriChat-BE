<?php

Route::get('/healthz', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});
