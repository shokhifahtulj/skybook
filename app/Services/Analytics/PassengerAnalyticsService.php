<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\BookingSegmentPassenger;
use App\Models\BookingPassengerAncillary;

class PassengerAnalyticsService
{
    /**
     * Get snapshot of passenger and ancillary conversion metrics
     */
    public function getSnapshot(): array
    {
        return Cache::remember('analytics_passenger_snapshot', now()->addMinutes(15), function () {
            $totalPassengers = BookingSegmentPassenger::count();
            
            if ($totalPassengers === 0) {
                return [
                    'checkin_conversion' => 0,
                    'ancillary_attachment_rate' => 0,
                    'priority_boarding_adoption' => 0,
                    'average_baggage_purchase' => 0,
                ];
            }

            $checkedInPassengers = BookingSegmentPassenger::whereIn('operational_status', ['checked_in', 'boarded'])->count();
            $checkinConversion = round(($checkedInPassengers / $totalPassengers) * 100, 1);

            $passengersWithAncillaries = BookingSegmentPassenger::has('ancillaries')->count();
            $ancillaryAttachmentRate = round(($passengersWithAncillaries / $totalPassengers) * 100, 1);

            $passengersWithPriority = BookingSegmentPassenger::whereHas('ancillaries', function($q) {
                $q->where('type', 'priority_boarding');
            })->count();
            $priorityAdoption = round(($passengersWithPriority / $totalPassengers) * 100, 1);

            $totalBaggageRevenue = BookingPassengerAncillary::where('type', 'baggage')
                ->sum('snapshot_price');
            $passengersWithBaggage = BookingSegmentPassenger::whereHas('ancillaries', function($q) {
                $q->where('type', 'baggage');
            })->count();

            $averageBaggagePurchase = $passengersWithBaggage > 0 ? $totalBaggageRevenue / $passengersWithBaggage : 0;

            return [
                'checkin_conversion' => $checkinConversion,
                'ancillary_attachment_rate' => $ancillaryAttachmentRate,
                'priority_boarding_adoption' => $priorityAdoption,
                'average_baggage_purchase' => $averageBaggagePurchase,
            ];
        });
    }
}
