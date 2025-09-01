use Illuminate\Support\Facades\Schema;

// Check if the columns exist in clinic_daily_schedules
$dailyColumns = Schema::getColumnListing('clinic_daily_schedules');
echo "Columns in clinic_daily_schedules:\n";
print_r($dailyColumns);

// Check if the columns exist in clinic_special_dates
$specialColumns = Schema::getColumnListing('clinic_special_dates');
echo "\nColumns in clinic_special_dates:\n";
print_r($specialColumns);

// Check for specific columns
$hasDailyLimit = in_array('daily_limit', $dailyColumns);
$hasSlotDuration = in_array('slot_duration', $dailyColumns);

echo "\nCheck for specific columns:\n";
echo "daily_limit: " . ($hasDailyLimit ? "exists" : "missing") . "\n";
echo "slot_duration: " . ($hasSlotDuration ? "exists" : "missing") . "\n";
