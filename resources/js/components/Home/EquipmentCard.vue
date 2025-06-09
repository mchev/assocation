<template>
  <div 
    class="group relative bg-card rounded-xl shadow-sm border border-border/50 overflow-hidden hover:shadow-lg transition-all duration-200"
  >
    <!-- Equipment Image -->
    <Link 
      :href="route('equipments.show', equipment)"
      class="block aspect-[16/9] bg-accent/10 relative overflow-hidden"
    >
      <img
        v-if="equipment.images && equipment.images.length > 0"
        :src="equipment.images[0].url"
        :alt="equipment.name"
        loading="lazy"
        placeholder="blur"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
      />
      <div 
        v-else 
        class="w-full h-full flex items-center justify-center text-muted-foreground/30"
      >
        <Tag class="w-12 h-12" />
      </div>
      
      <!-- Availability Badge -->
      <div class="absolute top-3 right-3">
        <Badge
          :variant="equipment.is_available ? 'success' : 'destructive'"
          class="flex items-center gap-1 shadow-sm"
        >
          <ShieldCheck v-if="equipment.is_available" class="h-3 w-3" />
          <AlertTriangle v-else class="h-3 w-3" />
          {{ equipment.is_available ? 'Disponible' : 'Indisponible' }}
        </Badge>
      </div>
    </Link>

    <div class="p-6">
      <!-- Header -->
      <div class="flex items-start justify-between gap-4">
        <Link 
          :href="route('equipments.show', equipment)"
          class="block"
        >
          <h3 class="text-lg font-semibold text-foreground group-hover:text-primary transition-colors duration-200 line-clamp-2">
            {{ equipment.name }}
          </h3>
        </Link>
      </div>

      <!-- Info Grid -->
      <div class="mt-4 space-y-3">
        <div class="flex items-center text-sm text-muted-foreground">
          <MapPin class="h-4 w-4 mr-2 text-primary/70 flex-shrink-0" />
          <span class="truncate">{{ equipment.depot?.city || 'Non localisé' }}</span>
        </div>
        <div class="flex items-center text-sm text-muted-foreground">
          <Tag class="h-4 w-4 mr-2 text-primary/70 flex-shrink-0" />
          <span class="truncate">{{ equipment.category?.name || 'Sans catégorie' }}</span>
        </div>
        <div class="flex items-center text-sm text-muted-foreground">
          <Euro class="h-4 w-4 mr-2 text-primary/70 flex-shrink-0" />
          <span>{{ formatPrice(equipment.rental_price) }} / jour</span>
        </div>
      </div>

      <!-- Rental Period Info -->
      <div 
        v-if="rentalPeriod"
        class="mt-4 p-3 bg-accent/50 rounded-lg space-y-2"
      >
        <div class="flex items-center text-sm">
          <Calendar class="h-4 w-4 mr-2 text-primary/70" />
          <span class="text-muted-foreground">
            {{ rentalPeriod.start }} - {{ rentalPeriod.end }}
          </span>
        </div>
        <div class="flex items-center justify-between text-sm font-medium">
          <span>Total ({{ rentalPeriod.days }} jour{{ rentalPeriod.days > 1 ? 's' : '' }})</span>
          <span class="text-foreground">{{ formatPrice(calculateTotalPrice) }}</span>
        </div>
      </div>

      <!-- Description -->
      <p class="mt-4 text-sm text-muted-foreground line-clamp-2">
        {{ equipment.description }}
      </p>

      <!-- Actions -->
      <div class="mt-6">
        <Button
          asChild
          class="w-full group/button"
        >
          <Link :href="route('equipments.show', equipment)">
            Voir les détails
            <ArrowRight class="ml-2 h-4 w-4 transition-transform group-hover/button:translate-x-1" />
          </Link>
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { 
  MapPin, 
  Tag, 
  Euro, 
  Calendar,
  ArrowRight,
  ShieldCheck,
  AlertTriangle
} from 'lucide-vue-next';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

const props = defineProps({
  equipment: {
    type: Object,
    required: true
  },
  startDate: {
    type: String,
    default: ''
  },
  endDate: {
    type: String,
    default: ''
  }
});

const calculateTotalPrice = computed(() => {
  if (!props.startDate || !props.endDate) {
    return props.equipment.rental_price;
  }

  const start = new Date(props.startDate);
  const end = new Date(props.endDate);
  const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
  
  return (props.equipment.rental_price * days).toFixed(2);
});

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price);
};

const rentalPeriod = computed(() => {
  if (!props.startDate || !props.endDate) return null;
  
  const start = new Date(props.startDate);
  const end = new Date(props.endDate);
  const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
  
  return {
    start: format(start, 'dd MMM yyyy', { locale: fr }),
    end: format(end, 'dd MMM yyyy', { locale: fr }),
    days
  };
});
</script> 