<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { MessageCircle, X, Send, Minimize2, Maximize2, Check, CheckCheck } from 'lucide-vue-next';
import axios from 'axios';

const page = usePage();

interface Message {
    id: number;
    sender_id: number;
    receiver_id: number;
    message: string;
    is_read: boolean;
    created_at: string;
    sender: {
        id: number;
        name: string;
        avatar: string | null;
    };
    status?: 'sending' | 'sent' | 'delivered' | 'read';
}

const isOpen = ref(false);
const isMinimized = ref(false);
const messages = ref<Message[]>([]);
const newMessage = ref('');
const loading = ref(false);
const sending = ref(false);
const unreadCount = ref(0);
const isTyping = ref(false);
const typingTimeout = ref<NodeJS.Timeout | null>(null);

const currentUser = computed(() => {
    return (page.props.auth as any)?.user;
});

const isAuthenticated = computed(() => {
    return !!currentUser.value;
});

const ADMIN_ID = 1;

// Load/save chat state from localStorage
const loadChatState = () => {
    try {
        const saved = localStorage.getItem('chat_widget_state');
        if (saved) {
            const state = JSON.parse(saved);
            isOpen.value = state.isOpen || false;
            isMinimized.value = state.isMinimized || false;
        }
    } catch (error) {
        console.error('Error loading chat state:', error);
    }
};

const saveChatState = () => {
    try {
        localStorage.setItem('chat_widget_state', JSON.stringify({
            isOpen: isOpen.value,
            isMinimized: isMinimized.value,
        }));
    } catch (error) {
        console.error('Error saving chat state:', error);
    }
};

// Watch for state changes and save
watch([isOpen, isMinimized], () => {
    saveChatState();
});

async function fetchMessages() {
    if (!isAuthenticated.value) return;
    
    loading.value = true;
    try {
        const response = await axios.get(`/support/messages`);
        messages.value = (response.data.messages || []).map((msg: Message) => ({
            ...msg,
            status: msg.is_read ? 'read' : 'delivered'
        }));
        unreadCount.value = response.data.unread_count || 0;
    } catch (error) {
        console.error('[ChatWidget] Error fetching messages:', error);
    } finally {
        loading.value = false;
    }
}

async function sendMessage() {
    if (!newMessage.value.trim() || !isAuthenticated.value || sending.value) return;

    const messageText = newMessage.value.trim();
    const tempId = Date.now();
    
    // Optimistic update
    const tempMessage: Message = {
        id: tempId,
        sender_id: currentUser.value.id,
        receiver_id: ADMIN_ID,
        message: messageText,
        is_read: false,
        created_at: new Date().toISOString(),
        sender: {
            id: currentUser.value.id,
            name: currentUser.value.name,
            avatar: currentUser.value.avatar,
        },
        status: 'sending'
    };
    
    messages.value.push(tempMessage);
    newMessage.value = '';
    await nextTick();
    scrollToBottom();
    
    sending.value = true;
    
    try {
        const response = await axios.post('/support/send', {
            message: messageText,
        });
        
        // Update temp message with real data
        const index = messages.value.findIndex(m => m.id === tempId);
        if (index !== -1) {
            messages.value[index] = {
                ...response.data,
                status: 'sent'
            };
        }
        
        await nextTick();
        scrollToBottom();
    } catch (error) {
        console.error('[ChatWidget] Error sending message:', error);
        
        // Mark as failed
        const index = messages.value.findIndex(m => m.id === tempId);
        if (index !== -1) {
            messages.value[index].status = 'failed' as any;
        }
        
        // Show error notification
        alert('Failed to send message. Please try again.');
    } finally {
        sending.value = false;
    }
}

function scrollToBottom() {
    setTimeout(() => {
        const messagesContainer = document.querySelector('.chat-widget-messages');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }, 50);
}

function openChat() {
    isOpen.value = true;
    isMinimized.value = false;
    
    if (isAuthenticated.value) {
        fetchMessages();
        unreadCount.value = 0;
    }
    
    scrollToBottom();
}

function closeChat() {
    isOpen.value = false;
}

function toggleMinimize() {
    isMinimized.value = !isMinimized.value;
    if (!isMinimized.value) {
        scrollToBottom();
    }
}

