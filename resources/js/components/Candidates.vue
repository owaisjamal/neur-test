<template>
  <div>
    <!-- Loading overlay -->
    <div v-if="loading" class="loading-overlay">Loading...</div>

    <div class="p-10">
      <h1 class="text-4xl font-bold">Candidates</h1>
    </div>
    <div
      class="p-10 grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-5"
    >
      <div
        v-for="candidate in candidates"
        :key="candidate.id"
        class="rounded overflow-hidden shadow-lg"
        v-if="!hasWordPressSkill(candidate.strengths)"
      >
        <img class="w-full" src="/avatar.png" alt="" />
        <div class="px-6 py-4">
          <div class="font-bold text-xl mb-2">{{ candidate.name }}</div>
          <p class="text-gray-700 text-base">{{ candidate.description }}</p>
        </div>
        <div class="px-6 pt-4 pb-2">
          <span
            v-for="strength in JSON.parse(candidate.strengths)"
            :key="strength"
            :class="{
              'inline-block bg-green-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2':
                isDesiredStrength(strength),
              'inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2':
                !isDesiredStrength(strength),
            }"
          >
            {{ strength }}
          </span>
        </div>
        <div class="px-6 pb-2">
          <span
            v-for="skill in JSON.parse(candidate.soft_skills)"
            :key="skill"
            :class="{
              'inline-block bg-green-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2':
                isDesiredSoftSkill(skill),
              'inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2':
                !isDesiredSoftSkill(skill),
            }"
          >
            {{ skill }}
          </span>
        </div>
        <div class="p-6 float-right">
          <button
            v-if="!candidate.hired"
            @click="contactCandidate(candidate.id)"
            class="bg-white hover:bg-gray-100 text-gray-800 font-semibold py-2 px-4 border border-gray-400 rounded shadow"
          >
            Contact
          </button>

          <span v-if="candidate.hired" class="text-green-500 font-semibold">
            Hired
          </span>

          <button
            v-if="!candidate.hired"
            @click="hireCandidate(candidate.id)"
            :class="{ 'bg-gray-300 cursor-not-allowed': candidate.hired }"
            class="bg-white hover:bg-gray-100 text-gray-800 font-semibold py-2 px-4 border border-gray-400 hover:bg-teal-100 rounded shadow"
          >
            {{ candidate.hired ? "Hired" : "Hire" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["candidates"],
  data() {
    return {
      loading: false,
      desiredStrengths: ["Vue.js", "Laravel", "PHP", "TailwindCSS"],
      desiredSoftSkills: ["Diplomacy", "Team player"],
    };
  },
  methods: {
    isDesiredStrength(strength) {
      return this.desiredStrengths.includes(strength);
    },
    isDesiredSoftSkill(skill) {
      return this.desiredSoftSkills.includes(skill);
    },
    hasWordPressSkill(strengths) {
      return JSON.parse(strengths).includes("Wordpress");
    },
    sendEmailToCandidate(candidate, hired = false) {
      // Implement email sending logic here
      console.log(
        `Sending email to ${candidate.email}. Candidate ${
          hired ? "hired" : "contacted"
        }.`
      );
    },
    chargeCompanyCoins(amount) {
      // Implement logic to charge company coins here
      console.log(`Charging company ${amount} coins.`);
    },
    putBackCoinsInWallet(amount) {
      // Implement logic to put back coins in the wallet here
      console.log(`Putting back ${amount} coins in the wallet.`);
    },

    async contactCandidate(candidateId) {
      const candidate = this.getCandidateById(candidateId);
      if (!candidate) return;

      try {
        this.loading = true; // Show loading overlay
        const response = await this.axios.post(`/api/contact/${candidateId}`);
        console.log(response.data);

        // Check if the response has a 'coins' property
        if ("coins" in response.data) {
          // Update the wallet coins if the 'coins' property is present
          this.updateWalletCoins(response.data.coins);
        }

        this.$toast.success("Candidate contacted successfully");
        // Optionally, update the candidate object or reload the data from the server
      } catch (error) {
        // ... (existing error handling code)
      } finally {
        this.loading = false; // Hide loading overlay
      }
    },

    async hireCandidate(candidateId) {
      const candidate = this.getCandidateById(candidateId);
      if (!candidate) return;

      try {
        this.loading = true; // Show loading overlay
        const response = await this.axios.post(`/api/hire/${candidateId}`);
        console.log(response.data);

        // Check if the response has a 'coins' property
        if ("coins" in response.data) {
          // Update the wallet coins if the 'coins' property is present
          this.updateWalletCoins(response.data.coins);
        }

        // Set the 'hired' property of the candidate to true
        this.$set(candidate, "hired", true);

        this.$toast.success("Candidate hired successfully");
        // Optionally, update the candidate object or reload the data from the server
      } catch (error) {
        // Check if the error has a 'response' property and 'data' property
        if (error.response && error.response.data) {
          const { message } = error.response.data;

          // Handle specific cases based on the error message
          if (message === "Contact a candidate first.") {
            // Show a custom message when trying to hire a candidate who hasn't been contacted
            this.$toast.error(message);
          } else {
            // Handle other error messages
            this.$toast.error(message);
          }
        } else {
          // Handle other types of errors
          this.$toast.error("An unexpected error occurred.");
        }
      } finally {
        this.loading = false; // Hide loading overlay
      }
    },

    getCandidateById(candidateId) {
      // Implement a method to get a candidate by ID from the candidates array
      return this.candidates.find((candidate) => candidate.id === candidateId);
    },
    updateWalletCoins(coins) {
      // Find the wallet element and update the coins
      const walletElement = document.getElementById("wallet-coins");
      if (walletElement) {
        walletElement.innerText = `Your wallet has: ${coins} coins`;
      }
    },
  },
};
</script>

<style scoped>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.5em;
}
</style>
