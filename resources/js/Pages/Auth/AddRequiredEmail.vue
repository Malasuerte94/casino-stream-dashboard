<template>
  <Head title="Please add your email address" />
  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>
    <form @submit.prevent="submit">
      <div>
        <InputLabel for="email" value="Email (pentru finalizarea contului)" />
        <TextInput
            id="email"
            v-model="form.email"
            type="email"
            class="mt-1 block w-full"
            required
            autofocus
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>
      <div class="flex items-center justify-end mt-4">
        <PrimaryButton class="w-100 w-full text-center center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Finalizare și Logare
        </PrimaryButton>
      </div>
    </form>
  </AuthenticationCard>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';


const props = defineProps({
  socialId: Number,
});

const form = useForm({
  email: '',
  remember: true,
  social: props.socialId
});

const submit = () => {
  form.post(route('add-required-email-to-account'), {
    onFinish: () => route('login'),
  });
};
</script>
