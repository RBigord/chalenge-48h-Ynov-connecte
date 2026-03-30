import { translations } from './data.js';

/**
 * Gère la traduction de l'interface utilisateur.
 */
export class TranslationService {
    constructor(initialLang = 'fr') {
        this.translations = translations;
        this.currentLang = initialLang;
    }

    setLanguage(lang) {
        if (!this.translations[lang]) {
            console.warn(`Langue ${lang} non supportée.`);
            return;
        }
        this.currentLang = lang;
        this.translatePage();
    }

    translatePage() {
        document.querySelectorAll('[data-translate-key]').forEach(el => {
            const key = el.getAttribute('data-translate-key');
            const translation = this.translations[this.currentLang][key];
            if (translation !== undefined) {
                el.textContent = translation;
            }
        });
    }
}