function formatTime(dateString: string) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);

    if (minutes < 1) return 'Vừa xong';
    if (minutes < 60) return `${minutes} phút`;
    if (hours < 24) return `${hours} giờ`;
    if (days === 1) return 'Hôm qua';
    if (days < 7) return `${days} ngày`;
    
    return date.toLocaleDateString('vi-VN', { 
        day: '2-digit', 
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getUserInitials(name: string) {
    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

function getMessageStatusIcon(status?: string) {
    switch (status) {
        case 'sending':
            return '⏱️';
        case 'sent':
            return '✓';
        case 'delivered':
            return '✓✓';
        case 'read':
            return '✓✓';
        case 'failed':
            return '❌';
        default:
            return '';
    }
}

// Handle typing indicator
function handleTyping() {
    if (typingTimeout.value) {
        clearTimeout(typingTimeout.value);
    }
    
    // Could broadcast typing status here
    isTyping.value = true;
    
    typingTimeout.value = setTimeout(() => {
        isTyping.value = false;
    }, 1000);
}

// Keyboard shortcuts
function handleKeyDown(event: KeyboardEvent) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

// Listen to Echo events
onMounted(() => {
    // Load saved state
    loadChatState();
    
    // Fetch messages on mount
    if (isAuthenticated.value) {
        fetchMessages();
    }
    
    if (isAuthenticated.value && window.Echo) {
        const channelName = `chat.${currentUser.value.id}`;
        const channel = window.Echo.private(channelName);
        
        channel.listen('.message.sent', (e: any) => {
            // Only add if from another user (admin)
            if (e.sender_id !== currentUser.value?.id) {
                const incomingMessage = {
                    ...e,
                    status: 'delivered'
                };
                
                messages.value.push(incomingMessage);
                
                if (!isOpen.value || isMinimized.value) {
                    unreadCount.value++;
                    
                    // Play notification sound
                    playNotificationSound();
                    
                    // Show browser notification
                    showBrowserNotification(e.message);
                } else {
                    scrollToBottom();
                    
                    // Mark as read immediately if chat is open
                    markAsRead(e.id);
                }
            }
        });
        
        channel.subscribed(() => {
            console.log('[ChatWidget] ✅ Connected to chat channel');
        });
        
        channel.error((error: any) => {
            console.error('[ChatWidget] ❌ Channel error:', error);
        });
    }
});

onUnmounted(() => {
    if (isAuthenticated.value && window.Echo) {
        window.Echo.leave(`chat.${currentUser.value.id}`);
    }
    
    if (typingTimeout.value) {
        clearTimeout(typingTimeout.value);
    }
});

// Helper functions
function playNotificationSound() {
    try {
        const audio = new Audio('/sounds/notification.mp3');
        audio.volume = 0.3;
        audio.play().catch(() => {
            // Ignore if sound fails to play
        });
    } catch (error) {
        // Sound file not found, ignore
    }
}

function showBrowserNotification(message: string) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Tin nhắn mới từ hỗ trợ', {
            body: message.substring(0, 50) + (message.length > 50 ? '...' : ''),
            icon: '/favicon.svg',
            tag: 'chat-message'
        });
    }
}

async function markAsRead(messageId: number) {
    try {
        await axios.post(`/support/messages/${messageId}/read`);
    } catch (error) {
        console.error('[ChatWidget] Error marking message as read:', error);
    }
}

// Request notification permission on first interaction
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}
</script>

