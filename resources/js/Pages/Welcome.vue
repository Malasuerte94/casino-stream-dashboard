<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SvgLogo from "/public/storage/assets/branding/logo-long-white.svg";
import Register from "@/Pages/Auth/Register.vue";

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
  <div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="py-4 backdrop-blur-xl bg-white/10 shadow-lg shadow-black/10">
      <div class="max-w-6xl mx-auto px-4 flex items-center justify-between">
        <SvgLogo class="w-[150px] h-auto text-gray-500 stroke-current" />
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center">
      <div class="text-center">
        <!-- If user is logged in -->
        <div v-if="$page.props.user.id" class="flex gap-6 justify-center">
          <!-- Display Dashboard Streamer only if user is a streamer -->
          <Link
              v-if="$page.props.user_streamer"
              :href="route('streamer.dashboard')"
              class="text-lg px-4 py-2 border border-gray-600 rounded transition transform duration-200 ease-in-out hover:bg-gray-700 hover:border-gray-500 hover:scale-105"
          >
            Dashboard Streamer
          </Link>
          <Link
              :href="route('user.dashboard')"
              class="text-lg px-4 py-2 border border-gray-600 rounded transition transform duration-200 ease-in-out hover:bg-gray-700 hover:border-gray-500 hover:scale-105"
          >
            Dashboard Viewer
          </Link>
        </div>

        <!-- If user is not logged in -->
        <div v-else class="flex gap-6 justify-center">
          <AuthenticationCard>
            <!-- Optional Status Message -->
            <div v-if="status" class="mb-4 text-center text-green-600 font-medium text-sm">
              {{ status }}
            </div>

            <!-- Login Form -->
            <form @submit.prevent="submit" class="space-y-6">
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
                  Log in
                </PrimaryButton>
                <a href="/register" class="btn-secondary w-full sm:w-auto text-center">
                  Înregistrare
                </a>
              </div>

              <!-- Additional Registration Options -->
              <div class="mt-8 text-center text-gray-400">
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-4">
                  <a href="/auth/google/redirect" class="btn-secondary w-full sm:w-auto text-center google-register">
                    Autentificare cu Google
                  </a>
                </div>
              </div>
            </form>
          </AuthenticationCard>
          <Register />
        </div>
      </div>
    </main>
  </div>
</template>
