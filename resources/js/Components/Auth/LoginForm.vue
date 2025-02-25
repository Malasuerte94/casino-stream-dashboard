<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String,
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.transform(data => ({
    ...data,
    remember: form.remember ? 'on' : '',
  })).post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>
<template>
  <AuthenticationCard>
    <slot name="logo">
      <h2 class="text-xl font-bold uppercase mb-2 text-left">Login</h2>
    </slot>
    <form @submit.prevent="submit" class="space-y-4 text-left">
      <!-- Email Field -->
      <div class="space-y-2">
        <InputLabel for="email" value="Email" class="text-white" />
        <TextInput
            id="email"
            v-model="form.email"
            type="email"
            required
            autofocus
            class="mt-1 block w-full rounded-md border border-gray-600 bg-gray-700 text-gray-100 focus:ring-indigo-500 focus:border-indigo-500"
        />
        <InputError class="mt-1" :message="form.errors.email" />
      </div>

      <!-- Password Field -->
      <div class="space-y-2">
        <InputLabel for="password" value="Password" class="text-white" />
        <TextInput
            id="password"
            v-model="form.password"
            type="password"
            required
            autocomplete="current-password"
            class="mt-1 block w-full rounded-md border border-gray-600 bg-gray-700 text-gray-100 focus:ring-indigo-500 focus:border-indigo-500"
        />
        <InputError class="mt-1" :message="form.errors.password" />
      </div>

      <!-- Remember Me & Forgot Password -->
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <Checkbox v-model:checked="form.remember" name="remember" class="mr-2" />
          <span class="text-sm text-gray-300">Remember me</span>
        </div>
        <Link
            v-if="canResetPassword"
            :href="route('password.request')"
            class="text-sm text-gray-600 hover:text-gray-900 underline"
        >
          Ai uitat parola?
        </Link>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <PrimaryButton
            class="btn-primary w-full sm:w-auto"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
        >
          Autentificare
        </PrimaryButton>
        <a href="/auth/google/redirect" class="btn-secondary w-full sm:w-auto text-center google-register flex-nowrap whitespace-nowrap">
          Autentificare cu Google
        </a>
      </div>
    </form>
  </AuthenticationCard>
</template>