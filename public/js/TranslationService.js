import { translations } from './data.js';

/**
 * Gère la traduction de l'interface utilisateur.
 */
export class TranslationService {
    constructor(initialLang = 'fr') {
        this.translations = translations;
        // On récupère la langue depuis le localStorage, ou on utilise la langue par défaut
        this.currentLang = localStorage.getItem('campus-connect-lang') || initialLang;
    }

    setLanguage(lang) {
        if (!this.translations[lang]) {
            console.warn(`Langue ${lang} non supportée.`);
            return;
        }
        this.currentLang = lang;
        // On sauvegarde la langue choisie dans le localStorage pour la persistance
        localStorage.setItem('campus-connect-lang', lang);
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