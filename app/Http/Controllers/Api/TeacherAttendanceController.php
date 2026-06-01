<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MadrasahResource;
use App\Http\Resources\TeachersAttendanceResource;
use App\Models\Madrasha;
use App\Models\TeacherAttendanceLog;
use App\Models\TeacherFaceData;
use App\Services\FaceVerificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeacherAttendanceController extends Controller
{
    private FaceVerificationService $faceService;

    public function __construct(FaceVerificationService $faceService)
    {
        $this->faceService = $faceService;
    }

    public function all(){
        $attendance = TeacherAttendanceLog::query()
            ->where('user_id', auth()->user()->id)
            ->latest('login')
            ->get();
        return TeachersAttendanceResource::collection($attendance);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'device_info' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $today = TeacherAttendanceLog::query()
            ->where('user_id', auth()->user()->id)
            ->whereRaw("date(login) = CURDATE()")
            ->first();

        $todayDate = Carbon::now()->toDateString();
        $userId = auth()->user()->id;
        $image = base64_decode($request->image);
        $extension = $this->getImageExtension($image);

        $faceResult = $this->verifyFace($image);
        $locationResult = $this->verifyLocation($request->latitude, $request->longitude);

        if ($today){
            $safeName = "{$userId}_{$todayDate}_logout.".$extension;
            Storage::disk('public_path')->put("teacher_attendance/".$safeName, $image);

            $today->update([
                'logout_location' => json_encode([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]),
                'logout' => Carbon::now(),
                'logout_photo' => $safeName,
                'face_match_score' => $faceResult['match_score'],
                'face_verified' => $faceResult['verified'],
                'location_verified' => $locationResult['verified'],
                'location_distance' => $locationResult['distance'],
                'device_info' => $request->device_info ? json_encode($request->device_info) : null,
            ]);

            $record = $today;
        } else {
            $safeName = "{$userId}_{$todayDate}_login.".$extension;
            Storage::disk('public_path')->put("teacher_attendance/".$safeName, $image);

            $record = TeacherAttendanceLog::create([
                'login_location' => json_encode([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]),
                'login' => Carbon::now(),
                'user_id' => $userId,
                'login_photo' => $safeName,
                'face_match_score' => $faceResult['match_score'],
                'face_verified' => $faceResult['verified'],
                'location_verified' => $locationResult['verified'],
                'location_distance' => $locationResult['distance'],
                'device_info' => $request->device_info ? json_encode($request->device_info) : null,
            ]);
        }

        return response()->json([
            'attendance' => new TeachersAttendanceResource($record),
            'face_verification' => $faceResult,
            'location_verification' => $locationResult,
            'type' => $today ? 'logout' : 'login',
        ]);
    }

    public function today(){
        $today = Carbon::now()->toDateString();
        $before30 = Carbon::now()->subDay(30)->toDateString();
        $attendance = TeacherAttendanceLog::query()
            ->where('user_id', auth()->user()->id)
            ->whereRaw("date(login) between '{$before30}' and '{$today}'")
            ->latest()
            ->get();
        return TeachersAttendanceResource::collection($attendance);
    }

    public function madrasah_location(){
        $user = auth()->user();
        if ($user->madrasah) {
            $madrasah = $user->madrasah;
            return response()->json(new MadrasahResource($madrasah));
        }
        return response()->json(['message' => 'No madrasah assigned'], 404);
    }

    public function todayStatus()
    {
        $today = TeacherAttendanceLog::query()
            ->where('user_id', auth()->user()->id)
            ->whereRaw("date(login) = CURDATE()")
            ->first();

        if (!$today) {
            return response()->json([
                'status' => 'not_logged_in',
                'message' => 'You have not logged attendance yet today',
            ]);
        }

        $hasLoggedOut = $today->logout !== null;

        return response()->json([
            'status' => $hasLoggedOut ? 'completed' : 'logged_in',
            'message' => $hasLoggedOut
                ? 'Attendance completed for today'
                : 'You are logged in. Please logout at end of day.',
            'login_time' => $today->login ? Carbon::parse($today->login)->toTimeString() : null,
            'logout_time' => $today->logout ? Carbon::parse($today->logout)->toTimeString() : null,
            'face_verified' => (bool) $today->face_verified,
            'location_verified' => (bool) $today->location_verified,
            'id' => $today->id,
        ]);
    }

    private function verifyFace(string $imageData): array
    {
        $faceData = TeacherFaceData::where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (!$faceData || !$faceData->face_image) {
            return [
                'verified' => false,
                'match_score' => 0,
                'message' => 'No face enrolled. Please setup face verification first.',
            ];
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'face_att_') . '.jpg';
        file_put_contents($tempPath, $imageData);

        $referencePaths = array_filter([
            $faceData->face_image,
            $faceData->face_image_2,
            $faceData->face_image_3,
        ]);

        $result = $this->faceService->compareWithMultipleReferences($tempPath, $referencePaths);

        unlink($tempPath);

        return [
            'verified' => $result['verified'],
            'match_score' => $result['match_score'],
            'message' => $result['verified']
                ? 'Face verified (match: ' . $result['match_score'] . '%)'
                : 'Face match too low (' . $result['match_score'] . '%)',
        ];
    }

    private function verifyLocation(float $latitude, float $longitude): array
    {
        $madrasah = auth()->user()->madrasah;
        if (!$madrasah || !$madrasah->location) {
            return [
                'verified' => false,
                'distance' => 0,
                'message' => 'Madrasah location not configured',
            ];
        }

        $madrasahLocation = json_decode($madrasah->location);
        if (!$madrasahLocation || empty($madrasahLocation->latitude) || empty($madrasahLocation->longitude)) {
            return [
                'verified' => false,
                'distance' => 0,
                'message' => 'Madrasah location not configured',
            ];
        }

        $distance = $this->haversineDistance(
            $latitude,
            $longitude,
            (float) $madrasahLocation->latitude,
            (float) $madrasahLocation->longitude
        );

        $radiusMeters = 100;
        $verified = $distance <= $radiusMeters;

        return [
            'verified' => $verified,
            'distance' => round($distance, 2),
            'message' => $verified
                ? 'Location verified (' . round($distance) . 'm from madrasah)'
                : 'You are ' . round($distance) . 'm away from madrasah (max: ' . $radiusMeters . 'm)',
        ];
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function getImageExtension(string $decoded): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $decoded);
        finfo_close($finfo);

        return match ($mime) {
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }
}
