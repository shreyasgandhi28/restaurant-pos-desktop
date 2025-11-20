<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class InstallFonts extends Command
{
    protected $signature = 'fonts:install';
    protected $description = 'Install required fonts for PDF generation';

    public function handle()
    {
        $fontDir = storage_path('fonts');
        
        // Create fonts directory if it doesn't exist
        if (!File::exists($fontDir)) {
            File::makeDirectory($fontDir, 0755, true);
        }

        // Download Noto Sans Devanagari font
        $this->info('Downloading Noto Sans Devanagari font...');
        $fontUrl = 'https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansDevanagari/NotoSansDevanagari-Regular.ttf';
        $fontPath = $fontDir . '/NotoSansDevanagari-Regular.ttf';
        
        if (!file_put_contents($fontPath, file_get_contents($fontUrl))) {
            $this->error('Failed to download Noto Sans Devanagari font');
            return 1;
        }

        // Download Noto Sans Devanagari Bold font
        $boldFontUrl = 'https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansDevanagari/NotoSansDevanagari-Bold.ttf';
        $boldFontPath = $fontDir . '/NotoSansDevanagari-Bold.ttf';
        
        if (!file_put_contents($boldFontPath, file_get_contents($boldFontUrl))) {
            $this->error('Failed to download Noto Sans Devanagari Bold font');
            return 1;
        }

        $this->info('Fonts installed successfully!');
        $this->info('Please run: php artisan cache:clear');
        
        return 0;
    }
}
