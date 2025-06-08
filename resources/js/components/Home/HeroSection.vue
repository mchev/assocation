<script setup>
import { Link } from '@inertiajs/vue3'
import { ArrowRightIcon, XIcon, ShieldCheckIcon, LayoutListIcon, UsersIcon, SparklesIcon } from 'lucide-vue-next'
import { ref } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'

const isVisible = ref(true)

const closeHero = () => {
    isVisible.value = false
    localStorage.setItem('heroCardDismissed', 'true')
}

const advantages = [
    {
        icon: SparklesIcon,
        title: 'Gratuit',
        description: 'Pour une association ou un particulier'
    },
    {
        icon: ShieldCheckIcon,
        title: 'Réservation sécurisée',
        description: 'Processus simple et sûr'
    },
    {
        icon: LayoutListIcon,
        title: 'Gestion d\'inventaire',
        description: 'Suivez votre matériel'
    },
    {
        icon: UsersIcon,
        title: 'Communauté active',
        description: 'Réseau associatif'
    }
]
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform -translate-y-4 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform -translate-y-4 opacity-0"
    >
        <section 
            v-if="isVisible" 
            aria-labelledby="hero-heading"
            class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4"
        >
            <!-- Background with gradient -->
            <div class="absolute inset-0  rounded-2xl" />
            
            <Card class="relative border-none bg-gradient-to-br from-gray-200 via-gray-50 to-background">
                <Button 
                    variant="ghost" 
                    size="icon"
                    class="absolute right-3 top-3 hover:bg-gray-100/80" 
                    @click="closeHero"
                >
                    <XIcon class="h-4 w-4" />
                    <span class="sr-only">Fermer la bannière d'accueil</span>
                </Button>
                
                <CardContent class="p-6">
                    <div class="flex flex-col lg:flex-row items-center gap-8">
                        <div class="flex-1 text-center lg:text-left">
                            <div class="space-y-4">
                                <h1 
                                    id="hero-heading" 
                                    class="text-2xl sm:text-3xl font-bold font-title text-gray-900 leading-tight"
                                >
                                    <span class="inline-block">
                                        Location et prêt de matériel 
                                        <span class="text-primary whitespace-nowrap">événementiel</span>
                                    </span>
                                    <span class="block text-lg sm:text-xl mt-2 text-gray-600 font-medium">
                                        entre associations et particuliers
                                    </span>
                                </h1>
                                
                                <p class="text-base text-gray-500 max-w-xl mx-auto lg:mx-0">
                                    <strong class="text-gray-700">Assodépôt</strong> facilite la location de matériel entre associations. Trouvez et réservez en quelques clics le matériel dont vous avez besoin pour vos événements.
                                </p>
                            </div>

                            <div class="mt-6 flex flex-wrap items-center gap-3 justify-center lg:justify-start">
                                <Link
                                    :href="route('discover')"
                                    class="inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary"
                                >
                                    Découvrir la plateforme
                                    <ArrowRightIcon class="ml-2 h-4 w-4" />
                                </Link>
                                <Button 
                                    variant="outline"
                                    as="a"
                                    :href="route('how-it-works')"
                                    class="text-sm"
                                >
                                    Comment ça marche ?
                                </Button>
                            </div>
                        </div>

                        <div class="hidden lg:block w-px h-32 bg-gray-200 self-center"></div>

                        <div class="flex-shrink-0 lg:w-80">
                            <div class="rounded-lg p-4">
                                <h2 class="text-sm font-medium text-gray-900 mb-4">
                                    Avantages de la plateforme
                                </h2>
                                <ul class="grid gap-3" role="list">
                                    <li 
                                        v-for="advantage in advantages" 
                                        :key="advantage.title"
                                        class="flex items-start gap-3 group"
                                    >
                                        <div 
                                            class="rounded-md bg-primary/10 p-2 group-hover:bg-primary/20 transition-colors duration-200"
                                            :class="{ 'bg-primary/20': advantage.title === 'Gratuit' }"
                                        >
                                            <component 
                                                :is="advantage.icon" 
                                                class="w-4 h-4 text-primary"
                                            />
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900">
                                                {{ advantage.title }}
                                            </h3>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ advantage.description }}
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </section>
    </Transition>
</template>