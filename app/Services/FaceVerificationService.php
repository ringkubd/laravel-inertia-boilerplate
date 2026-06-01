<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FaceVerificationService
{
    private float $threshold = 65.0;

    public function setThreshold(float $threshold): self
    {
        $this->threshold = $threshold;
        return $this;
    }

    public function compareFaces(string $capturedImagePath, string $referenceImagePath): array
    {
        $capturedPath = $this->ensureFullPath($capturedImagePath);
        $referencePath = $this->ensureFullPath($referenceImagePath);

        if (!file_exists($capturedPath) || !file_exists($referencePath)) {
            return [
                'match_score' => 0,
                'verified' => false,
                'error' => 'Image file not found',
            ];
        }

        try {
            $capturedHash = $this->perceptualHash($capturedPath);
            $referenceHash = $this->perceptualHash($referencePath);

            $distance = $this->hammingDistance($capturedHash, $referenceHash);
            $maxDistance = 64;
            $similarity = (1 - ($distance / $maxDistance)) * 100;

            $similarity = round($similarity, 2);

            return [
                'match_score' => $similarity,
                'verified' => $similarity >= $this->threshold,
                'hamming_distance' => $distance,
            ];
        } catch (\Exception $e) {
            Log::error('Face comparison failed: ' . $e->getMessage());
            return [
                'match_score' => 0,
                'verified' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function compareWithMultipleReferences(string $capturedPath, array $referencePaths): array
    {
        $bestScore = 0;
        $bestVerified = false;

        foreach ($referencePaths as $refPath) {
            if (empty($refPath)) continue;

            $result = $this->compareFaces($capturedPath, $refPath);
            if ($result['match_score'] > $bestScore) {
                $bestScore = $result['match_score'];
                $bestVerified = $result['verified'] ?? false;
            }
        }

        return [
            'match_score' => $bestScore,
            'verified' => $bestVerified,
        ];
    }

    private function perceptualHash(string $filePath): string
    {
        $size = 32;
        $smallSize = 8;

        list($width, $height) = getimagesize($filePath);
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $image = match ($extension) {
            'png' => imagecreatefrompng($filePath),
            'gif' => imagecreatefromgif($filePath),
            'webp' => imagecreatefromwebp($filePath),
            'jpg', 'jpeg' => imagecreatefromjpeg($filePath),
            default => throw new \InvalidArgumentException("Unsupported image type: {$extension}"),
        };

        $resized = imagecreatetruecolor($size, $size);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $size, $size, $width, $height);

        $pixels = [];
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $rgb = imagecolorat($resized, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $pixels[] = ($r + $g + $b) / 3;
            }
        }

        $small = imagecreatetruecolor($smallSize, $smallSize);
        imagecopyresampled($small, $image, 0, 0, 0, 0, $smallSize, $smallSize, $width, $height);

        $smallPixels = [];
        for ($y = 0; $y < $smallSize; $y++) {
            for ($x = 0; $x < $smallSize; $x++) {
                $rgb = imagecolorat($small, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $smallPixels[] = ($r + $g + $b) / 3;
            }
        }

        $average = array_sum($smallPixels) / count($smallPixels);

        $hash = '';
        foreach ($smallPixels as $pixel) {
            $hash .= ($pixel >= $average) ? '1' : '0';
        }

        imagedestroy($image);
        imagedestroy($resized);
        imagedestroy($small);

        return $hash;
    }

    private function hammingDistance(string $hash1, string $hash2): int
    {
        $distance = 0;
        $len = strlen($hash1);

        for ($i = 0; $i < $len; $i++) {
            if ($hash1[$i] !== $hash2[$i]) {
                $distance++;
            }
        }

        return $distance;
    }

    private function ensureFullPath(string $path): string
    {
        if (file_exists($path)) {
            return $path;
        }

        $storagePath = Storage::disk('public_path')->path($path);
        if (file_exists($storagePath)) {
            return $storagePath;
        }

        $publicPath = public_path($path);
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        return $path;
    }
}
