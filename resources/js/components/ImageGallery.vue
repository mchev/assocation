<script setup>
import { AspectRatio } from '@/components/ui/aspect-ratio'
import { Card, CardContent } from '@/components/ui/card'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { 
    ChevronLeft, 
    ChevronRight, 
    X, 
    ZoomIn, 
    ZoomOut,
    Loader2,
    Image
} from 'lucide-vue-next'

const props = defineProps({
    images: {
        type: Array,
        required: true
    }
})

const selectedImage = ref(null)
const isDialogOpen = ref(false)
const currentImageIndex = ref(0)
const isLoading = ref(false)
const zoomLevel = ref(1)

const allImages = computed(() => props.images)

// Calculate how many placeholder cards we need
const placeholderCount = computed(() => {
    const count = allImages.value.length
    if (count === 0) return 3
    if (count === 1) return 2
    if (count === 2) return 1
    return 0
})

const openImageDialog = (image) => {
    selectedImage.value = image
    currentImageIndex.value = allImages.value.findIndex(img => img.id === image.id)
    isDialogOpen.value = true
    zoomLevel.value = 1
}

const handleKeydown = (e) => {
    if (!isDialogOpen.value) return
    
    switch (e.key) {
        case 'ArrowLeft':
            navigateImage('prev')
            break
        case 'ArrowRight':
            navigateImage('next')
            break
        case 'Escape':
            isDialogOpen.value = false
            break
    }
}

const navigateImage = (direction) => {
    const totalImages = allImages.value.length
    
    if (direction === 'next') {
        currentImageIndex.value = (currentImageIndex.value + 1) % totalImages
    } else {
        currentImageIndex.value = (currentImageIndex.value - 1 + totalImages) % totalImages
    }
    
    selectedImage.value = allImages.value[currentImageIndex.value]
    zoomLevel.value = 1
}

const handleImageLoad = () => {
    isLoading.value = false
}

const handleZoom = (direction) => {
    if (direction === 'in' && zoomLevel.value < 3) {
        zoomLevel.value += 0.5
    } else if (direction === 'out' && zoomLevel.value > 1) {
        zoomLevel.value -= 0.5
    }
}

// Touch handling for swipe
const touchStart = ref({ x: 0, y: 0 })
const handleTouchStart = (e) => {
    touchStart.value = {
        x: e.touches[0].clientX,
        y: e.touches[0].clientY
    }
}

