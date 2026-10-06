<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Banner;
use App\Models\Category;
use App\Models\SubscriptionPlan;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Admin::query()->updateOrCreate(
            ['email' => 'admin@mybaap.local'],
            [
                'name' => 'MyBaap Admin',
                'password' => 'Admin@12345',
            ],
        );

        $categories = [
            ['name' => 'Comedy', 'slug' => 'comedy', 'sort_order' => 1],
            ['name' => 'Motivation/Speech', 'slug' => 'motivation-speech', 'sort_order' => 2],
            ['name' => 'Local Movie', 'slug' => 'local-movie', 'sort_order' => 3],
            ['name' => 'Stage Show', 'slug' => 'stage-show', 'sort_order' => 4],
        ];

        $categoryIds = [];
        foreach ($categories as $row) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $row['slug']],
                $row + ['is_active' => true],
            );
            $categoryIds[$row['slug']] = $category->id;
        }

        $plans = [
            [
                'name' => 'Monthly',
                'price' => 99,
                'duration_days' => 30,
                'description' => '30 days of paid videos.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Yearly',
                'price' => 999,
                'duration_days' => 365,
                'description' => '365 days of paid videos.',
                'sort_order' => 2,
            ],
        ];

        foreach ($plans as $row) {
            SubscriptionPlan::query()->updateOrCreate(
                ['name' => $row['name']],
                $row + ['is_active' => true],
            );
        }

        $banners = [
            ['title' => 'Watch free demos', 'link' => null, 'sort_order' => 1, 'color' => '#058354'],
            ['title' => 'Unlock every paid video', 'link' => null, 'sort_order' => 2, 'color' => '#025B3D'],
        ];

        foreach ($banners as $row) {
            $image = 'banners/banner-'.$row['sort_order'].'.jpg';
            $this->writeJpeg($image, $row['title'], $row['color']);
            Banner::query()->updateOrCreate(
                ['title' => $row['title']],
                [
                    'image' => $image,
                    'link' => $row['link'],
                    'sort_order' => $row['sort_order'],
                    'is_active' => true,
                ],
            );
        }

        $videos = [
            ['slug' => 'comedy', 'title' => 'Open Mic Clip', 'is_paid' => false, 'duration' => 180, 'sort' => 1],
            ['slug' => 'comedy', 'title' => 'Full Comedy Night', 'is_paid' => true, 'duration' => 2400, 'sort' => 2],
            ['slug' => 'motivation-speech', 'title' => 'Two Minute Push', 'is_paid' => false, 'duration' => 120, 'sort' => 1],
            ['slug' => 'motivation-speech', 'title' => 'Stage Speech Full', 'is_paid' => true, 'duration' => 1800, 'sort' => 2],
            ['slug' => 'local-movie', 'title' => 'Movie Trailer', 'is_paid' => false, 'duration' => 90, 'sort' => 1],
            ['slug' => 'local-movie', 'title' => 'Local Feature Film', 'is_paid' => true, 'duration' => 5400, 'sort' => 2],
            ['slug' => 'stage-show', 'title' => 'Show Highlight', 'is_paid' => false, 'duration' => 240, 'sort' => 1],
            ['slug' => 'stage-show', 'title' => 'Full Stage Show', 'is_paid' => true, 'duration' => 3600, 'sort' => 2],
        ];

        foreach ($videos as $index => $row) {
            $n = $index + 1;
            $thumb = 'thumbnails/video-'.$n.'.jpg';
            $playback = 'videos/seed-'.$n.'.mp4';
            $this->writeJpeg($thumb, $row['title'], $row['is_paid'] ? '#025B3D' : '#058354');
            Storage::disk('local')->put($playback, 'mybaap-seed-placeholder-'.$n);

            Video::query()->updateOrCreate(
                ['title' => $row['title']],
                [
                    'category_id' => $categoryIds[$row['slug']],
                    'description' => $row['title'].' — sample catalog item.',
                    'thumbnail' => $thumb,
                    'playback_path' => $playback,
                    'duration_seconds' => $row['duration'],
                    'is_paid' => $row['is_paid'],
                    'is_active' => true,
                    'sort_order' => $row['sort'],
                ],
            );
        }
    }

    private function writeJpeg(string $path, string $label, string $hex): void
    {
        $rgb = sscanf($hex, '#%02x%02x%02x');
        $image = imagecreatetruecolor(960, 540);
        $background = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, 960, 540, $background);
        imagestring($image, 5, 40, 250, $label, $white);
        ob_start();
        imagejpeg($image, null, 85);
        $bytes = ob_get_clean();
        imagedestroy($image);
        Storage::disk('public')->put($path, $bytes);
    }
}
