<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle, XCircle, Info } from 'lucide-vue-next';

const page = usePage();
const messages = ref([]);
let messageId = 0;

const addMessage = (type, text) => {
    const id = messageId++;
    messages.value.push({ id, type, text });
    
    // Remove message after 3 seconds
    setTimeout(() => {
        messages.value = messages.value.filter(m => m.id !== id);
    }, 3000);
};

// Watch for flash messages
watch(() => page.props.flash, (flash) => {
    if (flash.success) {
        addMessage('success', flash.success);
    }
    if (flash.error) {
        addMessage('error', flash.error);
    }
    if (flash.message) {
        addMessage('info', flash.message);
    }
}, { deep: true });

// Watch for validation errors
watch(() => page.props.errors, (errors) => {
    if (Object.keys(errors).length > 0) {
        addMessage('error', 'Il y a des erreurs dans le formulaire');
    }
}, { deep: true });
</script>

<template>
    <div aria-live="assertive" class="pointer-events-none fixed inset-0 z-50 flex flex-col items-end gap-y-2 px-4 py-6 sm:items-start sm:p-6">
        <TransitionGroup 
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-for="message in messages"
                :key="message.id"
                class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5"
            >
                <div :class="[
                    'p-4',
                    message.type === 'success' && 'bg-green-50',
                    message.type === 'error' && 'bg-red-50',
                    message.type === 'info' && 'bg-blue-50'
                ]">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <CheckCircle v-if="message.type === 'success'" class="h-5 w-5 text-green-400" />
                            <XCircle v-if="message.type === 'error'" class="h-5 w-5 text-red-400" />
                            <Info v-if="message.type === 'info'" class="h-5 w-5 text-blue-400" />
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p :class="[
                                'text-sm font-medium',
                                message.type === 'success' && 'text-green-800',
                                message.type === 'error' && 'text-red-800',
                                message.type === 'info' && 'text-blue-800'
                            ]">
                                {{ message.text }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>