<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class MigrateStudentsToUsersSeeder extends Seeder
{
    private $domain = 'example.com'; // Change to your domain

    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info('Starting student migration to users table...');

        $students = Student::whereNull('user_id')->get();
        $this->command->info("Found {$students->count()} students to migrate.");

        $progressBar = $this->command->getOutput()->createProgressBar($students->count());
        $progressBar->start();

        $results = [
            'created' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        foreach ($students as $student) {
            try {
                $this->migrateStudent($student, $results);
            } catch (\Exception $e) {
                $results['skipped']++;
                $results['errors'][] = "{$student->name}: {$e->getMessage()}";
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->showSummary($results);
    }

  private function migrateStudent(Student $student, array &$results)
{
    DB::transaction(function () use ($student, &$results) {
        // Check if student already has a user (double-check)
        if ($student->user_id) {
            throw new \Exception("Student already has user account");
        }

        // Generate unique email
        $baseEmail = $this->generateEmail($student);
        $email = $this->getUniqueEmail($baseEmail);

        // Clean and truncate phone number to fit in varchar(20)
        $phone = $this->cleanPhoneNumber($student->contact);

        // Create user
        $user = User::create([
            'name' => $student->name,
            'email' => $email,
            'password' => Hash::make('password'),
            'phone' => $phone, // Use cleaned phone number
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => $student->created_at,
            'updated_at' => now(),
        ]);

        // Update student
        $student->update([
            'user_id' => $user->id,
            'email' => $email,
        ]);

        try {
            $user->assignRole('student');
        } catch (\Exception $e) {
            // If student role doesn't exist in Spatie, just log it
            $this->command->warn("Spatie role 'student' not found for {$student->name}");
        }

        $results['created']++;

        // Log success (optional)
        if ($this->command->getOutput()->isVerbose()) {
            $this->command->info(" ✅ {$student->name} -> {$email}");
        }
    });
}

private function cleanPhoneNumber(?string $phone): ?string
{
    if (empty($phone)) {
        return null;
    }

    // Remove spaces, slashes, and other non-numeric characters except +
    $cleaned = preg_replace('/[^\d+]/', '', $phone);

    // Take only the first phone number if multiple exist
    // This handles cases like "07088661000708876395" after cleaning
    if (strlen($cleaned) > 20) {
        // If it's still too long, take the first 20 characters
        $cleaned = substr($cleaned, 0, 20);
    }

    // Alternative: Extract first phone number from the original string
    // This is better for numbers separated by slashes or commas
    $firstPhone = $this->extractFirstPhoneNumber($phone);

    return $firstPhone;
}

private function extractFirstPhoneNumber(string $phone): string
{
    // Split by common separators: comma, slash, space, "and", "or"
    $parts = preg_split('/[, \/]+|and|or/i', $phone);

    foreach ($parts as $part) {
        $part = trim($part);
        // Check if this part looks like a phone number (contains digits)
        if (preg_match('/\d/', $part)) {
            // Clean the phone number - keep only digits and +
            $cleanPart = preg_replace('/[^\d+]/', '', $part);

            // Validate it's a reasonable phone number length (8-15 digits typically)
            $digitCount = preg_match_all('/\d/', $cleanPart);
            if ($digitCount >= 8 && $digitCount <= 15 && strlen($cleanPart) <= 20) {
                return $cleanPart;
            }
        }
    }

    // Fallback: take first 20 characters of cleaned original
    $fallback = preg_replace('/[^\d+]/', '', $phone);
    return substr($fallback, 0, 20);
}

    private function generateEmail(Student $student): string
    {
        $name = $student->name;

        // Clean the name
        $cleanName = preg_replace('/[^a-zA-Z0-9\s]/', '', $name);
        $cleanName = strtolower(trim($cleanName));

        // Handle multiple names
        $nameParts = explode(' ', $cleanName);

        if (count($nameParts) >= 2) {
            // Use first name + last name
            $firstName = $nameParts[0];
            $lastName = end($nameParts);
            $emailName = $firstName . '.' . $lastName;
        } else {
            // Single name
            $emailName = $nameParts[0];
        }

        // Remove any remaining spaces and ensure it's clean
        $emailName = preg_replace('/\s+/', '', $emailName);
        $emailName = preg_replace('/\.+/', '.', $emailName);
        $emailName = trim($emailName, '.');

        return $emailName . '@' . $this->domain;
    }

    private function getUniqueEmail(string $baseEmail): string
    {
        $email = $baseEmail;
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            list($namePart, $domain) = explode('@', $baseEmail);
            $email = $namePart . $counter . '@' . $domain;
            $counter++;

            if ($counter > 50) {
                // Fallback: use random string
                $email = $namePart . '.' . Str::random(6) . '@' . $domain;
                break;
            }
        }

        return $email;
    }

    private function showSummary(array $results)
    {
        $this->command->info("\n\n=== Migration Summary ===");
        $this->command->info(" Successfully migrated: {$results['created']} students");
        $this->command->info(" Skipped/Failed: {$results['skipped']} students");

        if (!empty($results['errors'])) {
            $this->command->error("\n=== Errors ===");
            foreach (array_slice($results['errors'], 0, 10) as $error) { // Show first 10 errors
                $this->command->error($error);
            }

            if (count($results['errors']) > 10) {
                $this->command->error("... and " . (count($results['errors']) - 10) . " more errors");
            }
        }

        $this->command->info("\n Default password for all accounts: 'password'");
        $this->command->info("Email format: first.last@{$this->domain}");
        $this->command->info(" User role: 'student'");
        $this->command->info(" All accounts are email verified and active");
    }
}
