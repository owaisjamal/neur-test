<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateContactTest extends TestCase
{
    use RefreshDatabase;
    public function testContactingCandidateWithSufficientCoins()
    {
        $wallet = Wallet::factory()->create(['coins' => 10]);
        $candidate = Candidate::factory()->create();

        $response = $this->post("/api/contact/{$candidate->id}");

        // dd($response->json());

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Candidate contacted successfully.',
                'coins' => 5, // Adjusted coins value
            ]);

        $this->assertEquals(5, $wallet->fresh()->coins);
    }



    public function testContactingCandidateWithInsufficientCoins()
    {
        $wallet = Wallet::factory()->create(['coins' => 3]);
        $candidate = Candidate::factory()->create();

        $response = $this->post("/api/contact/{$candidate->id}");
        $response->assertStatus(403)->assertJson([
            'message' => 'Please add more coins to contact candidate.',
            'coins' => 3, // Adjusted coins value
        ]);
    }

    public function testContactingNonExistentCandidate()
    {
        $response = $this->post("/api/contact/999");

        $response->assertStatus(400)->assertJson([
            'message' => 'Unable to contact candidate.',
        ]);
    }
}
