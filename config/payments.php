<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Demo payment controls
    |--------------------------------------------------------------------------
    |
    | The original application contains a simulated wallet top-up and VNPay
    | success action. They must be explicitly enabled for local demos and must
    | stay disabled in production until a signed gateway callback is wired.
    |
    */
    'demo_enabled' => (bool) env('PAYMENTS_DEMO_ENABLED', false),
];
