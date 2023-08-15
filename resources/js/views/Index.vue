<script setup>
import axios from 'axios'

const user = await axios
  .get('/api/user')
  .then((response) => response?.data)
  .catch(() => null)
</script>

<template>
  <div class="flex justify-center">
    <div class="max-w-md w-full rounded overflow-hidden shadow-lg my-6 mx-4 md:mx-auto">
      <div class="font-bold text-xl px-4 py-6">ログインユーザ情報</div>
      <template v-if="user !== null">
        <div class="border-y border-gray-100">
          <dl class="divide-y divide-gray-100">
            <div class="px-4 py-6 sm:grid sm:grid-cols-3">
              <dt class="text-sm font-medium leading-6 text-gray-900">ユーザ名</dt>
              <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                {{ user.name }}
              </dd>
            </div>
            <div class="px-4 py-6 sm:grid sm:grid-cols-3">
              <dt class="text-sm font-medium leading-6 text-gray-900">メールアドレス</dt>
              <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                {{ user.email }}
              </dd>
            </div>
          </dl>
        </div>
      </template>

      <div class="flex justify-center my-4">
        <a
          v-if="user !== null"
          href="/logout"
          class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
          ログアウト
        </a>
        <a
          v-else
          href="/auth/redirect"
          class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
          ログイン
        </a>
      </div>
    </div>
  </div>
</template>
