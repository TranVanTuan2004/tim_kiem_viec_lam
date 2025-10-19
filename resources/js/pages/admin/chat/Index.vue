<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { ScrollArea } from '@/components/ui/scroll-area';
import axios from 'axios';

const page = usePage();

interface User {
    id: number;
    name: string;
    email: string;
    avatar: string | null;
    roles: string[];
    unread_count: number;
}

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
}

interface Props {
    users: User[];
}

const props = defineProps<Props>();

const selectedUser = ref<User | null>(null);
const messages = ref<Message[]>([]);
const newMessage = ref('');
const loading = ref(false);
const searchQuery = ref('');

const filteredUsers = computed(() => {
    if (!searchQuery.value) return props.users;
    const query = searchQuery.value.toLowerCase();
    return props.users.filter(user => 
        user.name.toLowerCase().includes(query) || 
        user.email.toLowerCase().includes(query)
    );
});

const currentUserId = computed(() => {
    // Get from Inertia page props
    return (page.props.auth as any)?.user?.id;
});

async function selectUser(user: User) {
    selectedUser.value = user;
    loading.value = true;
    try {
        const response = await axios.get(`/admin/chat/messages/${user.id}`);
        messages.value = response.data;
        // Update unread count
        const userIndex = props.users.findIndex(u => u.id === user.id);
        if (userIndex !== -1) {
            props.users[userIndex].unread_count = 0;
        }
    } catch (error) {
        console.error('Error fetching messages:', error);
    } finally {
        loading.value = false;
    }
}

