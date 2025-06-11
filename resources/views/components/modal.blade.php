@props(['name', 'show' => false, 'maxWidth' => '2xl', 'focusable' => true])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        focusable: @js($focusable),
        focusableElements: null,
        lastFocusedElement: null,
        init() {
            this.$watch('show', value => {
                if (value) {
                    this.lastFocusedElement = document.activeElement;
                    this.$nextTick(() => {
                        this.focusableElements = this.getFocusableElements();
                        if (this.focusableElements.length > 0 && this.focusable) {
                            this.focusableElements[0].focus();
                        }
                    });
                } else {
                    if (this.lastFocusedElement) {
                        this.lastFocusedElement.focus();
                    }
                }
            });

            this.$watch('focusable', value => {
                if (value && this.show) {
                    this.$nextTick(() => {
                        this.focusableElements = this.getFocusableElements();
                        if (this.focusableElements.length > 0) {
                            this.focusableElements[0].focus();
                        }
                    });
                }
            });

            const handleEscape = (e) => {
                if (e.key === 'Escape' && this.show) {
                    this.show = false;
                }
            };

            document.addEventListener('keydown', handleEscape);
            this.$cleanup = () => {
                document.removeEventListener('keydown', handleEscape);
            };
        },
        getFocusableElements() {
            return [...this.$el.querySelectorAll('a, button, input, textarea, select, details, [tabindex]:not([tabindex=\'-1\'])')].filter(el => !el.hasAttribute('disabled'));
        }
    }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey ? (focusableElements.indexOf($event.target) > 0 ? focusableElements[focusableElements.indexOf($event.target) - 1].focus() : focusableElements[focusableElements.length - 1].focus()) : (focusableElements.indexOf($event.target) < focusableElements.length - 1 ? focusableElements[focusableElements.indexOf($event.target) + 1].focus() : focusableElements[0].focus())"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    style="display: none;"
>
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div
        x-show="show"
        class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        {{ $slot }}
    </div>
</div>
