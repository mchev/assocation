<script setup>
import PublicLayout from '@/layouts/PublicLayout.vue'
import { Head, usePage, Link } from '@inertiajs/vue3'
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

const page = usePage();
const appName = page.props.name;

defineProps({
    popularCategories: {
        type: Array,
        required: true
    }
})

const features = [
    {
        icon: ShieldCheckIcon,
        title: "Reservation simplifiée",
        description: "Sélectionnez vos dates souhaitées et envoyez une demande de réservation."
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
        <title>Découvrir la plateforme - {{ appName }}</title>
        <meta name="description" :content="`Découvrez ${appName}, la plateforme de location de matériel événementiel qui connecte les associations et les particuliers.`" />
    </Head>

    <PublicLayout>
        <div class="py-12 space-y-24">
            <!-- En-tête -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto">
                    <h1 class="text-4xl font-bold text-foreground">
                        Trouvez le matériel idéal pour votre événement
                    </h1>
                    <p class="mt-4 text-lg text-muted-foreground">
                        {{ appName }} vous connecte avec une communauté d'associations et de particuliers pour partager du matériel événementiel de qualité.
                    </p>
                </div>
            </div>

            <!-- Catégories populaires -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-semibold text-foreground text-center mb-8">
                    Catégories populaires
                </h2>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <Card v-for="category in popularCategories" :key="category.name" 
                          class="hover:shadow-lg transition-shadow duration-300 group">
                        <CardHeader>
                            <CardTitle class="group-hover:text-primary transition-colors duration-300">
                                {{ category.name }}
                            </CardTitle>
                            <CardDescription>{{ category.description }}</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="flex items-center gap-2">
                                <div class="rounded-full bg-primary/10 p-1.5">
                                    <StarIcon class="w-4 h-4 text-primary" />
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    {{ category.count }} annonces disponibles
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Fonctionnalités -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-center mb-12">
                    Pourquoi choisir {{ appName }} ?
                </h2>
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    <div v-for="feature in features" :key="feature.title" 
                         class="group hover:scale-105 transition-transform duration-300">
                        <Card class="h-full">
                            <CardContent class="p-6">
                                <div class="flex flex-col items-center text-center gap-4">
                                    <div class="rounded-full bg-primary/10 p-4 group-hover:bg-primary/20 transition-colors duration-300">
                                        <component :is="feature.icon" class="w-8 h-8 text-primary" />
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-foreground mb-2">{{ feature.title }}</h3>
                                        <p class="text-sm text-muted-foreground">{{ feature.description }}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>

            <!-- Call to action -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-primary/5 rounded-2xl p-8 text-center">
                    <h2 class="text-2xl font-semibold mb-4">
                        Prêt à rejoindre la communauté ?
                    </h2>
                    <p class="text-muted-foreground mb-6 max-w-2xl mx-auto">
                        Créez votre compte gratuitement et commencez à louer ou proposer du matériel dès aujourd'hui.
                    </p>
                    <div class="flex gap-4 justify-center">
                        <Button asChild size="lg" class="font-semibold">
                            <Link :href="route('register')">
                                Créer un compte gratuitement
                            </Link>
                        </Button>
                        <Button asChild variant="outline" size="lg" class="font-semibold">
                            <Link :href="route('how-it-works')">
                                En savoir plus
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template> 