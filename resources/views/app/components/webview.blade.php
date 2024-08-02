@if(isset($src) || isset($html))
    @php($html ??= file_get_contents($src))
    <div wire:ignore x-data="{
        html: @js($html),
    }">
        <embedded-webview-{{ md5($id ?? $html) }} x-bind:html="html" />

        <script data-navigate-track>
            if (! window.customElements.get('embedded-webview-{{ md5($id ?? $html) }}')) {
                window.customElements.define('embedded-webview-{{ md5($id ?? $html) }}', class extends HTMLElement {
                    static observedAttributes = ["html"];

                    attributeChangedCallback(name, oldValue, newValue) {
                        const shadow = this.attachShadow({ mode: 'closed' });
                        shadow.innerHTML = newValue;
                    }
                });
            }
        </script>
    </div>
@endif
