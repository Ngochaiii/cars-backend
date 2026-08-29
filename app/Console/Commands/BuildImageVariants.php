<?php

namespace App\Console\Commands;

use App\Media\MediaStore;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Sinh các bản ảnh nhỏ hơn (WebP) cho mọi ảnh trong disk public để trang
 * không còn tải ảnh 5760px xuống khung 1440px.
 *
 *     php artisan catalog:images          # chỉ làm ảnh chưa có biến thể
 *     php artisan catalog:images --force  # làm lại tất cả
 *
 * Ảnh gốc KHÔNG bị đụng tới. Biến thể nằm trong `catalog/_v/…` theo đúng cây
 * thư mục gốc, kèm hậu tố chiều rộng:
 *
 *     catalog/vinfast/vinfast-vf-3/hero.jpg
 *  → catalog/_v/vinfast/vinfast-vf-3/hero-800.webp
 *
 * Kích thước thật của ảnh gốc được ghi vào manifest `catalog/_v/manifest.json`
 * để Blade đặt được width/height mà không phải mở file ở mỗi request —
 * thiếu width/height là nguyên nhân trang bị giật khi ảnh tải xong.
 */
class BuildImageVariants extends Command
{
    protected $signature = 'catalog:images {--force : Sinh lại cả những biến thể đã có}';

    protected $description = 'Sinh biến thể WebP nhiều kích cỡ cho ảnh trong disk public';

    /** Các chiều rộng cần sinh. Bỏ qua cỡ lớn hơn ảnh gốc — phóng to chỉ tổ nặng file. */
    public const WIDTHS = [400, 800, 1280, 1920, 2560];

    /** Thư mục chứa biến thể, nằm trong disk public. */
    public const DIR = 'catalog/_v';

    public const MANIFEST = self::DIR.'/manifest.json';

    public function handle(): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('GD không có WebP. Cài lại PHP với --with-webp rồi chạy lại.');

            return self::FAILURE;
        }

        // Ảnh gốc tới 5760×3240 ngốn ~75 MB khi bung ra bitmap.
        ini_set('memory_limit', '512M');

        $media = app(MediaStore::class);
        $force = (bool) $this->option('force');

        $sources = collect($media->allFiles('catalog'))
            ->reject(fn (string $p) => Str::startsWith($p, self::DIR.'/'))
            ->filter(fn (string $p) => in_array(Str::lower(pathinfo($p, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true))
            ->values();

        if ($sources->isEmpty()) {
            $this->warn('Không tìm thấy ảnh nào trong catalog/.');

            return self::SUCCESS;
        }

        $manifest = $this->readManifest($media);
        $bar      = $this->output->createProgressBar($sources->count());
        $bar->start();

        $made = 0;
        $kept = 0;
        $failed = [];

        foreach ($sources as $path) {
            try {
                [$n, $entry] = $this->processOne($media, $path, $force);
                $made += $n;
                $kept += $n === 0 ? 1 : 0;
                if ($entry) {
                    $manifest[$path] = $entry;
                }
            } catch (\Throwable $e) {
                $failed[] = $path.' — '.$e->getMessage();
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $media->write(self::MANIFEST, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info("Ảnh gốc: {$sources->count()} · biến thể mới: {$made} · đã có sẵn: {$kept}");
        $this->line('Manifest: '.self::MANIFEST.' ('.count($manifest).' mục)');

        foreach ($failed as $f) {
            $this->warn('Bỏ qua: '.$f);
        }

        return self::SUCCESS;
    }

    /**
     * @return array{0:int,1:?array} số biến thể vừa sinh, và mục manifest
     */
    private function processOne(MediaStore $media, string $path, bool $force): array
    {
        $full = $media->absolutePath($path);
        $info = @getimagesize($full);

        if (! $info) {
            throw new \RuntimeException('không đọc được kích thước');
        }

        [$srcW, $srcH] = $info;
        $widths = array_values(array_filter(self::WIDTHS, fn (int $w) => $w < $srcW));

        // Ảnh vốn đã nhỏ hơn bậc đầu tiên thì dùng thẳng bản gốc.
        $entry = ['w' => $srcW, 'h' => $srcH, 'v' => $widths];

        $needed = $force ? $widths : array_values(array_filter(
            $widths,
            fn (int $w) => ! $media->exists($this->variantPath($path, $w)),
        ));

        if ($needed === []) {
            return [0, $entry];
        }

        $src = @imagecreatefromstring(file_get_contents($full));

        if (! $src) {
            throw new \RuntimeException('GD không giải mã được');
        }

        $count = 0;

        foreach ($needed as $w) {
            $h   = (int) round($srcH * ($w / $srcW));
            $dst = imagecreatetruecolor($w, $h);

            // Giữ vùng trong suốt của PNG khi chuyển sang WebP.
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $srcW, $srcH);

            $tmp = tempnam(sys_get_temp_dir(), 'iv');
            imagewebp($dst, $tmp, 82);
            $media->write($this->variantPath($path, $w), (string) file_get_contents($tmp));

            @unlink($tmp);
            imagedestroy($dst);
            $count++;
        }

        imagedestroy($src);

        return [$count, $entry];
    }

    /** catalog/a/b.jpg + 800 → catalog/_v/a/b-800.webp */
    public static function variantPath(string $path, int $width): string
    {
        $rel = Str::after($path, 'catalog/');
        $dir = trim(pathinfo($rel, PATHINFO_DIRNAME), '.');
        $base = pathinfo($rel, PATHINFO_FILENAME);

        return self::DIR.'/'.($dir !== '' ? $dir.'/' : '').$base.'-'.$width.'.webp';
    }

    private function readManifest(MediaStore $media): array
    {
        if (! $media->exists(self::MANIFEST)) {
            return [];
        }

        return json_decode((string) $media->read(self::MANIFEST), true) ?: [];
    }
}
