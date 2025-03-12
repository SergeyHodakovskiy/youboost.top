// assets/js/core/locale-service.js
export class LocaleService {
    static detectLocale() {
        const savedLocale = localStorage.getItem('user_locale');
        if (savedLocale) {
            return savedLocale;
        }

        const browserLocale = navigator.language.split('-')[0];
        return browserLocale;
    }

    static async setLocale(locale) {
        try {
            const response = await fetch('/api/set-locale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ locale })
            });

            if (response.ok) {
                localStorage.setItem('user_locale', locale);
                window.location.reload();
            }
        } catch (error) {
            console.error('Locale set error:', error);
        }
    }
}