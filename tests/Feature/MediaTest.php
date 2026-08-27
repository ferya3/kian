<?php

namespace Tests\Feature;

use App\Support\Media;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MediaTest extends TestCase
{
    public function test_an_uploaded_image_url_is_relative_to_the_current_host(): void
    {
        /*
         * نشانی مطلق یعنی اگر APP_URL دقیقاً با میزبان مرورگر یکی نباشد
         * (http/https، با یا بدون www، پشت پراکسی) همه‌ی تصویرها خراب می‌شوند.
         */
        Config::set('app.url', 'http://localhost');
        Config::set('filesystems.disks.public.url', 'http://localhost/storage');

        $this->assertSame(
            '/storage/admin/products/block.png',
            Media::url('admin/products/block.png')
        );
    }

    public function test_a_cdn_on_another_host_stays_absolute(): void
    {
        Config::set('app.url', 'https://kian-ceramic.ir');
        Config::set('filesystems.disks.public.url', 'https://cdn.example.com/files');

        $this->assertSame(
            'https://cdn.example.com/files/admin/products/block.png',
            Media::url('admin/products/block.png')
        );
    }

    public function test_it_returns_null_without_a_path(): void
    {
        $this->assertNull(Media::url(null));
        $this->assertNull(Media::url(''));
        $this->assertFalse(Media::has(null));
    }

    public function test_a_gallery_drops_empty_entries(): void
    {
        Config::set('app.url', 'http://localhost');
        Config::set('filesystems.disks.public.url', 'http://localhost/storage');

        $this->assertSame(
            ['/storage/a.png', '/storage/b.png'],
            Media::gallery(['a.png', null, '', 'b.png'])
        );
    }
}
