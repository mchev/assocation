<script setup>
import { CalendarDate, DateFormatter, getLocalTimeZone } from '@internationalized/date'
import { CalendarIcon } from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'
import { Button } from '@/components/ui/button'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { RangeCalendar } from '@/components/ui/range-calendar'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({ from: null, to: null })
  }
})

const emit = defineEmits(['update:modelValue'])

const value = computed({
  get: () => ({
    start: props.modelValue.from,
    end: props.modelValue.to
  }),
  set: (newValue) => {
    emit('update:modelValue', {
      from: newValue.start,
      to: newValue.end
    })
  }
})

const df = new DateFormatter('fr-FR', {
  dateStyle: 'medium',
})

const buttonClasses = computed(() => {
  return [
    'w-[280px] justify-start text-left font-normal',
    !value.value && 'text-muted-foreground'
  ].filter(Boolean).join(' ')
})
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button
        variant="outline"
        :class="buttonClasses"
      >
        <CalendarIcon class="mr-2 h-4 w-4" />
        <template v-if="value.start">
          <template v-if="value.end">
            {{ df.format(value.start.toDate(getLocalTimeZone())) }} - {{ df.format(value.end.toDate(getLocalTimeZone())) }}
          </template>
          <template v-else>
            {{ df.format(value.start.toDate(getLocalTimeZone())) }}
          </template>
        </template>
        <template v-else>
          Choisir une date
        </template>
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-auto p-0">
      <RangeCalendar 
        v-model="value" 
        initial-focus 
        :number-of-months="1" 
        @update:modelValue="(newValue) => {
          value.start = newValue.start;
          value.end = newValue.end;
        }"
      />
    </PopoverContent>
  </Popover>
</template>