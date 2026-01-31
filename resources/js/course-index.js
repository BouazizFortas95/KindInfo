const initializeCourseIndex = () => {
    const config = window.courseIndexConfig;
    if (!config) return;
    const { translations, direction } = config;

    function loadCSS(url) {
        if (!document.querySelector(`link[href="${url}"]`)) {
            const link = document.createElement('link');
            link.href = url;
            link.rel = 'stylesheet';
            document.head.appendChild(link);
        }
    }

    function loadScript(url, callback) {
        const script = document.createElement('script');
        script.src = url;
        script.onload = callback;
        document.head.appendChild(script);
    }

    // Load Select2 CSS
    loadCSS('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');

    // Load jQuery if not present
    if (typeof jQuery === 'undefined') {
        loadScript('https://code.jquery.com/jquery-3.6.0.min.js', function() {
            loadSelect2();
        });
    } else {
        loadSelect2();
    }

    function loadSelect2() {
        if (typeof jQuery.fn.select2 === 'undefined') {
            loadScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', function() {
                setTimeout(initializeEnhancements, 500);
            });
        } else {
            setTimeout(initializeEnhancements, 100);
        }
    }

    function initializeEnhancements() {
        // Ensure direction is set on initialization
        setupSelect2();

        // Listen for Livewire updates to reinitialize
        document.addEventListener('livewire:navigated', setupSelect2);
        document.addEventListener('livewire:updated', function() {
            setTimeout(setupSelect2, 50);
        });

        // Backup observer for dynamic changes
        const observer = new MutationObserver((mutations) => {
            if (document.querySelector('select[wire\\:model\\.live="selectedCategories"]') &&
                !document.querySelector('.select2-container')) {
                setupSelect2();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    function setupSelect2() {
        try {
            const categorySelect = jQuery('select[wire\\:model\\.live="selectedCategories"]');
            const progressSelect = jQuery('select[wire\\:model\\.live="progressFilter"]');

            // Destroy existing instances
            if (categorySelect.hasClass('select2-hidden-accessible')) {
                categorySelect.select2('destroy');
            }
            if (progressSelect.hasClass('select2-hidden-accessible')) {
                progressSelect.select2('destroy');
            }

            // Initialize category select with Select2
            if (categorySelect.length > 0) {
                categorySelect.select2({
                    placeholder: translations.select_categories,
                    allowClear: true,
                    multiple: true,
                    closeOnSelect: false,
                    width: '100%',
                    dir: direction,
                    language: {
                        noResults: function() {
                            return translations.no_categories_found;
                        },
                        searching: function() {
                            return translations.searching;
                        }
                    }
                }).on('select2:select select2:unselect', function() {
                    // Trigger Livewire update
                    const event = new Event('change', {
                        bubbles: true
                    });
                    this.dispatchEvent(event);
                });
            }

            // Initialize progress select with Select2
            if (progressSelect.length > 0) {
                progressSelect.select2({
                    placeholder: translations.select_progress,
                    allowClear: false,
                    width: '100%',
                    dir: direction,
                    minimumResultsForSearch: Infinity,
                    templateResult: function(option) {
                        if (!option.id) return option.text;

                        const icons = {
                            'all': '📚',
                            'not_started': '⏱️',
                            'started': '▶️',
                            'completed': '✅'
                        };

                        const icon = icons[option.id] || '';
                        return jQuery('<span><span style="margin-right: 8px;">' + icon + '</span>' +
                            option.text + '</span>');
                    }
                }).on('select2:select', function() {
                    // Trigger Livewire update
                    const event = new Event('change', {
                        bubbles: true
                    });
                    this.dispatchEvent(event);
                });
            }

        } catch (error) {
            console.log('Select2 initialization error:', error);
        }
    }
}