<template>
    <!-- Floating Chat Widget -->
    <div class="fixed bottom-6 right-6 z-50">
        <!-- Chat Window -->
        <Transition name="chat-pop">
            <Card 
                v-if="isOpen" 
                :class="[
                    'shadow-2xl border-2 transition-all duration-300 p-0',
                    isMinimized ? 'w-80 h-16' : 'w-[400px] h-[650px]'
                ]"
                class="flex flex-col overflow-hidden bg-background"
            >
                <!-- Header -->
                <div 
                    :class="[
                        'px-4 py-3.5 bg-gradient-to-br from-primary via-primary to-primary/90 text-white flex-shrink-0 shadow-lg',
                        isMinimized ? 'cursor-pointer hover:brightness-110' : ''
                    ]"
                    @click="isMinimized && toggleMinimize()"
                >
                    <div class="flex items-center gap-3">
                        <div class="relative flex-shrink-0">
                            <div class="h-11 w-11 rounded-full bg-white/95 flex items-center justify-center shadow-md">
                                <span class="text-primary font-bold text-base">HT</span>
                            </div>
                            <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-400 rounded-full border-2 border-white shadow-sm animate-pulse"></span>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-base leading-tight tracking-tight">Hỗ trợ khách hàng</h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                                <p class="text-xs text-white/90 font-medium">Trực tuyến - Phản hồi ngay</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-0.5 flex-shrink-0 ml-auto">
                            <button
                                @click.stop="toggleMinimize" 
                                class="h-9 w-9 rounded-lg flex items-center justify-center text-white hover:bg-white/15 active:bg-white/25 transition-all"
                                :title="isMinimized ? 'Mở rộng' : 'Thu nhỏ'"
                            >
                                <Minimize2 v-if="!isMinimized" :size="18" class="stroke-[2.5]" />
                                <Maximize2 v-else :size="18" class="stroke-[2.5]" />
                            </button>
                            <button
                                @click.stop="closeChat" 
                                class="h-9 w-9 rounded-lg flex items-center justify-center text-white hover:bg-white/15 active:bg-white/25 transition-all"
                                title="Đóng"
                            >
                                <X :size="20" class="stroke-[2.5]" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chat Content (Hidden when minimized) -->
                <template v-if="!isMinimized">
                    <div class="flex-1 flex flex-col overflow-hidden">
                        <!-- Not Authenticated -->
                        <div v-if="!isAuthenticated" class="flex-1 flex flex-col items-center justify-center p-6 text-center bg-gradient-to-b from-muted/30 to-background">
                            <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                                <MessageCircle :size="40" class="text-primary" />
                            </div>
                            <h4 class="font-semibold text-lg mb-2">Đăng nhập để chat</h4>
                            <p class="text-sm text-muted-foreground mb-6 max-w-xs">
                                Đăng nhập để nhận hỗ trợ trực tiếp từ đội ngũ chăm sóc khách hàng
                            </p>
                            <Button as="a" href="/login" class="rounded-full px-6">
                                Đăng nhập ngay
                            </Button>
                        </div>

                        <!-- Messages Area -->
                        <div v-else class="flex-1 flex flex-col overflow-hidden">
                            <!-- Messages Container -->
                            <div class="flex-1 overflow-y-auto p-4 space-y-3 chat-widget-messages bg-gradient-to-b from-muted/5 to-background">
                                <!-- Loading -->
                                <div v-if="loading" class="flex items-center justify-center py-12">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-8 h-8 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                                        <p class="text-sm text-muted-foreground">Đang tải...</p>
                                    </div>
                                </div>

                                <!-- Welcome Message -->
                                <div v-else-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-center py-12">
                                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                                        <MessageCircle :size="32" class="text-primary" />
                                    </div>
                                    <h4 class="font-semibold text-base mb-2">Chào mừng bạn! 👋</h4>
                                    <p class="text-sm text-muted-foreground max-w-[280px]">
                                        Chúng tôi luôn sẵn sàng hỗ trợ bạn. Hãy gửi tin nhắn cho chúng tôi!
                                    </p>
                                </div>

                                <!-- Message List -->
                                <div
                                    v-for="message in messages"
                                    :key="message.id"
                                    :class="[
                                        'flex gap-2 items-end animate-in fade-in slide-in-from-bottom-2 duration-200',
                                        message.sender_id === currentUser?.id ? 'flex-row-reverse' : 'flex-row'
                                    ]"
                                >
                                    <!-- Avatar -->
                                    <Avatar class="h-8 w-8 flex-shrink-0">
                                        <AvatarImage v-if="message.sender.avatar" :src="message.sender.avatar" />
                                        <AvatarFallback :class="[
                                            'text-xs font-semibold',
                                            message.sender_id === ADMIN_ID 
                                                ? 'bg-primary text-primary-foreground' 
                                                : 'bg-muted text-muted-foreground'
                                        ]">
                                            {{ message.sender_id === ADMIN_ID ? 'HT' : getUserInitials(message.sender.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    
                                    <!-- Message Bubble -->
                                    <div :class="[
                                        'flex flex-col gap-1 max-w-[75%]',
                                        message.sender_id === currentUser?.id ? 'items-end' : 'items-start'
                                    ]">
                                        <div :class="[
                                            'rounded-2xl px-4 py-2.5 shadow-sm relative',
                                            message.sender_id === currentUser?.id 
                                                ? 'bg-primary text-primary-foreground rounded-br-md' 
                                                : 'bg-white border border-border rounded-bl-md'
                                        ]">
                                            <p class="text-sm leading-relaxed break-words whitespace-pre-wrap">
                                                {{ message.message }}
                                            </p>
                                            
                                            <!-- Status indicator for own messages -->
                                            <span 
                                                v-if="message.sender_id === currentUser?.id && message.status"
                                                :class="[
                                                    'absolute -bottom-1 -right-1 text-xs',
                                                    message.status === 'read' ? 'text-blue-500' : 'text-muted-foreground'
                                                ]"
                                            >
                                                <CheckCheck v-if="message.status === 'read' || message.status === 'delivered'" :size="14" />
                                                <Check v-else-if="message.status === 'sent'" :size="14" />
                                                <span v-else-if="message.status === 'sending'">⏱</span>
                                            </span>
                                        </div>
                                        <span class="text-xs text-muted-foreground px-2">
                                            {{ formatTime(message.created_at) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Input -->
                            <div class="flex-shrink-0 p-4 border-t bg-background/95 backdrop-blur-sm">
                                <form @submit.prevent="sendMessage" class="flex gap-2">
                                    <Input
                                        v-model="newMessage"
                                        placeholder="Nhập tin nhắn..."
                                        class="flex-1 rounded-full border-2 focus-visible:ring-primary/20"
                                        @keydown="handleKeyDown"
                                        @input="handleTyping"
                                        @focus="requestNotificationPermission"
                                        :disabled="sending"
                                    />
                                    <Button 
                                        type="submit" 
                                        size="icon"
                                        :disabled="!newMessage.trim() || sending"
                                        class="rounded-full w-10 h-10 transition-all hover:scale-105 flex-shrink-0"
                                    >
                                        <Send :size="18" v-if="!sending" />
                                        <div v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                    </Button>
                                </form>
                                <p class="text-xs text-muted-foreground text-center mt-2">
                                    Thường phản hồi trong vài phút ⚡
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </Transition>

        <!-- Floating Button -->
        <Transition name="bounce">
            <Button
                v-if="!isOpen"
                @click="openChat"
                size="lg"
                class="h-16 w-16 rounded-full shadow-2xl hover:shadow-primary/50 hover:scale-110 transition-all duration-300 bg-gradient-to-br from-primary to-primary/80 relative group"
            >
                <MessageCircle :size="28" class="group-hover:scale-110 transition-transform" />
                
                <!-- Unread Badge -->
                <Transition name="pop">
                    <Badge 
                        v-if="unreadCount > 0"
                        variant="destructive" 
                        class="absolute -top-2 -right-2 h-7 w-7 rounded-full p-0 flex items-center justify-center text-xs font-bold shadow-lg animate-pulse"
                    >
                        {{ unreadCount > 9 ? '9+' : unreadCount }}
                    </Badge>
                </Transition>

                <!-- Pulse Ring -->
                <span class="absolute inset-0 rounded-full bg-primary opacity-75 animate-ping"></span>
            </Button>
        </Transition>
    </div>
</template>

<style scoped>
/* Messages Scrollbar */
.chat-widget-messages {
    scroll-behavior: smooth;
    scrollbar-width: thin;
    scrollbar-color: hsl(var(--muted-foreground) / 0.2) transparent;
}

.chat-widget-messages::-webkit-scrollbar {
    width: 4px;
}

.chat-widget-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chat-widget-messages::-webkit-scrollbar-thumb {
    background: hsl(var(--muted-foreground) / 0.2);
    border-radius: 2px;
}

.chat-widget-messages::-webkit-scrollbar-thumb:hover {
    background: hsl(var(--muted-foreground) / 0.3);
}

/* Animations */
.chat-pop-enter-active,
.chat-pop-leave-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.chat-pop-enter-from {
    opacity: 0;
    transform: translateY(30px) scale(0.8);
}

.chat-pop-leave-to {
    opacity: 0;
    transform: translateY(20px) scale(0.9);
}

.bounce-enter-active {
    animation: bounce-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.bounce-leave-active {
    animation: bounce-out 0.3s ease-in;
}

@keyframes bounce-in {
    0% {
        opacity: 0;
        transform: scale(0) rotate(-180deg);
    }
    50% {
        transform: scale(1.1) rotate(10deg);
    }
    100% {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }
}

@keyframes bounce-out {
    0% {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }
    100% {
        opacity: 0;
        transform: scale(0.8) rotate(180deg);
    }
}

.pop-enter-active,
.pop-leave-active {
    transition: all 0.2s ease;
}

.pop-enter-from {
    opacity: 0;
    transform: scale(0);
}

.pop-leave-to {
    opacity: 0;
    transform: scale(0);
}
</style>
