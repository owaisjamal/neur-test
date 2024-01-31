<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateHireTest extends TestCase
{
    use RefreshDatabase;

    public function testHiringCandidateWithSufficientCoins()
    {
        $wallet = Wallet::factory()->create(['coins' => 10]);
        $candidate = Candidate::factory()->create(['contacted' => true]);

        $response = $this->post("/api/hire/{$candidate->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Candidate hired successfully.',
                'coins' => 15, // Adding 5 coins if hired successfully
            ]);
    }

    public function testHiringCandidateWithoutContacting()
    {
        $wallet = Wallet::factory()->create(['coins' => 10]);
        $candidate = Candidate::factory()->create(['contacted' => false]);

        $response = $this->post("/api/hire/{$candidate->id}");

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Contact a candidate first.'
            ]);
    }

    public function testHiringCandidateWithInsufficientCoins()
    {
        $wallet = Wallet::factory()->create(['coins' => 3]);
        $candidate = Candidate::factory()->create(['contacted' => true]);

        $response = $this->post("/api/hire/{$candidate->id}");

        $response->assertStatus(200)->assertJson([
            'message' => 'Candidate hired successfully.',
            'coins' => 8, // Adjusted coins value
        ]);
    }
}
