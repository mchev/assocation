<script setup>
import PublicLayout from '@/layouts/PublicLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { 
    SearchIcon, 
    MapPinIcon, 
    CalendarIcon,
    TruckIcon,
    HeartIcon,
    StarIcon,
    ShieldCheckIcon,
    UsersIcon
} from 'lucide-vue-next'

const categories = [
    {
        name: "Sonorisation",
        count: 124,
        description: "Enceintes, tables de mixage, micros..."
    },
    {
        name: "Éclairage",
        count: 89,
        description: "Projecteurs, jeux de lumière, consoles..."
    },
    {
        name: "Mobilier",
        count: 156,
        description: "Tables, chaises, barnums..."
    },
    {
        name: "Cuisine",
        count: 67,
        description: "Frigos, planchas, ustensiles..."
    }
]

const features = [
    {
        icon: ShieldCheckIcon,
        title: "Location sécurisée",
        description: "Système de paiement sécurisé et assurance incluse pour chaque location."
    },
    {
        icon: MapPinIcon,
        title: "Proximité",
        description: "Trouvez du matériel près de chez vous et réduisez les coûts de transport."
    },
    {
        icon: CalendarIcon,
        title: "Disponibilité en temps réel",
        description: "Consultez les calendriers et réservez aux dates qui vous conviennent."
    },
    {
        icon: UsersIcon,
        title: "Communauté associative",
        description: "Rejoignez un réseau d'associations partageant les mêmes valeurs."
    }
]
</script>

<template>
    <Head>
        <title>Découvrir la plateforme - Assodépôt</title>
        <meta name="description" content="Découvrez Assodépôt, la plateforme de location de matériel événementiel qui connecte les associations et les particuliers." />
    </Head>

    <PublicLayout>
    <div class="py-12 space-y-12">
        <!-- En-tête -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl font-bold text-foreground">
                    Trouvez le matériel idéal pour votre événement
                </h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    Assodépôt vous connecte avec une communauté d'associations et de particuliers pour partager du matériel événementiel de qualité.
                </p>
            </div>
        </div>

        <!-- Barre de recherche -->
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <Card>
                <CardContent class="p-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-grow relative">
                            <SearchIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <input 
                                type="search"
                                placeholder="Que recherchez-vous ?"
                                class="w-full pl-9 pr-4 py-2 rounded-md border bg-background"
                            />
                        </div>
                        <div class="relative sm:w-64">
                            <MapPinIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <input 
                                type="text"
                                placeholder="Où ?"
                                class="w-full pl-9 pr-4 py-2 rounded-md border bg-background"
                            />
                        </div>
                        <Button class="shrink-0">
                            Rechercher
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Catégories populaires -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-foreground text-center mb-8">
                Catégories populaires
            </h2>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <Card v-for="category in categories" :key="category.name">
                    <CardHeader>
                        <CardTitle>{{ category.name }}</CardTitle>
                        <CardDescription>{{ category.description }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            {{ category.count }} annonces disponibles
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Fonctionnalités -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-foreground text-center mb-8">
                Pourquoi choisir Assodépôt ?
            </h2>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div v-for="feature in features" :key="feature.title" class="text-center">
                    <div class="rounded-full bg-primary/10 p-3 w-12 h-12 mx-auto mb-4">
                        <component :is="feature.icon" class="w-6 h-6 text-primary" />
                    </div>
                    <h3 class="font-medium text-foreground mb-2">{{ feature.title }}</h3>
                    <p class="text-sm text-muted-foreground">{{ feature.description }}</p>
                </div>
            </div>
        </div>

        <!-- Call to action -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <Card class="bg-primary text-primary-foreground">
                <CardContent class="p-8 text-center">
                    <h2 class="text-2xl font-semibold mb-4">
                        Prêt à rejoindre la communauté ?
                    </h2>
                    <p class="text-primary-foreground/90 mb-6 max-w-2xl mx-auto">
                        Créez votre compte gratuitement et commencez à louer ou proposer du matériel dès aujourd'hui.
                    </p>
                    <div class="flex gap-4 justify-center">
                        <Button variant="secondary" size="lg">
                            Créer un compte
                        </Button>
                        <Button variant="outline" size="lg" class="bg-transparent">
                            En savoir plus
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</PublicLayout>
</template> 