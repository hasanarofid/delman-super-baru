<?php

namespace Tests\Feature;

use App\User;
use App\Models\RencanaKerjaT;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerencanaanPdfTest extends TestCase
{
    /**
     * Test PDF export route returns success for supervisor.
     *
     * @return void
     */
    public function test_supervisor_can_export_perencanaan_to_pdf()
    {
        $supervisor = User::where('role', 'Pengawas')->first();
        if (!$supervisor) {
            $supervisor = factory(User::class)->create(['role' => 'Pengawas']);
        }

        $response = $this->actingAs($supervisor)->get(route('pengawas.perencanaan.exportPDF'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
