<template>
  <AppLayout 
    title="FAQ - Questions fréquentes | Location de matériel événementiel"
    description="Trouvez toutes les réponses à vos questions sur la location et le prêt de matériel événementiel entre associations. Comment ça marche, tarifs, sécurité et plus encore."
  >
    <Head>
      <component v-bind:is="script" type="application/ld+json">
        {{ schemaMarkup }}
      </component>
    </Head>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
          Questions fréquentes
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
          Trouvez toutes les réponses à vos questions sur la location et le prêt de matériel événementiel entre associations.
        </p>
      </div>

      <!-- FAQ Categories -->
      <div class="grid gap-8">
        <!-- Général -->
        <div class="space-y-6">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
            Général
          </h2>
          
          <Accordion type="single" collapsible class="w-full space-y-4">
            <AccordionItem value="general-1">
              <AccordionTrigger class="text-left">
                Comment fonctionne la réservation de matériel sur la plateforme ?
              </AccordionTrigger>
              <AccordionContent>
                <div class="space-y-4">
                  <p>La plateforme facilite la mise en relation entre associations. Voici comment cela fonctionne :</p>
                  <ol class="list-decimal list-inside space-y-2 ml-4">
                    <li><strong>Recherche :</strong> Consultez les annonces de matériel disponible dans votre région</li>
                    <li><strong>Sélection :</strong> Choisissez le matériel qui vous intéresse et les dates souhaitées</li>
                    <li><strong>Demande :</strong> Envoyez une demande de réservation au propriétaire</li>
                    <li><strong>Échange :</strong> Une fois la demande envoyée, vous échangez directement avec le propriétaire pour organiser les détails pratiques (dates, lieu de récupération, paiement, etc.)</li>
                  </ol>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    <strong>Note :</strong> La plateforme facilite uniquement la mise en relation. Les arrangements financiers et logistiques se font directement entre les parties.
                  </p>
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="general-2">
              <AccordionTrigger class="text-left">
                Y a-t-il des frais d'inscription ou d'utilisation de la plateforme ?
              </AccordionTrigger>
              <AccordionContent>
                <p>Non, l'inscription et l'utilisation de la plateforme sont <strong>totalement gratuites</strong>. Notre objectif est simplement de faciliter l'entraide entre associations et de promouvoir le partage de ressources.</p>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                  Les seuls coûts potentiels sont ceux liés à la location elle-même, qui sont définis directement avec le propriétaire du matériel.
                </p>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="general-3">
              <AccordionTrigger class="text-left">
                La plateforme est-elle disponible dans toute la France ?
              </AccordionTrigger>
              <AccordionContent>
                <p>Oui, la plateforme est accessible dans toute la France. Les associations et particuliers de toutes les régions peuvent s'inscrire et proposer ou rechercher du matériel événementiel.</p>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                  Nous encourageons particulièrement les échanges locaux pour faciliter la logistique et réduire les coûts de transport.
                </p>
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </div>

        <!-- Réservations et paiements -->
        <div class="space-y-6">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
            Réservations et paiements
          </h2>
          
          <Accordion type="single" collapsible class="w-full space-y-4">
            <AccordionItem value="reservation-1">
              <AccordionTrigger class="text-left">
                Comment se passe la location et le paiement ?
              </AccordionTrigger>
              <AccordionContent>
                <div class="space-y-4">
                  <p>Une fois votre demande acceptée, voici comment procéder :</p>
                  <ul class="list-disc list-inside space-y-2 ml-4">
                    <li><strong>Contact direct :</strong> Vous échangez directement avec le propriétaire</li>
                    <li><strong>Organisation :</strong> Vous convenez des modalités (dates, lieu de récupération, etc.)</li>
                    <li><strong>Paiement :</strong> Les paiements se font directement avec les propriétaires, en dehors de la plateforme</li>
                    <li><strong>Récupération :</strong> Vous récupérez le matériel selon les arrangements convenus</li>
                  </ul>
                  <Alert variant="info" class="mt-4">
                    <InfoIcon class="h-4 w-4" />
                    <AlertTitle>Important</AlertTitle>
                    <AlertDescription>
                      La plateforme ne prend pas de commission et n'intervient pas dans les transactions financières.
                    </AlertDescription>
                  </Alert>
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="reservation-2">
              <AccordionTrigger class="text-left">
                Comment annuler une réservation ?
              </AccordionTrigger>
              <AccordionContent>
                <div class="space-y-4">
                  <p>Les modalités d'annulation dépendent du statut de votre réservation :</p>
                  <ul class="list-disc list-inside space-y-2 ml-4">
                    <li><strong>Demande en attente :</strong> Vous pouvez annuler depuis votre espace personnel</li>
                    <li><strong>Demande confirmée :</strong> Contactez directement le propriétaire pour discuter des modalités d'annulation</li>
                  </ul>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Nous recommandons d'annuler le plus tôt possible pour permettre au propriétaire de proposer le matériel à d'autres utilisateurs.
                  </p>
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="reservation-3">
              <AccordionTrigger class="text-left">
                Quels types de matériel peuvent être loués sur la plateforme ?
              </AccordionTrigger>
              <AccordionContent>
                <p>La plateforme accepte tous types de matériel événementiel :</p>
                <div class="grid grid-cols-2 gap-2 mt-4">
                  <ul class="list-disc list-inside space-y-1">
                    <li>Tables et chaises</li>
                    <li>Tentes et chapiteaux</li>
                    <li>Systèmes de sonorisation</li>
                    <li>Éclairage d'événement</li>
                  </ul>
                  <ul class="list-disc list-inside space-y-1">
                    <li>Vaisselle et couverts</li>
                    <li>Décoration</li>
                    <li>Matériel de cuisine</li>
                    <li>Équipements sportifs</li>
                  </ul>
                </div>
                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                  L'important est que le matériel soit en bon état et adapté à un usage événementiel.
                </p>
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </div>

        <!-- Sécurité et protection -->
        <div class="space-y-6">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
            Sécurité et protection
          </h2>
          
          <Accordion type="single" collapsible class="w-full space-y-4">
            <AccordionItem value="security-1">
              <AccordionTrigger class="text-left">
                Comment protéger mon matériel en tant que propriétaire ?
              </AccordionTrigger>
              <AccordionContent>
                <div class="space-y-4">
                  <p>Voici nos recommandations pour protéger votre matériel :</p>
                  <ul class="list-disc list-inside space-y-2 ml-4">
                    <li><strong>Contrat de location :</strong> Établissez un contrat simple entre les parties</li>
                    <li><strong>Conditions claires :</strong> Définissez bien les conditions d'utilisation</li>
                    <li><strong>Caution :</strong> Demandez un chèque de caution si nécessaire</li>
                    <li><strong>État du matériel :</strong> Documentez l'état avant et après utilisation</li>
                    <li><strong>Communication :</strong> Échangez les coordonnées et restez en contact</li>
                  </ul>
                  <Alert variant="warning" class="mt-4">
                    <AlertTriangleIcon class="h-4 w-4" />
                    <AlertTitle>Responsabilité</AlertTitle>
                    <AlertDescription>
                      La plateforme facilite la mise en relation mais la responsabilité de la protection du matériel incombe aux parties prenantes.
                    </AlertDescription>
                  </Alert>
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="security-2">
              <AccordionTrigger class="text-left">
                Que faire en cas de problème avec le matériel loué ?
              </AccordionTrigger>
              <AccordionContent>
                <div class="space-y-4">
                  <p>En cas de problème, voici les étapes à suivre :</p>
                  <ol class="list-decimal list-inside space-y-2 ml-4">
                    <li><strong>Contact immédiat :</strong> Contactez directement le propriétaire du matériel</li>
                    <li><strong>Documentation :</strong> Prenez des photos et documentez le problème</li>
                    <li><strong>Communication :</strong> Expliquez clairement la situation</li>
                    <li><strong>Solution :</strong> Trouvez ensemble une solution équitable</li>
                  </ol>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Nous recommandons de documenter l'état du matériel avant et après utilisation pour éviter les litiges.
                  </p>
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="security-3">
              <AccordionTrigger class="text-left">
                Comment signaler un utilisateur problématique ?
              </AccordionTrigger>
              <AccordionContent>
                <p>Si vous rencontrez un utilisateur problématique, vous pouvez nous contacter à <a href="mailto:contact@assocation.fr" class="text-primary hover:underline">contact@assocation.fr</a>.</p>
                <p class="mt-2">Nous examinerons la situation et prendrons les mesures appropriées pour maintenir la qualité de la communauté.</p>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                  N'hésitez pas à nous fournir tous les détails utiles pour nous aider à traiter votre signalement.
                </p>
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </div>

        <!-- Optimisation -->
        <div class="space-y-6">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
            Conseils et optimisation
          </h2>
          
          <Accordion type="single" collapsible class="w-full space-y-4">
            <AccordionItem value="tips-1">
              <AccordionTrigger class="text-left">
                Comment optimiser mes annonces pour attirer plus de demandes ?
              </AccordionTrigger>
              <AccordionContent>
                <div class="space-y-4">
                  <p>Voici nos conseils pour optimiser vos annonces :</p>
                  <ul class="list-disc list-inside space-y-2 ml-4">
                    <li><strong>Photos de qualité :</strong> Ajoutez des photos claires et représentatives</li>
                    <li><strong>Description détaillée :</strong> Décrivez précisément le matériel et ses caractéristiques</li>
                    <li><strong>Tarifs clairs :</strong> Indiquez clairement les prix et conditions</li>
                    <li><strong>Réactivité :</strong> Répondez rapidement aux demandes de réservation</li>
                    <li><strong>Disponibilité :</strong> Mettez à jour régulièrement vos disponibilités</li>
                    <li><strong>Localisation :</strong> Précisez bien votre zone géographique</li>
                  </ul>
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="tips-2">
              <AccordionTrigger class="text-left">
                Comment bien préparer ma demande de réservation ?
              </AccordionTrigger>
              <AccordionContent>
                <div class="space-y-4">
                  <p>Pour maximiser vos chances d'acceptation :</p>
                  <ul class="list-disc list-inside space-y-2 ml-4">
                    <li><strong>Présentez-vous :</strong> Expliquez brièvement votre association et votre projet</li>
                    <li><strong>Dates précises :</strong> Indiquez clairement vos besoins en dates</li>
                    <li><strong>Usage détaillé :</strong> Décrivez l'usage prévu du matériel</li>
                    <li><strong>Contact :</strong> Assurez-vous que vos coordonnées sont à jour</li>
                    <li><strong>Flexibilité :</strong> Soyez flexible sur les modalités de récupération</li>
                  </ul>
                </div>
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </div>
      </div>

      <!-- Contact Section -->
      <div class="mt-16 bg-gradient-to-r from-violet-50 to-blue-50 dark:from-violet-950 dark:to-blue-950 rounded-2xl p-8 text-center">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
          Vous ne trouvez pas votre réponse ?
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-2xl mx-auto">
          Notre équipe est là pour vous aider. N'hésitez pas à nous contacter pour toute question supplémentaire.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Button as="a" href="mailto:contact@assocation.fr" class="font-semibold">
            <MailIcon class="mr-2 h-4 w-4" />
            Nous contacter
          </Button>
          <Button variant="outline" as="a" :href="route('how-it-works')" class="font-semibold">
            <HelpCircleIcon class="mr-2 h-4 w-4" />
            Comment ça marche ?
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { 
  Accordion, 
  AccordionContent, 
  AccordionItem, 
  AccordionTrigger 
} from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { 
  InfoIcon, 
  AlertTriangleIcon, 
  MailIcon, 
  HelpCircleIcon 
} from 'lucide-vue-next';

