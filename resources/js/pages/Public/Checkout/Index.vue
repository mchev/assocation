<template>
    <PublicLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-3xl font-bold mb-8">L'application est en développement !</h1>
                <div class="space-y-4">
                    <p class="text-muted-foreground">
                        Notre plateforme est en cours de développement et nous travaillons activement pour vous offrir une expérience complète. Certaines fonctionnalités sont déjà disponibles et d'autres arriveront très prochainement.
                    </p>
                    <div class="flex items-center gap-2">
                        <AlertCircle class="h-5 w-5 text-yellow-500" />
                        <p class="text-sm text-yellow-600">
                            La fonctionnalité de réservation sera disponible dans les prochaines semaines. Nous finalisons les derniers détails pour vous garantir une expérience optimale.
                        </p>
                    </div>
                    <p class="text-muted-foreground">
                        En attendant, nous vous invitons à commencer à enrichir notre base de données en ajoutant vos matériels. Plus il y aura de matériels disponibles, plus la plateforme sera utile pour toute la communauté. Commencez dès maintenant à créer votre inventaire !
                    </p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/layouts/PublicLayout.vue';
</script> 