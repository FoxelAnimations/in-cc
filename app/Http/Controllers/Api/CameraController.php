<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\CameraDefaultBlock;
use App\Models\CameraSlotSetting;
use Illuminate\Http\JsonResponse;

class CameraController extends Controller
{
    public function schedule(): JsonResponse
    {
        $now = now('Europe/Brussels');
        $dayOfWeek = $now->dayOfWeekIso - 1; // 1=Mon→0, 7=Sun→6
        $currentTime = $now->format('H:i');

        $cameras = Camera::visible()
            ->with(['videos', 'defaultBlocks.video', 'scheduledVideos.video'])
            ->get();

        $result = $cameras->map(function (Camera $camera) use ($dayOfWeek, $currentTime, $now) {
            if ($camera->is_offline) {
                return [
                    'id' => $camera->id,
                    'name' => $camera->name,
                    'status' => 'offline',
                    'video_url' => null,
                    'next_check_seconds' => 300,
                ];
            }

            // Check scheduled videos first (overrides)
            $activeScheduled = $camera->scheduledVideos
                ->where('day_of_week', $dayOfWeek)
                ->filter(fn ($s) => substr($s->start_time, 0, 5) <= $currentTime && substr($s->end_time, 0, 5) > $currentTime)
                ->sortBy('start_time')
                ->first();

            $videoUrl = null;
            $blockEndTime = null;

            if ($activeScheduled) {
                $videoUrl = $activeScheduled->video?->videoUrl();
                $blockEndTime = substr($activeScheduled->end_time, 0, 5);
            } else {
                // Fall back to default block
                $slot = CameraDefaultBlock::slotForTime($currentTime);
                $defaultBlock = $camera->defaultBlocks
                    ->where('day_of_week', $dayOfWeek)
                    ->where('time_slot', $slot)
                    ->first();

                $videoUrl = $defaultBlock?->video?->videoUrl();
                $blockEndTime = CameraDefaultBlock::slots()[$slot]['end'];
            }

            // Calculate seconds until current block ends (handles midnight wrap)
            $blockEndMinutes = $blockEndTime === '24:00' ? 1440 : $this->timeToMinutes($blockEndTime);
            $currentMinutes = intval($now->format('H')) * 60 + intval($now->format('i'));
            $diff = $blockEndMinutes - $currentMinutes;
            if ($diff <= 0) {
                $diff += 1440; // wrap around midnight
            }
            $secondsUntilEnd = max(60, $diff * 60);

            // Check if a scheduled video starts sooner
            $nextScheduled = $camera->scheduledVideos
                ->where('day_of_week', $dayOfWeek)
                ->filter(fn ($s) => substr($s->start_time, 0, 5) > $currentTime)
                ->sortBy('start_time')
                ->first();

            if ($nextScheduled) {
                $nextStartMinutes = $this->timeToMinutes(substr($nextScheduled->start_time, 0, 5));
                $secondsUntilNext = max(60, ($nextStartMinutes - $currentMinutes) * 60);
                $secondsUntilEnd = min($secondsUntilEnd, $secondsUntilNext);
            }

            return [
                'id' => $camera->id,
                'name' => $camera->name,
                'status' => 'online',
                'video_url' => $videoUrl,
                'next_check_seconds' => $secondsUntilEnd,
            ];
        });

        return response()->json([
            'cameras' => $result->values(),
            'server_time' => $now->toIso8601String(),
            'slots' => CameraSlotSetting::getSlots(),
        ]);
    }

    private function timeToMinutes(string $time): int
    {
        $parts = explode(':', $time);
        return intval($parts[0]) * 60 + intval($parts[1] ?? 0);
    }
}
