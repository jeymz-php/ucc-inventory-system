<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\ConsumableCategory;

class ConsumableCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = ['Office Supplies', 'Cleaning Supplies', 'Janitorial Equipment', 'ICT Supplies', 'Medical Supplies', 'Printing and Presentation Materials', 'Miscellaneous'];
        foreach ($categories as $cat) {
            ConsumableCategory::firstOrCreate(['name' => $cat]);
        }
    }
}