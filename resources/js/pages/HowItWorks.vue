<script setup>
import PublicLayout from '@/layouts/PublicLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent } from '@/components/ui/card'
import { 
    SearchIcon, 
    CalendarIcon, 
    MessageSquareIcon, 
    CreditCardIcon, 
    PackageIcon, 
    CheckCircleIcon,
    ListIcon,
    UsersIcon
} from 'lucide-vue-next'

const steps = [
    {
        icon: SearchIcon,
        title: "Recherchez du matériel",
        description: "Parcourez notre catalogue de matériel événementiel disponible près de chez vous."
    },
    {
        icon: CalendarIcon,
        title: "Vérifiez la disponibilité",
        description: "Consultez le calendrier pour voir les dates disponibles et planifiez votre location."
    },
    {
        icon: MessageSquareIcon,
        title: "Contactez le propriétaire",
        description: "Échangez directement avec le propriétaire pour organiser les détails de la location."
    },
    {
        icon: CreditCardIcon,
        title: "Réservez en ligne",
        description: "Effectuez le paiement de manière sécurisée via notre plateforme."
    },
    {
        icon: PackageIcon,
        title: "Récupérez le matériel",
        description: "Récupérez le matériel au lieu convenu et vérifiez son état."
    },
    {
        icon: CheckCircleIcon,
        title: "Profitez et restituez",
        description: "Utilisez le matériel pour votre événement et restituez-le dans le même état."
    }
]

const features = [
    {
        icon: ListIcon,
        title: "Pour les propriétaires",
        items: [
            "Créez votre inventaire de matériel",
            "Gérez vos disponibilités",
            "Suivez vos locations",
            "Générez des revenus supplémentaires",
            "Contribuez à l'économie collaborative"
        ]
    },
    {
        icon: UsersIcon,
        title: "Pour les associations",
        items: [
            "Trouvez du matériel à proximité",
            "Économisez sur vos événements",
            "Réservation simple et sécurisée",
            "Échangez avec d'autres associations",
            "Accédez à du matériel professionnel"
        ]
    }
]
</script>

<template>
    <Head>
        <title>Comment ça marche ? - Assodépôt</title>
        <meta name="description" content="Découvrez comment fonctionne Assodépôt, la plateforme de location de matériel événementiel entre associations et particuliers." />
    </Head>

<PublicLayout>
    <div class="py-12 space-y-12">
        <!-- En-tête -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-foreground">Comment ça marche ?</h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    Découvrez comment utiliser Assodépôt pour louer ou proposer du matériel événementiel
                </p>
            </div>
        </div>

        <!-- Étapes -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="(step, index) in steps" :key="index" class="relative">
                    <span class="absolute -top-3 -left-3 w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-medium">
                        {{ index + 1 }}
                    </span>
                    <CardContent class="pt-8">
                        <div class="flex flex-col items-start gap-4">
                            <div class="rounded-lg bg-primary/10 p-2.5">
                                <component :is="step.icon" class="w-5 h-5 text-primary" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-foreground">{{ step.title }}</h3>
                                <p class="mt-1 text-sm text-muted-foreground">{{ step.description }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Fonctionnalités par profil -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-2">
                <Card v-for="feature in features" :key="feature.title">
                    <CardContent class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="rounded-lg bg-primary/10 p-2.5">
                                <component :is="feature.icon" class="w-5 h-5 text-primary" />
                            </div>
                            <h2 class="text-xl font-semibold text-foreground">{{ feature.title }}</h2>
                        </div>
                        <ul class="space-y-2">
                            <li v-for="item in feature.items" :key="item" class="flex items-center gap-2 text-muted-foreground">
                                <CheckCircleIcon class="w-4 h-4 text-primary" />
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</PublicLayout>
</template> 