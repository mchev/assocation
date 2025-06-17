<script setup>
    import AppLayout from '@/layouts/AppLayout.vue';
    import { Link } from '@inertiajs/vue3';
    import { Button } from '@/components/ui/button';
    import { Download, Upload } from 'lucide-vue-next';
    import { usePage } from '@inertiajs/vue3';
    import { computed } from 'vue';

    defineProps({
        title: String,
        description: String,
        loading: {
            type: Boolean,
            default: false
        }
    });

    const page = usePage();
    const isIncoming = computed(() => page.url.includes('reservations/in'));
    const isOutgoing = computed(() => page.url.includes('reservations/out'));
</script>

<template>
    <AppLayout :title="title" :description="description">
        <div class="max-w-7xl mx-auto">
            <nav 
                class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0 rounded-b-xl bg-secondary p-4 shadow-sm"
                role="navigation"
                aria-label="Réservations navigation"
            >
                <div class="flex-1 space-y-1">
                    <Button 
                        asChild 
                        :variant="isIncoming ? 'default' : 'outline'" 
                        class="w-full transition-all duration-200 hover:scale-[1.02]"
                        :disabled="loading"
                        :aria-current="isIncoming ? 'page' : undefined"
                    >
                        <Link 
                            :href="route('app.organizations.reservations.in.index')"
                            class="flex items-center justify-center"
                        >
                            <Download class="w-4 h-4 mr-2" />
                            <span>Mes demandes</span>
                        </Link>
                    </Button>
                    <p class="text-xs text-muted-foreground text-center">
                        Consultez et gérez vos demandes d'emprunt de matériel auprès d'autres organisations
                    </p>
                </div>

                <div class="hidden sm:flex items-center">
                    <div class="w-px h-16 bg-gray-200"></div>
                </div>

                <div class="flex-1 space-y-1">
                    <Button 
                        asChild 
                        :variant="isOutgoing ? 'default' : 'outline'" 
                        class="w-full transition-all duration-200 hover:scale-[1.02]"
                        :disabled="loading"
                        :aria-current="isOutgoing ? 'page' : undefined"
                    >
                        <Link 
                            :href="route('app.organizations.reservations.out.index')"
                            class="flex items-center justify-center"
                        >
                            <Upload class="w-4 h-4 mr-2" />
                            <span>Demandes reçues</span>
                        </Link>
                    </Button>
                    <p class="text-xs text-muted-foreground text-center">
                        Gérez les demandes d'emprunt de matériel reçues de la part d'autres organisations
                    </p>
                </div>
            </nav>
        </div>
        <slot />
    </AppLayout>
</template>