<script setup lang="ts">
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Bot, Send, Home } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

type ChatMessage = { role: 'system' | 'user' | 'assistant'; content: string; createdAt?: string };

const time = () => new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

const messages = ref<ChatMessage[]>([
  { role: 'assistant', content: 'Xin chào! Tôi là trợ lý AI. Hãy hỏi tôi về việc làm, công ty hoặc CV.', createdAt: time() }
]);
const sending = ref(false);
const text = ref('');
const quickPrompts = [
  'Gợi ý mẫu CV cho vị trí Frontend',
  'Tư vấn lộ trình trở thành Backend Developer',
  'Những công ty phù hợp với người mới ra trường',
  'Mẹo phỏng vấn vị trí Data Engineer',
  'Gợi ý việc phù hợp với CV của tôi',
];

const csrfToken = () => {
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.getAttribute('content') || '' : '';
};


const send = async () => {
  const t = text.value.trim();
  if (!t || sending.value) return;
  messages.value.push({ role: 'user', content: t, createdAt: time() });
  text.value = '';
  sending.value = true;
  const payload = {
    messages: [...messages.value],
  };
  try {
    const res = await fetch('/ai/chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
      },
      body: JSON.stringify(payload),
    });
    const data = await res.json();
    messages.value.push({ role: 'assistant', content: data.reply || 'Xin lỗi, AI hiện không phản hồi.', createdAt: time() });
  } catch {
    messages.value.push({ role: 'assistant', content: 'Có lỗi xảy ra. Vui lòng thử lại.', createdAt: time() });
  } finally {
    sending.value = false;
    await nextTick();
    const box = document.querySelector('.ai-chat-page-messages') as HTMLElement | null;
    if (box) box.scrollTop = box.scrollHeight;
  }
};

const choosePrompt = async (p: string) => {
  text.value = p;
  await nextTick();
  send();
};
</script>

<template>
  <ClientLayout title="Chat với Trợ lý AI" fullscreen>
    <div class="fixed inset-0">
      <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-primary/10 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 h-96 w-96 rounded-full bg-orange-100/40 blur-3xl"></div>
      </div>

      <div class="fixed top-0 left-0 right-0 z-10 border-b bg-background/95 px-6 py-4 backdrop-blur supports-[backdrop-filter]:bg-background/80">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center">
          <div class="flex items-center gap-3">
            <div class="grid h-9 w-9 place-items-center rounded-lg bg-primary text-primary-foreground shadow"><Bot class="h-5 w-5" /></div>
            <div>
              <p class="text-sm font-semibold">Trợ lý AI</p>
              <p class="text-xs text-muted-foreground">Tư vấn thông minh về việc làm IT, CV và phỏng vấn</p>
            </div>
          </div>
          <Link href="/" aria-label="Về trang chủ" class="mx-auto inline-flex h-9 w-9 items-center justify-center rounded-full border text-muted-foreground transition hover:border-primary hover:text-primary">
            <Home class="h-5 w-5" />
          </Link>
          <div class="flex justify-end">
            <Badge class="rounded-full">Online</Badge>
          </div>
        </div>
      </div>

      <div class="absolute inset-x-0 top-[72px] bottom-[92px] overflow-y-auto px-6 py-6 ai-chat-page-messages space-y-6">
        <div v-for="(m, i) in messages" :key="i" class="flex" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
          <div class="max-w-[75%]">
            <div class="mb-1 flex items-center gap-2 text-xs text-muted-foreground" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
              <div v-if="m.role === 'assistant'" class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary"><Bot class="h-3 w-3" /></div>
              <span>{{ m.role === 'assistant' ? 'Expert' : 'Bạn' }}</span>
              <span>•</span>
              <span>{{ m.createdAt }}</span>
            </div>
            <div :class="[
              'rounded-2xl px-4 py-2.5 text-sm shadow-sm',
              m.role === 'user' ? 'bg-gradient-to-br from-primary to-primary/80 text-primary-foreground rounded-br-md' : 'border border-border bg-white rounded-bl-md',
            ]">
              {{ m.content }}
            </div>
          </div>
        </div>
        <div v-if="sending" class="flex justify-start">
          <div class="max-w-[75%]">
            <div class="mb-1 flex items-center gap-2 text-xs text-muted-foreground">
              <div class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary"><Bot class="h-3 w-3" /></div>
              <span>Expert</span>
              <span>•</span>
              <span>{{ time() }}</span>
            </div>
            <div class="inline-flex items-center gap-1 rounded-2xl border bg-white px-4 py-2.5 text-sm shadow-sm">
              <div class="h-2 w-2 animate-bounce rounded-full bg-muted-foreground/70"></div>
              <div class="h-2 w-2 animate-bounce rounded-full bg-muted-foreground/70 [animation-delay:120ms]"></div>
              <div class="h-2 w-2 animate-bounce rounded-full bg-muted-foreground/70 [animation-delay:240ms]"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="fixed bottom-0 left-0 right-0 z-10 border-t bg-background/95 px-6 py-4 backdrop-blur supports-[backdrop-filter]:bg-background/80">
        <div class="mb-3 flex flex-wrap gap-2">
          <button v-for="p in quickPrompts" :key="p" @click="choosePrompt(p)" class="rounded-full border px-3 py-1.5 text-xs text-muted-foreground transition hover:border-primary hover:text-primary">{{ p }}</button>
        </div>
        <div class="grid grid-cols-[1fr_auto] gap-2">
          <Input v-model="text" :disabled="sending" @keydown.enter.prevent="send" placeholder="Hỏi về việc làm, CV, phỏng vấn..." class="rounded-full border-2" />
          <Button :disabled="!text.trim() || sending" @click="send" size="icon" class="h-10 w-10 rounded-full bg-black text-white hover:bg-black/90">
            <Send v-if="!sending" class="h-4 w-4" />
            <div v-else class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></div>
          </Button>
        </div>
      </div>
    </div>
  </ClientLayout>
</template>

<style scoped>
.ai-chat-page-messages { scroll-behavior: smooth; scrollbar-width: thin; }
.ai-chat-page-messages::-webkit-scrollbar { width: 6px; }
.ai-chat-page-messages::-webkit-scrollbar-thumb { background: hsl(var(--muted-foreground) / 0.25); border-radius: 3px; }
</style>
