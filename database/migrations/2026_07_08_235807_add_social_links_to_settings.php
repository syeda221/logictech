<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insert([
            [
                'key' => 'facebook_link',
                'value' => null,
                'type' => 'string',
                'group' => 'company',
                'label' => 'Facebook Link',
                'description' => 'Facebook page URL for receipts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tiktok_link',
                'value' => null,
                'type' => 'string',
                'group' => 'company',
                'label' => 'TikTok Link',
                'description' => 'TikTok profile URL for receipts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'instagram_link',
                'value' => null,
                'type' => 'string',
                'group' => 'company',
                'label' => 'Instagram Link',
                'description' => 'Instagram profile URL for receipts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'website_link',
                'value' => null,
                'type' => 'string',
                'group' => 'company',
                'label' => 'Website Link',
                'description' => 'Website URL for receipts',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'facebook_link',
            'tiktok_link',
            'instagram_link',
            'website_link'
        ])->delete();
    }
};
