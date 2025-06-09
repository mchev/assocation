<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ArrowRightIcon, XIcon, ShieldCheckIcon, LayoutListIcon, UsersIcon, SparklesIcon } from 'lucide-vue-next'
import { ref } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'

const page = usePage();
const appName = page.props.name;

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
            
            <Card class="relative border-none bg-gradient-to-br from-violet-100 via-blue-50 to-background dark:from-violet-950 dark:via-blue-950 dark:to-background">
                <Button 
                    variant="ghost" 
                    size="icon"
                    class="absolute right-3 top-3 hover:bg-white/80" 
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
                                    class="text-2xl sm:text-3xl font-bold font-title text-gray-900 leading-tight dark:text-white"
                                >
                                    <span class="inline-block">
                                        Location et prêt de matériel 
                                        <span class="bg-gradient-to-r from-violet-600 to-blue-600 bg-clip-text text-transparent whitespace-nowrap dark:from-violet-400 dark:to-blue-400">événementiel</span>
                                    </span>
                                    <span class="block text-lg sm:text-xl mt-2 text-gray-600 font-medium dark:text-gray-400">
                                        entre associations et particuliers
                                    </span>
                                </h1>
                                
                                <p class="text-base text-gray-500 max-w-xl mx-auto lg:mx-0 dark:text-gray-400">
                                    <strong class="text-gray-900 dark:text-white">{{ appName }}</strong> facilite la location de matériel entre associations. Trouvez et réservez en quelques clics le matériel dont vous avez besoin pour vos événements.
                                </p>
                            </div>

                            <div class="mt-6 flex flex-wrap items-center gap-3 justify-center lg:justify-start">
                                <Link
                                    :href="route('discover')"
                                    class="inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-lg bg-gradient-to-r from-violet-600 to-blue-600 text-white hover:from-violet-700 hover:to-blue-700 transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-violet-500 dark:from-violet-500 dark:to-blue-500 dark:hover:from-violet-600 dark:hover:to-blue-600"
                                >
                                    Découvrir la plateforme
                                    <ArrowRightIcon class="ml-2 h-4 w-4" />
                                </Link>
                                <Button 
                                    variant="outline"
                                    as="a"
                                    :href="route('how-it-works')"
                                    class="text-sm border-violet-200 text-violet-700 hover:bg-violet-50 dark:border-violet-800 dark:text-violet-300 dark:hover:bg-violet-900"
                                >
                                    Comment ça marche ?
                                </Button>
                            </div>
                        </div>

                        <div class="hidden lg:block w-px h-32 bg-gradient-to-b from-violet-200 to-blue-200 self-center dark:from-violet-800 dark:to-blue-800"></div>

                        <div class="flex-shrink-0 lg:w-80">
                            <div class="rounded-lg p-4">
                                <h2 class="text-sm font-medium text-gray-900 mb-4 dark:text-white">
                                    Avantages de la plateforme
                                </h2>
                                <ul class="grid gap-3" role="list">
                                    <li 
                                        v-for="advantage in advantages" 
                                        :key="advantage.title"
                                        class="flex items-start gap-3 group"
                                    >
                                        <div 
                                            class="rounded-md bg-gradient-to-br from-violet-100 to-blue-100 p-2 group-hover:from-violet-200 group-hover:to-blue-200 transition-colors duration-200 dark:from-violet-900 dark:to-blue-900"
                                            :class="{ 'from-violet-200 to-blue-200 dark:from-violet-800 dark:to-blue-800': advantage.title === 'Gratuit' }"
                                        >
                                            <component 
                                                :is="advantage.icon" 
                                                class="w-4 h-4 text-violet-600 dark:text-violet-400"
                                            />
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ advantage.title }}
                                            </h3>
                                            <p class="text-xs text-gray-500 mt-0.5 dark:text-gray-400">
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