const handleTouchEnd = (e) => {
    const touchEnd = {
        x: e.changedTouches[0].clientX,
        y: e.changedTouches[0].clientY
    }
    
    const deltaX = touchEnd.x - touchStart.value.x
    const deltaY = touchEnd.y - touchStart.value.y
    
    // Only handle horizontal swipes
    if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
        if (deltaX > 0) {
            navigateImage('prev')
        } else {
            navigateImage('next')
        }
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
    <div class="space-y-6">
        <!-- Images Grid -->
        <div class="grid grid-cols-3 gap-2 sm:gap-4">
            <!-- Actual Images -->
            <Card 
                v-for="image in allImages" 
                :key="image.id" 
                class="overflow-hidden group cursor-pointer p-0"
                @click="openImageDialog(image)"
            >
                <CardContent class="p-0 relative h-full w-full">
                    <img 
                        :src="image.url" 
                        :alt="image.original_name" 
                        class="object-cover group-hover:scale-105 transition-transform duration-300 h-full w-full"
                        loading="lazy"
                    >
                    <div class="absolute inset-0 bg-black/40 dark:bg-white/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <ZoomIn class="w-4 h-4 sm:w-6 sm:h-6 text-white dark:text-black" />
                    </div>
                </CardContent>
            </Card>

            <!-- Placeholder Cards -->
            <Card 
                v-for="i in placeholderCount" 
                :key="`placeholder-${i}`"
                class="overflow-hidden bg-gray-50/50 dark:bg-gray-900/50"
            >
                <CardContent class="p-0">
                    <AspectRatio :ratio="4/3">
                        <div class="w-full h-full flex items-center justify-center">
                            <Image class="w-4 h-4 sm:w-8 sm:h-8 text-gray-200 dark:text-gray-700" />
                        </div>
                    </AspectRatio>
                </CardContent>
            </Card>
        </div>

        <!-- Image Dialog -->
        <Dialog v-model:open="isDialogOpen" class="z-50">
            <DialogContent 
                class="max-w-screen-xl p-0"
                @touchstart="handleTouchStart"
                @touchend="handleTouchEnd"
            >
                <div class="relative bg-black">
                    <!-- Close button -->
                    <button 
                        @click="isDialogOpen = false"
                        class="absolute top-4 right-4 z-50 rounded-full bg-black/50 dark:bg-white/50 p-2 text-white dark:text-black hover:bg-black/75 dark:hover:bg-white/75 transition-colors"
                        aria-label="Close dialog"
                    >
                        <X class="w-6 h-6" />
                    </button>

                    <!-- Zoom controls -->
                    <div class="absolute top-4 left-4 z-50 flex gap-2">
                        <button 
                            @click="handleZoom('out')"
                            class="rounded-full bg-black/50 dark:bg-white/50 p-2 text-white dark:text-black hover:bg-black/75 dark:hover:bg-white/75 transition-colors"
                            :disabled="zoomLevel <= 1"
                            aria-label="Zoom out"
                        >
                            <ZoomOut class="w-6 h-6" />
                        </button>
                        <button 
                            @click="handleZoom('in')"
                            class="rounded-full bg-black/50 dark:bg-white/50 p-2 text-white dark:text-black hover:bg-black/75 dark:hover:bg-white/75 transition-colors"
                            :disabled="zoomLevel >= 3"
                            aria-label="Zoom in"
                        >
                            <ZoomIn class="w-6 h-6" />
                        </button>
                    </div>

                    <!-- Navigation buttons -->
                    <button 
                        v-if="currentImageIndex > 0"
                        @click="navigateImage('prev')"
                        class="absolute left-4 top-1/2 -translate-y-1/2 z-50 rounded-full bg-black/50 dark:bg-white/50 p-2 text-white dark:text-black hover:bg-black/75 dark:hover:bg-white/75 transition-colors"
                        aria-label="Previous image"
                    >
                        <ChevronLeft class="w-8 h-8" />
                    </button>
                    
                    <button 
                        v-if="currentImageIndex < allImages.length - 1"
                        @click="navigateImage('next')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 z-50 rounded-full bg-black/50 dark:bg-white/50 p-2 text-white dark:text-black hover:bg-black/75 dark:hover:bg-white/75 transition-colors"
                        aria-label="Next image"
                    >
                        <ChevronRight class="w-8 h-8" />
                    </button>

                    <!-- Loading spinner -->
                    <div 
                        v-if="isLoading"
                        class="absolute inset-0 flex items-center justify-center bg-black/50 dark:bg-white/50"
                    >
                        <Loader2 class="w-8 h-8 text-white dark:text-black animate-spin" />
                    </div>

                    <!-- Image -->
                    <div 
                        class="overflow-auto max-h-[90vh] flex items-center justify-center bg-gray-50 dark:bg-gray-900"
                        :style="{ cursor: zoomLevel > 1 ? 'move' : 'default' }"
                    >
                        <img 
                            v-if="selectedImage"
                            :src="selectedImage.url" 
                            :alt="selectedImage.original_name"
                            class="w-full h-auto object-contain transition-transform duration-300"
                            :style="{ transform: `scale(${zoomLevel})` }"
                            @load="handleImageLoad"
                            loading="lazy"
                        >
                    </div>

                    <!-- Image counter -->
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/50 dark:bg-white/50 px-3 py-1 rounded-full text-white dark:text-black text-sm">
                        {{ currentImageIndex + 1 }} / {{ allImages.length }}
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>