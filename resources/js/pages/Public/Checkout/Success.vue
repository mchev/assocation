<template>
    <PublicLayout>
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-2xl mx-auto text-center">
                <div class="mb-8">
                    <CheckCircle class="w-16 h-16 text-green-500 mx-auto" />
                </div>

                <h1 class="text-3xl font-bold mb-4">Reservation Successful!</h1>
                <p class="text-lg text-muted-foreground mb-8">
                    Thank you for your reservation. We have sent a confirmation email with all the details.
                </p>

                <div class="space-y-4">
                    <Button asChild>
                        <Link :href="route('equipments.index')">
                            Browse More Equipment
                        </Link>
                    </Button>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import { CheckCircle } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Button } from '@/components/ui/button';
</script> 