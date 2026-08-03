<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Post;
use App\Models\User;
use GdImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class UploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    private string $originalPublicPath;

    private string $testPublicPath;

    /** @var list<string> */
    private array $temporaryUploads = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->originalPublicPath = app()->publicPath();
        $this->testPublicPath = storage_path('framework/testing/public-'.Str::random(12));
        File::ensureDirectoryExists($this->testPublicPath);
        app()->usePublicPath($this->testPublicPath);
    }

    protected function tearDown(): void
    {
        app()->usePublicPath($this->originalPublicPath);
        File::deleteDirectory($this->testPublicPath);

        foreach ($this->temporaryUploads as $temporaryUpload) {
            File::delete($temporaryUpload);
        }

        parent::tearDown();
    }

    public function test_supported_image_formats_are_reencoded_as_generated_webp_files(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        foreach (['jpeg' => 'cover.jpg', 'png' => 'cover.png', 'webp' => 'cover.webp'] as $format => $filename) {
            $title = 'Book '.$format;

            $response = $this->actingAs($admin)->post(route('admin.books.store'), [
                'title' => $title,
                'slug' => Str::slug($title),
                'status' => 'available',
                'description' => 'A secure upload.',
                'cover_file' => $this->rasterUpload($filename, $format),
            ]);

            $response->assertRedirect(route('admin.books.index'));

            $cover = Book::query()->where('title', $title)->value('cover');

            $this->assertIsString($cover);
            $this->assertMatchesRegularExpression('#^images/uploads/[A-Za-z0-9]{40}\.webp$#', $cover);
            $this->assertSame('image/webp', mime_content_type(public_path($cover)));
        }
    }

    public function test_post_upload_uses_the_same_secure_webp_pipeline(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'Secure post image',
            'slug' => 'secure-post-image',
            'image_file' => $this->rasterUpload('article.jpg', 'jpeg'),
            'is_published' => true,
        ]);

        $response->assertRedirect(route('admin.posts.index'));

        $image = Post::query()->where('title', 'Secure post image')->value('image');

        $this->assertIsString($image);
        $this->assertMatchesRegularExpression('#^images/uploads/[A-Za-z0-9]{40}\.webp$#', $image);
        $this->assertSame('image/webp', mime_content_type(public_path($image)));
    }

    public function test_gif_and_bmp_uploads_are_rejected_without_persisting_files(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        foreach (['gif' => 'cover.gif', 'bmp' => 'cover.bmp'] as $format => $filename) {
            $response = $this->actingAs($admin)->from(route('admin.books.create'))->post(route('admin.books.store'), [
                'title' => 'Rejected '.$format,
                'status' => 'available',
                'description' => 'Rejected upload.',
                'cover_file' => $this->rasterUpload($filename, $format),
            ]);

            $response->assertRedirect(route('admin.books.create'));
            $response->assertSessionHasErrors('cover_file');
        }

        $this->assertSame(0, Book::query()->count());
        $this->assertUploadDirectoryIsEmpty();
    }

    public function test_corrupt_oversized_and_non_image_payloads_are_rejected(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $invalidUploads = [
            $this->contentUpload('corrupt.jpg', 'not a decodable image'),
            $this->oversizedPngUpload('oversized.png'),
            $this->contentUpload('document.txt', 'plain text is not an image'),
        ];

        foreach ($invalidUploads as $index => $invalidUpload) {
            $response = $this->actingAs($admin)->post(route('admin.books.store'), [
                'title' => 'Invalid upload '.$index,
                'status' => 'available',
                'description' => 'Rejected upload.',
                'cover_file' => $invalidUpload,
            ]);

            $response->assertSessionHasErrors('cover_file');
        }

        $this->assertSame(0, Book::query()->count());
        $this->assertUploadDirectoryIsEmpty();
    }

    public function test_php_filename_is_rejected_even_when_the_content_is_a_valid_jpeg(): void
    {
        $this->assertMisleadingFilenameIsRejected('cover.php');
    }

    public function test_multiple_extension_php_filename_is_rejected_even_when_the_content_is_a_valid_jpeg(): void
    {
        $this->assertMisleadingFilenameIsRejected('cover.jpg.php');
    }

    public function test_invalid_executable_looking_payload_is_rejected(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.books.store'), [
            'title' => 'Executable payload',
            'status' => 'available',
            'description' => 'Must be rejected.',
            'cover_file' => $this->contentUpload('cover.jpg.php', '<?php echo "unsafe";'),
        ]);

        $response->assertSessionHasErrors('cover_file');
        $this->assertSame(0, Book::query()->count());
        $this->assertUploadDirectoryIsEmpty();
    }

    private function rasterUpload(string $filename, string $format): UploadedFile
    {
        $path = tempnam(storage_path('framework/testing'), 'upload-');
        $image = imagecreatetruecolor(32, 32);

        $this->assertIsString($path);
        $this->assertInstanceOf(GdImage::class, $image);

        $written = match ($format) {
            'jpeg' => imagejpeg($image, $path, 90),
            'png' => imagepng($image, $path),
            'webp' => imagewebp($image, $path, 90),
            'gif' => imagegif($image, $path),
            'bmp' => imagebmp($image, $path),
        };

        imagedestroy($image);
        $this->assertTrue($written);
        $this->temporaryUploads[] = $path;

        return new UploadedFile($path, $filename, null, null, true);
    }

    private function contentUpload(string $filename, string $content): UploadedFile
    {
        $path = tempnam(storage_path('framework/testing'), 'upload-');

        $this->assertIsString($path);
        File::put($path, $content);
        $this->temporaryUploads[] = $path;

        return new UploadedFile($path, $filename, null, null, true);
    }

    private function oversizedPngUpload(string $filename): UploadedFile
    {
        $upload = $this->rasterUpload($filename, 'png');
        File::append($upload->getPathname(), str_repeat('x', (4096 * 1024) + 1));

        return $upload;
    }

    private function assertMisleadingFilenameIsRejected(string $filename): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.books.store'), [
            'title' => 'Misleading filename',
            'slug' => 'misleading-filename',
            'status' => 'available',
            'description' => 'Valid JPEG content with an executable-looking name.',
            'cover_file' => $this->rasterUpload($filename, 'jpeg'),
        ]);

        $response->assertSessionHasErrors('cover_file');
        $this->assertSame(0, Book::query()->count());
        $this->assertUploadDirectoryIsEmpty();
    }

    private function assertUploadDirectoryIsEmpty(): void
    {
        $directory = public_path('images/uploads');

        $this->assertFalse(File::isDirectory($directory) && File::files($directory) !== []);
    }
}
