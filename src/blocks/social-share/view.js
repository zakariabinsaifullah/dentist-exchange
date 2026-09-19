document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.dnte-social-share__item.is-copy-link').forEach(function (button) {
        button.addEventListener('click', function () {
            const url = this.dataset.copyUrl;
            if (!url) return;

            const self = this;

            const onCopied = () => {
                self.classList.add('is-copied');
                setTimeout(() => self.classList.remove('is-copied'), 2000);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(onCopied);
            } else {
                // Fallback for non-secure contexts.
                const input = document.createElement('input');
                input.value = url;
                input.style.cssText = 'position:absolute;left:-9999px;top:-9999px';
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                onCopied();
            }
        });
    });
});
