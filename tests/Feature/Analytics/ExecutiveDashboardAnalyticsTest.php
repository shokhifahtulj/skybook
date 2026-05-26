<?php

use App\Services\Analytics\ExecutiveDashboardService;

test('executive dashboard analytics loads without sql errors', function () {
    $snapshot = app(ExecutiveDashboardService::class)->getDailySnapshot();

    expect($snapshot)->toHaveKeys(['revenue', 'operations', 'passenger', 'security_alerts'])
        ->and($snapshot['revenue'])->toHaveKeys([
            'total_revenue',
            'flight_revenue',
            'ancillary_revenue',
            'revenue_per_passenger',
            'revenue_per_flight',
        ])
        ->and($snapshot['passenger'])->toHaveKeys([
            'checkin_conversion',
            'ancillary_attachment_rate',
            'priority_boarding_adoption',
            'average_baggage_purchase',
        ]);
});
