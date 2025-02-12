<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  user: Object,
});

// We keep these reactive objects for two-way binding.
// They are no longer using Inertia’s useForm for submission.
const profileForm = ref({
  name: props.user.name,
  email: props.user.email,
  photo: null,
});
const discordWebhookFormSchedule = ref({
  discordWebhook: '', // initial value for schedule webhook
});
const discordWebhookFormHuntBuyBattle = ref({
  discordWebhook: '', // initial value for hunt-buy-battle webhook
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

// Update profile information and upload the profile photo using axios.
const updateProfileInformation = () => {
  // Create a FormData object to support file uploads.
  const formData = new FormData();
  formData.append('name', profileForm.value.name);
  formData.append('email', profileForm.value.email);

  if (photoInput.value && photoInput.value.files[0]) {
    formData.append('photo', photoInput.value.files[0]);
  }

  axios.post('/api/user/profile-picture', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
      .then(response => {
        // Clear file input on success.
        clearPhotoFileInput();
        alert('Profile picture updated!');
        console.log('Profile updated:', response.data);
      })
      .catch(error => {
        console.error('Failed to update profile:', error);
        // You might want to add additional error handling here.
      });
};

const saveDiscordWebhookSchedule = () => {
  axios.post('/api/user-settings/save-discord-webhook', {
    discordWebhook: discordWebhookFormSchedule.value.discordWebhook
  })
      .then(response => {
        alert('Discord webhook saved successfully!');
        console.log('Webhook schedule:', response.data);
      })
      .catch(error => {
        alert('Failed to save the webhook. Please try again.');
        console.error(error);
      });
};

const saveDiscordWebhookHuntBuyBattle = () => {
  axios.post('/api/user-settings/save-discord-webhook/hunt-buy-battle', {
    discordWebhook: discordWebhookFormHuntBuyBattle.value.discordWebhook
  })
      .then(response => {
        alert('Discord webhook saved successfully!');
        console.log('Webhook hunt-buy-battle:', response.data);
      })
      .catch(error => {
        alert('Failed to save the webhook. Please try again.');
        console.error(error);
      });
};

const sendEmailVerification = () => {
  verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
  photoInput.value.click();
};

const updatePhotoPreview = () => {
  const photo = photoInput.value.files[0];
  if (!photo) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    photoPreview.value = e.target.result;
  };
  reader.readAsDataURL(photo);
};

const deletePhoto = () => {
  // If you have an endpoint for deleting the photo, you could also change this to axios.
  router.delete(route('current-user-photo.destroy'), {
    preserveScroll: true,
    onSuccess: () => {
      photoPreview.value = null;
      clearPhotoFileInput();
    },
  });
};

const clearPhotoFileInput = () => {
  if (photoInput.value?.value) {
    photoInput.value.value = null;
  }
};
</script>

<template>
  <div class="flex gap-2 w-full">
    <!-- Discord Webhook Schedule Form -->
    <div class="bg-gray-700 shadow rounded-lg p-6 mb-6 w-1/3">
      <h3 class="text-lg font-semibold text-gray-100 mb-4">Discord Webhook - Schedule</h3>
      <div class="flex items-center gap-4">
        <div class="flex-1">
          <TextInput
              id="discordWebhookSchedule"
              v-model="discordWebhookFormSchedule.discordWebhook"
              type="text"
              placeholder="Enter Discord Webhook URL"
              class="mt-1 block w-full"
          />
        </div>
        <PrimaryButton
            @click="saveDiscordWebhookSchedule"
        >
          Save
        </PrimaryButton>
      </div>
      <ActionMessage :on="false" class="mt-3">
        Webhook Saved.
      </ActionMessage>
      <InputError :message="discordWebhookFormSchedule.errors" class="mt-2"/>
    </div>

    <!-- Discord Webhook Hunt/Buy/Battle Form -->
    <div class="bg-gray-700 shadow rounded-lg p-6 mb-6 w-1/3">
      <h3 class="text-lg font-semibold text-gray-100 mb-4">Discord Webhook - Hunt / Buy / Battle</h3>
      <div class="flex items-center gap-4">
        <div class="flex-1">
          <TextInput
              id="discordWebhookHuntBuyBattle"
              v-model="discordWebhookFormHuntBuyBattle.discordWebhook"
              type="text"
              placeholder="Enter Discord Webhook URL"
              class="mt-1 block w-full"
          />
        </div>
        <PrimaryButton
            @click="saveDiscordWebhookHuntBuyBattle"
        >
          Save
        </PrimaryButton>
      </div>
      <ActionMessage :on="false" class="mt-3">
        Webhook Saved.
      </ActionMessage>
      <InputError :message="discordWebhookFormHuntBuyBattle.errors" class="mt-2"/>
    </div>
  </div>

  <!-- Profile Information Form Section -->
  <FormSection @submitted="updateProfileInformation">
    <template #title>
      Profile Information
    </template>
    <template #description>
      Update your account's profile information and email address.
    </template>
    <template #form>
      <!-- Profile Photo -->
      <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
        <input
            ref="photoInput"
            type="file"
            class="hidden"
            @change="updatePhotoPreview"
        />
        <InputLabel for="photo" value="Photo"/>
        <div v-show="!photoPreview" class="mt-2">
          <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
        </div>
        <div v-show="photoPreview" class="mt-2">
          <span
              class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
              :style="'background-image: url(\'' + photoPreview + '\');'"
          />
        </div>
        <SecondaryButton class="mt-2 mr-2" type="button" @click.prevent="selectNewPhoto">
          Select A New Photo
        </SecondaryButton>
        <SecondaryButton
            v-if="user.profile_photo_path"
            type="button"
            class="mt-2"
            @click.prevent="deletePhoto"
        >
          Remove Photo
        </SecondaryButton>
        <InputError :message="profileForm.errors" class="mt-2"/>
      </div>
      <!-- Name -->
      <div class="col-span-6 sm:col-span-4">
        <InputLabel for="name" value="Name"/>
        <TextInput
            id="name"
            v-model="profileForm.name"
            type="text"
            class="mt-1 block w-full"
            autocomplete="name"
        />
        <InputError :message="profileForm.errors" class="mt-2"/>
      </div>
      <!-- Email -->
      <div class="col-span-6 sm:col-span-4">
        <InputLabel for="email" value="Email"/>
        <TextInput
            id="email"
            v-model="profileForm.email"
            type="email"
            class="mt-1 block w-full"
        />
        <InputError :message="profileForm.errors" class="mt-2"/>
        <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
          <p class="text-sm mt-2">
            Your email address is unverified.
            <Link
                :href="route('verification.send')"
                method="post"
                as="button"
                class="underline text-gray-600 hover:text-gray-900"
                @click.prevent="sendEmailVerification"
            >
              Click here to re-send the verification email.
            </Link>
          </p>
          <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
            A new verification link has been sent to your email address.
          </div>
        </div>
      </div>
    </template>
    <template #actions>
      <ActionMessage :on="false" class="mr-3">
        Saved.
      </ActionMessage>
      <PrimaryButton>
        Save
      </PrimaryButton>
    </template>
  </FormSection>
</template>
