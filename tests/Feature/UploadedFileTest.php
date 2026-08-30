<?php

namespace Tests\Feature;

use App\Models\UploadedFile as UploadedFileModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class UploadedFileTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin_tu',
        ]));
    }

    public function test_index_page_loads(): void
    {
        $this->actingAsAdmin();
        $response = $this->get(route('admin.uploaded-files'));
        $response->assertStatus(200);
        $response->assertSee('Upload File');
    }

    public function test_create_page_loads(): void
    {
        $this->actingAsAdmin();
        $response = $this->get(route('admin.uploaded-files.create'));
        $response->assertStatus(200);
    }

    public function test_store_requires_all_fields(): void
    {
        $this->actingAsAdmin();

        Livewire::test('admin.create-uploaded-file')
            ->set('title', '')
            ->set('upload_date', '')
            ->set('file', null)
            ->call('save')
            ->assertHasErrors(['title', 'upload_date', 'file']);

        $this->assertDatabaseCount('uploaded_files', 0);
    }

    public function test_store_pdf_works(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        Livewire::test('admin.create-uploaded-file')
            ->set('title', 'Dokumen Rapat')
            ->set('upload_date', '2026-08-30')
            ->set('file', UploadedFile::fake()->create('dokumen.pdf', 200, 'application/pdf'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.uploaded-files'));

        $this->assertDatabaseHas('uploaded_files', ['title' => 'Dokumen Rapat', 'mime_type' => 'application/pdf']);
        Storage::disk('public')->assertExists(UploadedFileModel::first()->file_path);
    }

    public function test_store_image_works(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        Livewire::test('admin.create-uploaded-file')
            ->set('title', 'Foto Kegiatan')
            ->set('upload_date', '2026-08-30')
            ->set('file', UploadedFile::fake()->create('foto.jpg', 200, 'image/jpeg'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.uploaded-files'));

        $this->assertDatabaseHas('uploaded_files', ['title' => 'Foto Kegiatan']);
        $this->assertTrue(UploadedFileModel::first()->isImage());
    }

    public function test_store_rejects_non_image_pdf(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        Livewire::test('admin.create-uploaded-file')
            ->set('title', 'Virus')
            ->set('upload_date', '2026-08-30')
            ->set('file', UploadedFile::fake()->create('virus.exe', 200, 'application/octet-stream'))
            ->call('save')
            ->assertHasErrors('file');

        $this->assertDatabaseCount('uploaded_files', 0);
    }

    public function test_view_file_returns_inline_response(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $file = UploadedFileModel::create([
            'title' => 'Dokumen',
            'upload_date' => '2026-08-30',
            'file_path' => 'uploaded-files/test.pdf',
            'file_name' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 123,
        ]);

        Storage::disk('public')->put('uploaded-files/test.pdf', 'dummy');

        $response = $this->get(route('admin.uploaded-files.view', $file));
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'inline; filename="test.pdf"');
    }

    public function test_edit_allows_keeping_old_file(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $file = UploadedFileModel::create([
            'title' => 'Lama',
            'upload_date' => '2026-08-30',
            'file_path' => 'uploaded-files/old.pdf',
            'file_name' => 'old.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 123,
        ]);

        Livewire::test('admin.edit-uploaded-file', ['id' => $file->id])
            ->set('title', 'Baru')
            ->set('upload_date', '2026-08-31')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.uploaded-files'));

        $file->refresh();
        $this->assertEquals('Baru', $file->title);
        $this->assertEquals('uploaded-files/old.pdf', $file->file_path);
    }

    public function test_edit_replaces_file_and_deletes_old(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        Storage::disk('public')->put('uploaded-files/old.pdf', 'dummy');
        $file = UploadedFileModel::create([
            'title' => 'Lama',
            'upload_date' => '2026-08-30',
            'file_path' => 'uploaded-files/old.pdf',
            'file_name' => 'old.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 123,
        ]);

        Livewire::test('admin.edit-uploaded-file', ['id' => $file->id])
            ->set('title', 'Baru')
            ->set('upload_date', '2026-08-31')
            ->set('file', UploadedFile::fake()->create('new.pdf', 200, 'application/pdf'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.uploaded-files'));

        $file->refresh();
        $this->assertNotEquals('uploaded-files/old.pdf', $file->file_path);
        Storage::disk('public')->assertMissing('uploaded-files/old.pdf');
        Storage::disk('public')->assertExists($file->file_path);
    }

    public function test_delete_removes_record_and_file(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $file = UploadedFileModel::create([
            'title' => 'Hapus',
            'upload_date' => '2026-08-30',
            'file_path' => 'uploaded-files/del.pdf',
            'file_name' => 'del.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 123,
        ]);

        Storage::disk('public')->put('uploaded-files/del.pdf', 'dummy');

        Livewire::test('admin.uploaded-file-table')
            ->call('delete', $file->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('uploaded_files', ['id' => $file->id]);
        Storage::disk('public')->assertMissing('uploaded-files/del.pdf');
    }

    public function test_guest_cannot_access(): void
    {
        $response = $this->get(route('admin.uploaded-files'));
        $response->assertRedirect(route('login'));
    }
}
