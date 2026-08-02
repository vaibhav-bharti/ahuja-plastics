<?php

namespace App\Services;

use Carbon\Carbon;

class ProductionCalculator
{
    /**
     * Calculate shift duration in hours.
     */
    public static function shiftHours(string $start, string $end): float
    {
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);

        if ($endTime->lessThan($startTime)) {
            $endTime->addDay();
        }

        return $startTime->diffInMinutes($endTime) / 60;
    }

    /**
     * Planned Quantity (PCS)
     */
    public static function plannedQuantity(
        int $cycleTime,
        int $cavity,
        string $shiftStart,
        string $shiftEnd
    ): int {

        $hours = self::shiftHours($shiftStart, $shiftEnd);

        $seconds = $hours * 3600;

        $shots = floor($seconds / $cycleTime);

        return (int) ($shots * $cavity);
    }

    /**
     * Predicted Counter (Machine Shots)
     */
    public static function predictedCounter(
        int $cycleTime,
        string $shiftStart,
        string $shiftEnd
    ): int {

        $hours = self::shiftHours($shiftStart, $shiftEnd);

        $seconds = $hours * 3600;

        return (int) floor($seconds / $cycleTime);
    }

    /**
     * Actual Production (PCS)
     */
    public static function actualProduction(
        int $actualCounter,
        int $cavity
    ): int {

        return $actualCounter * $cavity;
    }

    /**
     * Production Difference (PCS)
     */
    public static function productionDifference(
        int $plannedQuantity,
        int $actualProduction
    ): int {

        return $actualProduction - $plannedQuantity;
    }

    /**
     * Downtime Minutes
     */
    public static function downtimeMinutes(
        string $startTime,
        string $endTime
    ): int {

        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        if ($end->lessThan($start)) {
            $end->addDay();
        }

        return $start->diffInMinutes($end);
    }

    /**
     * Running Minutes
     */
    public static function runningMinutes(
        int $shiftMinutes,
        int $downtimeMinutes
    ): int {

        return max(0, $shiftMinutes - $downtimeMinutes);
    }
}