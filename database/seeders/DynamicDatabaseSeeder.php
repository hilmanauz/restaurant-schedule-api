<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\Schedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DynamicDatabaseSeeder extends Seeder
{
    /**
     * Daftar hari.
     */
    private $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

    /**
     * Fungsi untuk memformat jadwal restoran.
     */
    private function parseSchedule(string $rawSchedule): array
    {
        $result = [];
        $sections = explode(" / ", $rawSchedule);

        foreach ($sections as $section) {
            preg_match('/(?<days>[a-zA-Z,\-\s]+)\s(?<start>[0-9:apm\s]+)\s-\s(?<end>[0-9:apm\s]+)/', $section, $matches);

            if ($matches) {
                $dayRanges = explode(",", trim($matches['days']));
                $openTime = date("H:i:s", strtotime($matches['start']));
                $closeTime = date("H:i:s", strtotime($matches['end']));

                foreach ($dayRanges as $range) {
                    $range = trim($range);
                    if (strpos($range, '-') !== false) {
                        [$startDay, $endDay] = array_map('trim', explode('-', $range));
                        $startIndex = array_search($startDay, $this->days);
                        $endIndex = array_search($endDay, $this->days);

                        for ($i = $startIndex; $i <= $endIndex; $i++) {
                            $result[] = [
                                'day_of_week' => $this->days[$i],
                                'open_time' => $openTime,
                                'close_time' => $closeTime,
                            ];
                        }
                    } else {
                        $result[] = [
                            'day_of_week' => $range,
                            'open_time' => $openTime,
                            'close_time' => $closeTime,
                        ];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Run the database seeds.
     */

    public function run()
    {
        $restaurants = [
            ["name" => "Kushi Tsuru", "schedule" => "Mon-Sun 11:30 am - 9 pm"]
            ,
            [
                "name" =>
                    "Osakaya Restaurant",
                "schedule" =>
                    "Mon-Thu, Sun 11:30 am - 9 pm / Fri-Sat 11:30
am - 9:30 pm"
            ]
            ,
            [
                "name" =>
                    "The Stinking Rose",
                "schedule" =>
                    "Mon-Thu, Sun 11:30 am - 10 pm / Fri-Sat 11:30
am - 11 pm"
            ]
            ,
            [
                "name" =>
                    "McCormick & Kuleto's",
                "schedule" =>
                    "Mon-Thu, Sun 11:30 am - 10 pm / Fri-Sat
11:30 am - 11 pm"
            ]
            ,
            ["name" => "Mifune Restaurant", "schedule" => "Mon-Sun 11 am - 10 pm"]
            ,
            [
                "name" =>
                    "The Cheesecake Factory",
                "schedule" =>
                    "Mon-Thu 11 am - 11 pm / Fri-Sat 11 am -
12:30 am / Sun 10 am - 11 pm"
            ]
            ,
            [
                "name" =>
                    "New Delhi Indian Restaurant",
                "schedule" =>
                    "Mon-Sat 11:30 am - 10 pm / Sun 5:30
pm - 10 pm"
            ]
            ,
            [
                "name" =>
                    "Iroha Restaurant",
                "schedule" =>
                    "Mon-Thu, Sun 11:30 am - 9:30 pm / Fri-Sat 11:30
am - 10 pm"
            ]
            ,
            ["name" => "Rose Pistola", "schedule" => "Mon-Thu 11:30 am - 10 pm / Fri-Sun 11:30 am - 11 pm"]
            ,
            ["name" => "Alioto's Restaurant", "schedule" => "Mon-Sun 11 am - 11 pm"]
            ,
            [
                "name" =>
                    "Canton Seafood & Dim Sum Restaurant",
                "schedule" =>
                    "Mon-Fri 10:30 am - 9:30 pm /
Sat-Sun 10 am - 9:30 pm"
            ]
            ,
            [
                "name" =>
                    "All Season Restaurant",
                "schedule" =>
                    "Mon-Fri 10 am - 9:30 pm / Sat-Sun 9:30 am -
9:30 pm"
            ]
            ,
            ["name" => "Bombay Indian Restaurant", "schedule" => "Mon-Sun 11:30 am - 10:30 pm"]
            ,
            [
                "name" =>
                    "Sam's Grill & Seafood Restaurant",
                "schedule" =>
                    "Mon-Fri 11 am - 9 pm / Sat 5 pm
- 9 pm"
            ]
            ,
            [
                "name" =>
                    "2G Japanese Brasserie",
                "schedule" =>
                    "Mon-Thu, Sun 11 am - 10 pm / Fri-Sat 11 am
- 11 pm"
            ]
            ,
            [
                "name" =>
                    "Restaurant Lulu",
                "schedule" =>
                    "Mon-Thu, Sun 11:30 am - 9 pm / Fri-Sat 11:30 am -
10 pm"
            ]
            ,
            [
                "name" =>
                    "Sudachi",
                "schedule" =>
                    "Mon-Wed 5 pm - 12:30 am / Thu-Fri 5 pm - 1:30 am / Sat 3
pm - 1:30 am / Sun 3 pm - 11:30 pm"
            ]
            ,
            ["name" => "Hanuri", "schedule" => "Mon-Sun 11 am - 12 am"]
            ,
            ["name" => "Herbivore", "schedule" => "Mon-Thu, Sun 9 am - 10 pm / Fri-Sat 9 am - 11 pm"]
            ,
            [
                "name" =>
                    "Penang Garden",
                "schedule" =>
                    "Mon-Thu 11 am - 10 pm / Fri-Sat 10 am - 10:30 pm /
Sun 11 am - 11 pm"
            ]
            ,
            ["name" => "John's Grill", "schedule" => "Mon-Sat 11 am - 10 pm / Sun 12 pm - 10 pm"]
            ,
            ["name" => "Quan Bac", "schedule" => "Mon-Sun 11 am - 10 pm"]
            ,
            ["name" => "Bamboo Restaurant", "schedule" => "Mon-Sat 11 am - 12 am / Sun 12 pm - 12 am"]
            ,
            ["name" => "Burger Bar", "schedule" => "Mon-Thu, Sun 11 am - 10 pm / Fri-Sat 11 am - 12 am"]
            ,
            ["name" => "Blu Restaurant", "schedule" => "Mon-Fri 11:30 am - 10 pm / Sat-Sun 7 am - 3 pm"]
            ,
            ["name" => "Naan 'N' Curry", "schedule" => "Mon-Sun 11 am - 4 am"]
            ,
            ["name" => "Shanghai China Restaurant", "schedule" => "Mon-Sun 11 am - 9:30 pm"]
            ,
            ["name" => "Tres", "schedule" => "Mon-Thu, Sun 11:30 am - 10 pm / Fri-Sat 11:30 am - 11 pm"]
            ,
            ["name" => "Isobune Sushi", "schedule" => "Mon-Sun 11:30 am - 9:30 pm"]
            ,
            ["name" => "Viva Pizza Restaurant", "schedule" => "Mon-Sun 11 am - 12 am"]
            ,
            ["name" => "Far East Cafe", "schedule" => "Mon-Sun 11:30 am - 10 pm"]
            ,
            ["name" => "Parallel 37", "schedule" => "Mon-Sun 11:30 am - 10 pm"]
            ,
            ["name" => "Bai Thong Thai Cuisine", "schedule" => "Mon-Sat 11 am - 11 pm / Sun 11 am - 10 pm"]
            ,
            ["name" => "Alhamra", "schedule" => "Mon-Sun 11 am - 11 pm"]
            ,
            ["name" => "A-1 Cafe Restaurant", "schedule" => "Mon, Wed-Sun 11 am - 10 pm"]
            ,
            ["name" => "Nick's Lighthouse", "schedule" => "Mon-Sun 11 am - 10:30 pm"]
            ,
            [
                "name" =>
                    "Paragon Restaurant & Bar",
                "schedule" =>
                    "Mon-Fri 11:30 am - 10 pm / Sat 5:30 pm -
10 pm"
            ]
            ,
            ["name" => "Chili Lemon Garlic", "schedule" => "Mon-Fri 11 am - 10 pm / Sat-Sun 5 pm - 10 pm"]
            ,
            ["name" => "Bow Hon Restaurant", "schedule" => "Mon-Sun 11 am - 10:30 pm"]
            ,
            ["name" => "San Dong House", "schedule" => "Mon-Sun 11 am - 11 pm"]
            ,
            ["name" => "Thai Stick Restaurant", "schedule" => "Mon-Sun 11 am - 1 am"],
            [
                "name" =>
                    "Cesario's",
                "schedule" =>
                    "Mon-Thu, Sun 11:30 am - 10 pm / Fri-Sat 11:30 am -
10:30 pm"
            ]
            ,
            [
                "name" =>
                    "Colombini Italian Cafe Bistro",
                "schedule" =>
                    "Mon-Fri 12 pm - 10 pm / Sat-Sun 5
pm - 10 pm"
            ]
            ,
            [
                "name" =>
                    "Sabella & La Torre",
                "schedule" =>
                    "Mon-Thu, Sun 10 am - 10:30 pm / Fri-Sat 10 am
- 12:30 am"
            ]

            ,
            [
                "name" =>
                    "Soluna Cafe and Lounge",
                "schedule" =>
                    "Mon-Fri 11:30 am - 10 pm / Sat 5 pm - 10
pm"
            ]
            ,
            ["name" => "Tong Palace", "schedule" => "Mon-Fri 9 am - 9:30 pm / Sat-Sun 9 am - 10 pm"]
            ,
            ["name" => "India Garden Restaurant", "schedule" => "Mon-Sun 10 am - 11 pm"]
            ,
            [
                "name" =>
                    "Sapporo-Ya Japanese Restaurant",
                "schedule" =>
                    "Mon-Sat 11 am - 11 pm / Sun 11 am
- 10:30 pm"
            ]
            ,
            ["name" => "Santorini's Mediterranean Cuisine", "schedule" => "Mon-Sun 8 am - 10:30 pm"]
            ,
            [
                "name" =>
                    "Kyoto Sushi",
                "schedule" =>
                    "Mon-Thu 11 am - 10:30 pm / Fri 11 am - 11 pm / Sat
11:30 am - 11 pm / Sun 4:30 pm - 10:30 pm"
            ]
            ,
            ["name" => "Marrakech Moroccan Restaurant", "schedule" => "Mon-Sun 5:30 pm - 2 am"]
            ,
            [
                "name" =>
                    "Parallel 37",
                "schedule" =>
                    "Mon, Fri 5 pm - 6:15 pm / Tue 12:15 pm - 12:15 pm /
Weds 1:15 pm - 5:45 pm / Thu, Sat 10 am - 3 pm / Sun 6:30 am -
12:45 pm"
            ]
        ];
        ;

        foreach ($restaurants as $restaurantData) {
            $formattedSchedules = $this->parseSchedule($restaurantData['schedule']);
            $restaurant = Restaurant::create([
                'name' => $restaurantData['name'],
            ]);
            $restaurantId = $restaurant->id;

            foreach ($formattedSchedules as $schedule) {
                Schedule::create([
                    'restaurant_id' => $restaurantId,
                    'day_of_week' => $schedule['day_of_week'],
                    'open_time' => $schedule['open_time'],
                    'close_time' => $schedule['close_time'],
                ]);
            }
        }
    }
}
