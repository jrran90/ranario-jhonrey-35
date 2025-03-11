<?php

namespace App\Services;

use App\Models\StoreHour;
use Carbon\Carbon;

class StoreService
{
    public function getStoreStatus(): array
    {
        $now = Carbon::now('Asia/Manila');

        $isLunchBreak = $now->between(
            $now->copy()->setTimeFromTimeString('12:00:00'),
            $now->copy()->setTimeFromTimeString('12:44:59')
        );

        $storeHours = StoreHour::query()
            ->where('day_of_week', $now->dayOfWeek)
            ->get();

        $isOpen = false;

        foreach ($storeHours as $hour) {
            if ($hour->is_alternate_saturday) {
                $isEvenWeek = $now->weekOfYear % 2 === 0;
                if (!$isEvenWeek) {
                    continue;
                }
            }

            if ($now->between(
                $now->copy()->setTimeFromTimeString($hour->open_time),
                $now->copy()->setTimeFromTimeString($hour->close_time)->subSecond()
            )) {
                $isOpen = true;
                break;
            }
        }

        if ($isOpen && !$isLunchBreak) {
            return [
                'is_open' => true,
                'next_opening' => null,
            ];
        }

        return [
            'is_open' => false,
            'next_opening' => $this->getNextOpening(),
        ];
    }

    private function getNextOpening($date = null): ?string
    {
        $now = $date
            ? Carbon::parse($date, 'Asia/Manila')->setTimeFromTimeString(Carbon::now('Asia/Manila')->format('H:i:s'))
            : Carbon::now('Asia/Manila');

        $currentDayOfWeek = $now->dayOfWeek;
        $currentTime = $now->format('H:i:s');

        $nextOpening = StoreHour::query()
            ->where('is_open', true)
            ->where(function ($query) use ($currentDayOfWeek, $currentTime) {
                $query
                    ->where('day_of_week', '>', $currentDayOfWeek)
                    ->orWhere(function ($query) use ($currentDayOfWeek, $currentTime) {
                        $query
                            ->where('day_of_week', $currentDayOfWeek)
                            ->where('open_time', '>=', $currentTime);
                    });
            })
            ->orderByRaw("CASE
            WHEN day_of_week >= $currentDayOfWeek THEN day_of_week
            ELSE day_of_week + 7
        END")
            ->orderBy('open_time')
            ->first();

        if (!$nextOpening) {
            return null;
        }

        $nextOpeningDateTime = $now->copy();

        if ($nextOpening->day_of_week === $currentDayOfWeek) {
            $nextOpeningDateTime->setTimeFromTimeString($nextOpening->open_time);
        } else {
            $daysToAdd = ($nextOpening->day_of_week >= $currentDayOfWeek)
                ? $nextOpening->day_of_week - $currentDayOfWeek
                : ($nextOpening->day_of_week - $currentDayOfWeek + 7) % 7;
            $nextOpeningDateTime->addDays($daysToAdd)->setTimeFromTimeString($nextOpening->open_time);
        }

        $diffInMinutes = $now->diffInMinutes($nextOpeningDateTime);

        $days = floor($diffInMinutes / 1440);
        $hours = ceil(($diffInMinutes % 1440) / 60);

        if ($days > 0) {
            return "Reopens in {$days} day" . ($days > 1 ? 's' : '') . " {$hours} hour" . ($hours > 1 ? 's' : '');
        }

        return "Reopens in {$hours} hour" . ($hours > 1 ? 's' : '');
    }

    public function checkIfOpen($date): array
    {
        $parsedDate = Carbon::parse($date, 'Asia/Manila');
        $dayOfWeek = $parsedDate->dayOfWeek;

        $storeHours = StoreHour::query()
            ->where('day_of_week', $dayOfWeek)
            ->get();

        $isOpen = false;

        foreach ($storeHours as $hour) {
            if (!$hour->is_open) {
                continue;
            }

            if ($hour->is_alternate_saturday) {
                $isEvenWeek = $parsedDate->weekOfYear % 2 === 0;
                if (!$isEvenWeek) {
                    continue;
                }
            }

            if ($parsedDate->isToday() &&
                $parsedDate->between(
                    $parsedDate->copy()->setTimeFromTimeString($hour->open_time),
                    $parsedDate->copy()->setTimeFromTimeString($hour->close_time)->subSecond()
                )
            ) {
                $isOpen = true;
                break;
            }
        }

        return [
            'is_open' => $isOpen,
            'next_opening' => $isOpen ? null : $this->getNextOpening($date),
        ];
    }
}
