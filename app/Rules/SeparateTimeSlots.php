<?php

namespace App\Rules;

use Closure;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\ValidationRule;

class SeparateTimeSlots implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            // Convert the time strings to DateTime objects for comparison.
            $slots = collect($value);

            foreach ($slots as $key1 => $slot1) {
                foreach ($slots as $key2 => $slot2) {
                    // Skip comparing a slot to itself
                    if ($key1 === $key2) {
                        continue;
                    }

                    $start1 = strtotime($slot1['start_time']);
                    $end1 = strtotime($slot1['end_time']);
                    $start2 = strtotime($slot2['start_time']);
                    $end2 = strtotime($slot2['end_time']);

                    // Check for overlap
                    if (
                        $start1 === $start2 || ($start1 > $start2 && $start1 < $end2)
                        || $end1 === $end2 || ($end1 > $start2 && $end1 < $end2)
                    ) {
                        $fail("The time slots overlap with each other.");
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            $fail("Please provide correct slot times");
        }
    }
}
