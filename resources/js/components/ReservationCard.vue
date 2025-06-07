<script setup>
import { computed } from 'vue'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent } from '@/components/ui/card'

const props = defineProps({
    reservation: {
        type: Object,
        required: true
    },
    showOrganization: {
        type: String,
        required: true,
        validator: (value) => ['lender', 'borrower'].includes(value)
    }
})

const organization = computed(() => {
    return props.showOrganization === 'lender'
        ? props.reservation.lenderOrganization
        : props.reservation.borrowerOrganization
})

const formatDate = (date) => {
    return format(new Date(date), 'dd MMM yyyy', { locale: fr })
}

const dateRange = computed(() => {
    return `${formatDate(props.reservation.start_date)} - ${formatDate(props.reservation.end_date)}`
})

const equipmentList = computed(() => {
    return props.reservation.items
        .map(item => item.equipment.name)
        .join(', ')
})
</script>

<template>
    <Card>
        <CardContent class="pt-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-medium">{{ organization.name }}</h3>
                    <p class="text-sm text-muted-foreground">{{ dateRange }}</p>
                </div>
                <Badge :variant="reservation.status.color">
                    {{ reservation.status.label }}
                </Badge>
            </div>
            <p class="mt-2 text-sm">{{ equipmentList }}</p>
        </CardContent>
    </Card>
</template> 