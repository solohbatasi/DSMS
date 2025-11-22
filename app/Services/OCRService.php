<?php

namespace App\Services;

use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Image;
use Illuminate\Support\Facades\Log;

class OCRService
{
    public function extractMeterReading($imagePath)
    {
        try {
            // Check if the image file exists
            if (!file_exists($imagePath)) {
                Log::error("OCR Service: Image file not found - " . $imagePath);
                return null;
            }

            // Initialize the Vision client
            $imageAnnotator = new ImageAnnotatorClient([
                // If you have a service account key file, specify the path here
                // 'keyFilePath' => storage_path('app/google-vision-key.json')
                // Otherwise, it will use environment variables or default credentials
            ]);

            $image = Image::fromFile($imagePath);
            
            // Configure for text detection
            $response = $imageAnnotator->textDetection($image);
            $texts = $response->getTextAnnotations();

            if ($texts && count($texts) > 0) {
                $detectedText = $texts[0]->getDescription();
                Log::info("OCR Detected Text: " . $detectedText);
                return $this->parseMeterReading($detectedText);
            }

            Log::warning("OCR Service: No text detected in image");
            return null;

        } catch (\Exception $e) {
            Log::error('OCR Service Error: ' . $e->getMessage());
            return null;
        } finally {
            if (isset($imageAnnotator)) {
                $imageAnnotator->close();
            }
        }
    }

    private function parseMeterReading($text)
    {
        if (empty($text)) {
            return null;
        }

        // Clean the text - remove non-numeric characters except decimal points
        $cleanText = preg_replace('/[^\d.]/', ' ', $text);
        
        // Split into individual numbers
        $numbers = preg_split('/\s+/', trim($cleanText));
        
        // Filter valid meter readings
        $validReadings = array_filter($numbers, function($num) {
            if (empty($num)) return false;
            
            $value = floatval($num);
            // Meter readings are typically positive numbers within a reasonable range
            return $value > 0 && $value <= 99999999; // Up to 8 digits
        });

        if (empty($validReadings)) {
            Log::warning("OCR Service: No valid numbers found in text: " . $text);
            return null;
        }

        // Return the largest number (most likely the current meter reading)
        $largest = max($validReadings);
        $reading = floatval($largest);
        
        Log::info("OCR Service: Extracted reading - " . $reading);
        return number_format($reading, 2, '.', '');
    }

    /**
     * Alternative method for base64 images (useful for API calls)
     */
    public function extractMeterReadingFromBase64($base64Image)
    {
        try {
            // Remove data URL prefix if present
            if (strpos($base64Image, 'base64,') !== false) {
                $base64Image = substr($base64Image, strpos($base64Image, 'base64,') + 7);
            }

            $imageContent = base64_decode($base64Image);
            
            $imageAnnotator = new ImageAnnotatorClient();
            $image = Image::fromString($imageContent);
            
            $response = $imageAnnotator->textDetection($image);
            $texts = $response->getTextAnnotations();

            if ($texts && count($texts) > 0) {
                $detectedText = $texts[0]->getDescription();
                return $this->parseMeterReading($detectedText);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('OCR Service Base64 Error: ' . $e->getMessage());
            return null;
        } finally {
            if (isset($imageAnnotator)) {
                $imageAnnotator->close();
            }
        }
    }
}