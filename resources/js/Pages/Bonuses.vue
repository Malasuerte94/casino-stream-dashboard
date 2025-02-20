<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import BonusLists from '@/Components/BonusLists.vue';

const verifyComandStreamlabs = () => {
  const host = window.location.origin;
  return `{readapi.${host}/api/verify-yt-code/{user.name}/{1}}`;
};

const verifyComandNightBot = () => {
  const host = window.location.origin;
  return `$(urlfetch ${host}/api/verify-yt-code/$(user)/$(1))`;
};

const copyText = (text) => {
  if (navigator.clipboard) {
    navigator.clipboard
        .writeText(text)
        .then(() => {
          alert("Comanda a fost copiată în clipboard");
        })
        .catch((err) => {
          console.error("Failed to copy text: ", err);
        });
  } else {
    // Fallback for older browsers
    const input = document.createElement("input");
    input.value = text;
    document.body.appendChild(input);
    input.select();
    try {
      document.execCommand("copy");
      console.log("Copied to clipboard");
    } catch (err) {
      console.error("Failed to copy text: ", err);
    }
    document.body.removeChild(input);
  }
};
</script>


<template>
  <AppLayout title="Bonuses">
    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <transition name="fade">
          <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 text-gray-300 flex flex-row gap-4">
                <div class="mb-4 text-sm">
                  Comanda pentru useri Streamlabs: !verific
                  <button
                      class="w-full text-left p-2 bg-green-600 text-gray-900 rounded focus:outline-none hover:bg-green-500 transition"
                      @click="copyText(verifyComandStreamlabs())"
                  >
                    <code>{{ verifyComandStreamlabs() }}</code>
                  </button>
                </div>

                <div class="mb-4 text-sm">
                  Comanda pentru useri Nightbot: !verific
                  <button
                      class="w-full text-left p-2 bg-green-600 text-gray-900 rounded focus:outline-none hover:bg-green-500 transition"
                      @click="copyText(verifyComandNightBot())"
                  >
                    <code>{{ verifyComandNightBot() }}</code>
                  </button>
                </div>
            </div>
            <BonusLists />
          </div>
        </transition>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Fade Transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