// Schema.org FAQ Markup
const schemaMarkup = JSON.stringify({
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Comment fonctionne la réservation de matériel sur la plateforme ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "La plateforme facilite la mise en relation entre associations. Vous consultez les annonces, sélectionnez le matériel souhaité et envoyez une demande de réservation. Une fois la demande envoyée, vous échangez directement avec le propriétaire pour organiser les détails pratiques (dates, lieu de récupération, paiement, etc.)."
      }
    },
    {
      "@type": "Question",
      "name": "Y a-t-il des frais d'inscription ou d'utilisation de la plateforme ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Non, l'inscription et l'utilisation de la plateforme sont totalement gratuites. Notre objectif est simplement de faciliter l'entraide entre associations et de promouvoir le partage de ressources."
      }
    },
    {
      "@type": "Question",
      "name": "Comment se passe la location et le paiement ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Une fois votre demande acceptée, vous convenez directement avec le propriétaire des modalités de la location (dates, lieu de récupération, paiement, etc.). Les paiements et arrangements financiers se font directement avec les propriétaires des équipements, en dehors de la plateforme."
      }
    },
    {
      "@type": "Question",
      "name": "Comment protéger mon matériel en tant que propriétaire ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Nous vous recommandons d'établir un contrat de location simple entre les parties et de bien définir les conditions de location. Vous pouvez également demander un chèque de caution si nécessaire. La plateforme facilite la mise en relation mais la responsabilité de la protection du matériel incombe aux parties prenantes."
      }
    },
    {
      "@type": "Question",
      "name": "Quels types de matériel peuvent être loués sur la plateforme ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "La plateforme accepte tous types de matériel événementiel : tables, chaises, tentes, sono, éclairage, vaisselle, décoration, etc. L'important est que le matériel soit en bon état et adapté à un usage événementiel."
      }
    },
    {
      "@type": "Question",
      "name": "Comment annuler une réservation ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Vous pouvez annuler une réservation depuis votre espace personnel tant qu'elle n'a pas été confirmée par le propriétaire. Une fois confirmée, il est recommandé de contacter directement le propriétaire pour discuter des modalités d'annulation."
      }
    },
    {
      "@type": "Question",
      "name": "Que faire en cas de problème avec le matériel loué ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "En cas de problème, contactez directement le propriétaire du matériel. Nous vous recommandons de documenter l'état du matériel avant et après utilisation, et de communiquer rapidement en cas de souci."
      }
    },
    {
      "@type": "Question",
      "name": "Comment signaler un utilisateur problématique ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Si vous rencontrez un utilisateur problématique, vous pouvez nous contacter à contact@assocation.fr. Nous examinerons la situation et prendrons les mesures appropriées pour maintenir la qualité de la communauté."
      }
    },
    {
      "@type": "Question",
      "name": "La plateforme est-elle disponible dans toute la France ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui, la plateforme est accessible dans toute la France. Les associations et particuliers de toutes les régions peuvent s'inscrire et proposer ou rechercher du matériel événementiel."
      }
    },
    {
      "@type": "Question",
      "name": "Comment optimiser mes annonces pour attirer plus de demandes ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Pour optimiser vos annonces : ajoutez des photos de qualité, décrivez précisément le matériel et ses caractéristiques, indiquez clairement les tarifs et conditions, et répondez rapidement aux demandes de réservation."
      }
    }
  ]
});
</script>
