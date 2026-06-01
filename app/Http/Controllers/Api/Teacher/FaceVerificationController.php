<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Madrasha;
use App\Models\TeacherFaceData;
use App\Services\FaceVerificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FaceVerificationController extends Controller
{
    private FaceVerificationService $faceService;

    public function __construct(FaceVerificationService $faceService)
    {
        $this->faceService = $faceService;
    }

    public function status()
    {
        $faceData = TeacherFaceData::where('user_id', auth()->id())->first();

        $madrasah = auth()->user()->madrasah;
        $locationSet = false;
        if ($madrasah && $madrasah->location) {
            $loc = json_decode($madrasah->location);
            $locationSet = !empty($loc->latitude) && !empty($loc->longitude);
        }

        return response()->json([
            'face_enrolled' => $faceData && $faceData->is_active,
            'face_images_count' => $faceData ? $this->countFaceImages($faceData) : 0,
            'location_set' => $locationSet,
            'madrasah_name' => $madrasah?->name,
            'needs_setup' => !($faceData && $faceData->is_active) || !$locationSet,
        ]);
    }

    public function enroll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
            'image_2' => 'nullable|string',
            'image_3' => 'nullable|string',
            'angle' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $userId = auth()->id();
        $timestamp = Carbon::now()->format('Ymd_His');
        $paths = [];

        $images = [
            'face_image' => $request->image,
            'face_image_2' => $request->image_2,
            'face_image_3' => $request->image_3,
        ];

        foreach ($images as $field => $imageData) {
            if (empty($imageData)) continue;

            $decoded = base64_decode($imageData);
            $extension = $this->getImageExtension($decoded);
            $filename = "face_{$userId}_{$timestamp}_{$field}.{$extension}";

            Storage::disk('public_path')->put("face_data/{$filename}", $decoded);
            $paths[$field] = "face_data/{$filename}";
        }

        $faceData = TeacherFaceData::updateOrCreate(
            ['user_id' => $userId],
            [
                'face_image' => $paths['face_image'] ?? null,
                'face_image_2' => $paths['face_image_2'] ?? null,
                'face_image_3' => $paths['face_image_3'] ?? null,
                'metadata' => [
                    'angle' => $request->angle ?? 'front',
                    'enrolled_at' => Carbon::now()->toIso8601String(),
                    'device' => $request->header('User-Agent'),
                ],
                'is_active' => true,
            ]
        );

        return response()->json([
            'message' => 'Face enrolled successfully',
            'face_images_count' => $this->countFaceImages($faceData),
            'face_data' => $faceData,
        ]);
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $userId = auth()->id();
        $faceData = TeacherFaceData::where('user_id', $userId)->where('is_active', true)->first();

        if (!$faceData || !$faceData->face_image) {
            return response()->json([
                'verified' => false,
                'match_score' => 0,
                'message' => 'No face reference found. Please enroll your face first.',
            ], 400);
        }

        $decoded = base64_decode($request->image);
        $extension = $this->getImageExtension($decoded);
        $tempPath = tempnam(sys_get_temp_dir(), 'face_verify_') . ".{$extension}";
        file_put_contents($tempPath, $decoded);

        $referencePaths = array_filter([
            $faceData->face_image,
            $faceData->face_image_2,
            $faceData->face_image_3,
        ]);

        $result = $this->faceService->compareWithMultipleReferences($tempPath, $referencePaths);

        unlink($tempPath);

        return response()->json([
            'verified' => $result['verified'],
            'match_score' => $result['match_score'],
            'message' => $result['verified']
                ? 'Face verified successfully'
                : 'Face does not match. Score: ' . $result['match_score'] . '%',
        ]);
    }

    public function updateMadrasahLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $madrasah = auth()->user()->madrasah;
        if (!$madrasah) {
            return response()->json(['message' => 'No madrasah assigned to your account'], 404);
        }

        $madrasah->update([
            'location' => json_encode([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]),
        ]);

        return response()->json([
            'message' => 'Madrasah location updated successfully',
            'location' => [
                'latitude' => (float) $request->latitude,
                'longitude' => (float) $request->longitude,
            ],
        ]);
    }

    public function getMadrasahLocation()
    {
        $madrasah = auth()->user()->madrasah;
        if (!$madrasah) {
            return response()->json(['message' => 'No madrasah assigned'], 404);
        }

        $location = $madrasah->location ? json_decode($madrasah->location) : null;

        return response()->json([
            'id' => $madrasah->id,
            'name' => $madrasah->name,
            'address' => $madrasah->address,
            'latitude' => $location->latitude ?? null,
            'longitude' => $location->longitude ?? null,
            'location_set' => !empty($location->latitude) && !empty($location->longitude),
        ]);
    }

    public function deleteFaceData()
    {
        $faceData = TeacherFaceData::where('user_id', auth()->id())->first();
        if (!$faceData) {
            return response()->json(['message' => 'No face data found'], 404);
        }

        foreach (['face_image', 'face_image_2', 'face_image_3'] as $field) {
            if ($faceData->$field) {
                Storage::disk('public_path')->delete($faceData->$field);
            }
        }

        $faceData->delete();

        return response()->json(['message' => 'Face data deleted successfully']);
    }

    private function countFaceImages(TeacherFaceData $faceData): int
    {
        $count = 0;
        foreach (['face_image', 'face_image_2', 'face_image_3'] as $field) {
            if ($faceData->$field) $count++;
        }
        return $count;
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
