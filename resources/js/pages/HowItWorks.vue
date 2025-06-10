<script setup>
import PublicLayout from '@/layouts/PublicLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { Card, CardContent } from '@/components/ui/card'
import { 
    SearchIcon, 
    CalendarIcon, 
    MessageSquareIcon, 
    PackageIcon, 
    CheckCircleIcon,
    ListIcon,
    UsersIcon,
    ShieldCheckIcon,
    ClockIcon,
    HeartIcon,
} from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion'

const page = usePage();
const appName = page.props.name;

const steps = [
    {
        icon: SearchIcon,
        title: "Créez votre compte",
        description: "Inscrivez-vous gratuitement en quelques clics pour accéder à la plateforme."
    },
    {
        icon: CalendarIcon,
        title: "Trouvez votre matériel",
        description: "Parcourez les annonces et trouvez le matériel dont vous avez besoin pour votre événement."
    },
    {
        icon: MessageSquareIcon,
        title: "Envoyez votre demande",
        description: "Sélectionnez vos dates souhaitées et envoyez une demande de réservation."
    },
    {
        icon: UsersIcon,
        title: "Échangez avec le propriétaire",
        description: "Discutez directement avec le propriétaire pour organiser les détails pratiques de la location."
    },
    {
        icon: PackageIcon,
        title: "Profitez de votre location",
        description: "Récupérez le matériel aux dates convenues et profitez de votre événement."
    }
]

const features = [
    {
        icon: ListIcon,
        title: "Pour les propriétaires",
        items: [
            "Créez votre inventaire de matériel",
            "Gérez vos disponibilités",
            "Validez les demandes de réservation",
            "Contribuez à l'économie collaborative"
        ]
    },
    {
        icon: UsersIcon,
        title: "Pour les associations",
        items: [
            "Trouvez du matériel à proximité",
            "Économisez sur vos événements",
            "Réservation simple et rapide",
            "Accédez à du matériel professionnel"
        ]
    }
]

const benefits = [
    {
        icon: ShieldCheckIcon,
        title: "Simple et gratuit",
        value: "100%",
        description: "Une plateforme gratuite et facile à utiliser"
    },
    {
        icon: ClockIcon,
        title: "Disponibilité",
        value: "24/7",
        description: "Consultez les annonces à tout moment"
    },
    {
        icon: UsersIcon,
        title: "Communauté",
        value: "Local",
        description: "Trouvez du matériel près de chez vous"
    },
    {
        icon: HeartIcon,
        title: "Entraide",
        value: "∞",
        description: "L'esprit associatif avant tout"
    }
]

const faqs = [
    {
        question: "Comment fonctionne la réservation ?",
        answer: "La plateforme permet simplement de consulter les annonces et d'envoyer des demandes de réservation. Une fois la demande envoyée, vous pouvez échanger directement avec le propriétaire pour organiser les détails pratiques."
    },
    {
        question: "Comment se passe la location ?",
        answer: "Une fois que vous avez trouvé le matériel qui vous intéresse, vous convenez directement avec le propriétaire des modalités de la location (dates, lieu de récupération, paiement, etc.). La plateforme facilite uniquement la mise en relation."
    },
    {
        question: "Y a-t-il des frais d'inscription ou d'utilisation ?",
        answer: "Non, l'inscription et l'utilisation de la plateforme sont totalement gratuites. Notre objectif est simplement de faciliter l'entraide entre associations."
    },
    {
        question: "Comment protéger mon matériel en tant que propriétaire ?",
        answer: "Nous vous recommandons d'établir un contrat de location simple entre les parties et de bien définir les conditions de location. Vous pouvez également demander un chèque de caution si nécessaire."
    }
]
</script>

<template>
    <Head>
        <title>Comment ça marche ? - {{ appName }}</title>
        <meta name="description" :content="`Découvrez comment fonctionne ${appName}, la plateforme de location de matériel événementiel entre associations et particuliers.`" />
    </Head>

    <PublicLayout>
        <div class="py-12 space-y-24">
            <!-- En-tête -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-foreground">Comment ça marche ?</h1>
                    <p class="mt-4 text-lg text-muted-foreground max-w-2xl mx-auto">
                        <strong>{{ appName }}</strong> est une plateforme de mise en relation qui simplifie la recherche 
                        et la réservation de matériel événementiel entre associations et particuliers.
                    </p>
                </div>
            </div>

            <!-- Étapes avec design amélioré -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-semibold text-center mb-12">En 5 étapes simples</h2>
                <div class="relative">
                    <!-- Ligne de connexion -->
                    <div class="hidden lg:block absolute top-1/2 left-0 w-full h-0.5 bg-primary/10 -translate-y-1/2"></div>
                    
                    <div class="grid gap-8 lg:gap-12 md:grid-cols-2 lg:grid-cols-5">
                        <div v-for="(step, index) in steps" :key="index" 
                             class="relative flex flex-col items-center text-center group">
                            <!-- Cercle avec numéro et icône -->
                            <div class="relative mb-4">
                                <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center
                                          group-hover:bg-primary/20 transition-colors duration-300
                                          relative z-10">
                                    <component :is="step.icon" class="w-8 h-8 text-primary" />
                                </div>
                                <div class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-primary text-white
                                          flex items-center justify-center text-sm font-medium">
                                    {{ index + 1 }}
                                </div>
                            </div>
                            
                            <!-- Texte -->
                            <div class="space-y-2">
                                <h3 class="font-semibold text-foreground">{{ step.title }}</h3>
                                <p class="text-sm text-muted-foreground">{{ step.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pourquoi nous choisir -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-semibold text-center mb-8">Pourquoi choisir {{ appName }} ?</h2>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <Card v-for="benefit in benefits" :key="benefit.title" class="text-center">
                        <CardContent class="pt-6">
                            <div class="flex flex-col items-center gap-4">
                                <div class="rounded-full bg-primary/10 p-3">
                                    <component :is="benefit.icon" class="w-6 h-6 text-primary" />
                                </div>
                                <div>
                                    <span class="block text-2xl font-bold text-primary">{{ benefit.value }}</span>
                                    <h3 class="font-semibold text-foreground">{{ benefit.title }}</h3>
                                    <p class="mt-1 text-sm text-muted-foreground">{{ benefit.description }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Fonctionnalités par profil -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-semibold text-center mb-8">Une solution adaptée à chacun</h2>
                <div class="grid gap-8 md:grid-cols-2">
                    <Card v-for="feature in features" :key="feature.title" class="hover:shadow-lg transition-shadow duration-300">
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

            <!-- FAQ -->
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-semibold text-center mb-8">Questions fréquentes</h2>
                <Accordion type="single" collapsible class="w-full">
                    <AccordionItem v-for="(faq, index) in faqs" :key="index" :value="'item-' + index">
                        <AccordionTrigger class="text-left">{{ faq.question }}</AccordionTrigger>
                        <AccordionContent>
                            {{ faq.answer }}
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>

            <!-- CTA -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-primary/5 rounded-2xl p-8 text-center">
                    <h2 class="text-2xl font-semibold mb-4">Prêt à commencer ?</h2>
                    <p class="text-muted-foreground mb-6">
                        Rejoignez notre communauté et facilitez l'organisation de vos événements grâce au partage de matériel.
                    </p>
                    <div class="flex gap-4 justify-center">
                        <Button size="lg" class="font-semibold" @click="$inertia.visit(route('register'))">
                            Créer un compte gratuitement
                        </Button>
                        <Button size="lg" variant="outline" class="font-semibold" @click="$inertia.visit(route('login'))">
                            Se connecter
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template> 