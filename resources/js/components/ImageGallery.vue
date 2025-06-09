<script setup>
import { AspectRatio } from '@/components/ui/aspect-ratio'
import { Card, CardContent } from '@/components/ui/card'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { ref } from 'vue'

const props = defineProps({
    images: {
        type: Array,
        required: true
    }
})

const featuredImage = props.images[0]
const remainingImages = props.images.slice(1)

const selectedImage = ref(null)
const isDialogOpen = ref(false)

const openImageDialog = (image) => {
    selectedImage.value = image
    isDialogOpen.value = true
}
</script>

<template>
    <div class="space-y-6">
        <!-- Featured Image -->
        <Card 
            v-if="featuredImage" 
            class="overflow-hidden cursor-pointer"
            @click="openImageDialog(featuredImage)"
        >
            <CardContent class="p-0">
                <AspectRatio :ratio="16/9">
                    <img 
                        :src="featuredImage.url" 
                        :alt="featuredImage.original_name"
                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                    >
                </AspectRatio>
            </CardContent>
        </Card>

        <!-- Remaining Images Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <Card 
                v-for="image in remainingImages" 
                :key="image.id" 
                class="overflow-hidden group cursor-pointer"
                @click="openImageDialog(image)"
            >
                <CardContent class="p-0">
                    <AspectRatio :ratio="1">
                        <img 
                            :src="image.url" 
                            :alt="image.original_name" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                    </AspectRatio>
                </CardContent>
            </Card>
        </div>

        <!-- Image Dialog -->
        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="max-w-screen-lg p-0">
                <div class="relative">
                    <button 
                        @click="isDialogOpen = false"
                        class="absolute top-2 right-2 z-50 rounded-full bg-black/50 p-2 text-white hover:bg-black/75 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    </button>
                    <img 
                        v-if="selectedImage"
                        :src="selectedImage.url" 
                        :alt="selectedImage.original_name"
                        class="w-full h-auto"
                    >
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>