async function sendMessage() {
    if (!selectedUser.value || !newMessage.value.trim()) return;

    try {
        const response = await axios.post(`/admin/chat/send/${selectedUser.value.id}`, {
            message: newMessage.value,
        });
        
        messages.value.push(response.data);
        newMessage.value = '';
        
        // Scroll to bottom
        setTimeout(() => {
            const messagesContainer = document.querySelector('.messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }, 100);
    } catch (error) {
        console.error('Error sending message:', error);
    }
}

function formatTime(dateString: string) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days < 7) return `${days}d ago`;
    
    return date.toLocaleDateString();
}

function getUserInitials(name: string) {
    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

// Listen to Echo events
onMounted(() => {
    if (currentUserId.value && window.Echo) {
        const channelName = `chat.${currentUserId.value}`;
        console.log('💬 [Admin Chat] Subscribing to:', channelName);
        console.log('💬 [Admin Chat] Current Admin ID:', currentUserId.value);
        
        const channel = window.Echo.private(channelName);
        
        channel.listen('.message.sent', (e: any) => {
            console.log('💬 [Admin Chat] Received message:', e);
            console.log('💬 [Admin Chat] Sender:', e.sender_id, 'Receiver:', e.receiver_id);
            console.log('💬 [Admin Chat] Current User ID:', currentUserId.value);
            console.log('💬 [Admin Chat] Selected User ID:', selectedUser.value?.id);
            
            // If message is not from me, it's an incoming message
            if (e.sender_id !== currentUserId.value) {
                console.log('✅ [Admin Chat] Incoming message from user');
                
                // If viewing this user's chat, add message
                if (selectedUser.value && e.sender_id === selectedUser.value.id) {
                    console.log('✅ [Admin Chat] Adding to current conversation');
                    messages.value.push(e);
                    
                    // Scroll to bottom
                    setTimeout(() => {
                        const messagesContainer = document.querySelector('.messages-container');
                        if (messagesContainer) {
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        }
                    }, 100);
                } else {
                    // Update unread count for other users
                    console.log('📬 [Admin Chat] Updating unread count for user', e.sender_id);
                    const userIndex = props.users.findIndex(u => u.id === e.sender_id);
                    if (userIndex !== -1) {
                        props.users[userIndex].unread_count++;
                        console.log('📬 [Admin Chat] Unread count updated');
                    }
                }
            } else {
                console.log('⏭️ [Admin Chat] Message from self, already added locally');
            }
        });
        
        channel.subscribed(() => {
            console.log('✅ [Admin Chat] Successfully subscribed to', channelName);
        });
        
        channel.error((error: any) => {
            console.error('❌ [Admin Chat] Subscription error:', error);
        });
    }
});

onUnmounted(() => {
    if (currentUserId.value && window.Echo) {
        window.Echo.leave(`chat.${currentUserId.value}`);
    }
});
</script>

<template>
    <AppLayout title="Chat">
        <div class="container mx-auto px-4 py-8">
            <Card class="h-[calc(100vh-12rem)]">
                <CardContent class="p-0 h-full">
                    <div class="flex h-full">
                        <!-- Users List -->
                        <div class="w-80 border-r flex flex-col">
                            <div class="p-4 border-b">
                                <Input 
                                    v-model="searchQuery"
                                    placeholder="Search users..."
                                    class="w-full"
                                />
                            </div>
                            
                            <ScrollArea class="flex-1">
                                <div class="p-2">
                                    <div
                                        v-for="user in filteredUsers"
                                        :key="user.id"
                                        @click="selectUser(user)"
                                        :class="[
                                            'flex items-center gap-3 p-3 rounded-lg cursor-pointer hover:bg-muted transition-colors',
                                            selectedUser?.id === user.id ? 'bg-muted' : ''
                                        ]"
                                    >
                                        <Avatar>
                                            <AvatarImage v-if="user.avatar" :src="user.avatar" />
                                            <AvatarFallback>{{ getUserInitials(user.name) }}</AvatarFallback>
                                        </Avatar>
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="font-medium truncate">{{ user.name }}</p>
                                                <Badge v-if="user.unread_count > 0" variant="destructive" class="ml-2">
                                                    {{ user.unread_count }}
                                                </Badge>
                                            </div>
                                            <p class="text-sm text-muted-foreground truncate">{{ user.email }}</p>
                                            <div class="flex gap-1 mt-1">
                                                <Badge v-for="role in user.roles" :key="role" variant="secondary" class="text-xs">
                                                    {{ role }}
                                                </Badge>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </ScrollArea>
                        </div>

                        <!-- Chat Area -->
                        <div class="flex-1 flex flex-col">
                            <!-- Chat Header -->
                            <div v-if="selectedUser" class="p-4 border-b flex items-center gap-3">
                                <Avatar>
                                    <AvatarImage v-if="selectedUser.avatar" :src="selectedUser.avatar" />
                                    <AvatarFallback>{{ getUserInitials(selectedUser.name) }}</AvatarFallback>
                                </Avatar>
                                <div>
                                    <p class="font-medium">{{ selectedUser.name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ selectedUser.email }}</p>
                                </div>
                            </div>

                            <!-- No User Selected -->
                            <div v-else class="flex-1 flex items-center justify-center text-muted-foreground">
                                Select a user to start chatting
                            </div>

                            <!-- Messages -->
                            <div v-if="selectedUser" class="flex-1 overflow-y-auto p-4 messages-container">
                                <div v-if="loading" class="flex items-center justify-center min-h-full">
                                    <p class="text-muted-foreground">Loading messages...</p>
                                </div>
                                
                                <div v-else class="space-y-4">
                                    <div
                                        v-for="message in messages"
                                        :key="message.id"
                                        :class="[
                                            'flex gap-3',
                                            message.sender_id === currentUserId ? 'flex-row-reverse' : ''
                                        ]"
                                    >
                                        <Avatar class="flex-shrink-0">
                                            <AvatarImage v-if="message.sender.avatar" :src="message.sender.avatar" />
                                            <AvatarFallback>{{ getUserInitials(message.sender.name) }}</AvatarFallback>
                                        </Avatar>
                                        
                                        <div :class="[
                                            'flex flex-col gap-1 max-w-[70%]',
                                            message.sender_id === currentUserId ? 'items-end' : 'items-start'
                                        ]">
                                            <div :class="[
                                                'rounded-lg px-4 py-2',
                                                message.sender_id === currentUserId 
                                                    ? 'bg-primary text-primary-foreground' 
                                                    : 'bg-muted'
                                            ]">
                                                <p class="break-words">{{ message.message }}</p>
                                            </div>
                                            <p class="text-xs text-muted-foreground">
                                                {{ formatTime(message.created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Input -->
                            <div v-if="selectedUser" class="p-4 border-t">
                                <form @submit.prevent="sendMessage" class="flex gap-2">
                                    <Input
                                        v-model="newMessage"
                                        placeholder="Type your message..."
                                        class="flex-1"
                                        @keydown.enter.exact.prevent="sendMessage"
                                    />
                                    <Button type="submit" :disabled="!newMessage.trim()">
                                        Send
                                    </Button>
                                </form>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<style scoped>
.messages-container {
    scroll-behavior: smooth;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

/* Hide scrollbar for Chrome, Safari and Opera */
.messages-container::-webkit-scrollbar {
    display: none;
}
</style>

