/**
 * Gère l'état de l'interface, comme l'affichage/masquage des vues et des éléments.
 */
export class UIManager {
    constructor() {
        this.views = {
            'view-feed': document.getElementById('view-feed'),
            'view-profile': document.getElementById('view-profile'),
            'view-messages': document.getElementById('view-messages'),
            'view-friends': document.getElementById('view-friends'),
            'view-settings': document.getElementById('view-settings')
        };
        this.rightFeed = document.getElementById('right-feed');
        this.rightProfile = document.getElementById('right-profile');
        this.viewAuth = document.getElementById('view-auth');
        this.viewApp = document.getElementById('view-app');
        this.composerOverlay = document.getElementById('composer-overlay');
        this.navLinks = document.querySelectorAll('.nav-link');
    }

    showAuth() {
        if (this.viewAuth) this.viewAuth.style.display = 'flex';
        if (this.viewApp) this.viewApp.style.display = 'none';
        this.hideComposer();
    }

    showApp() {
        if (this.viewAuth) this.viewAuth.style.display = 'none';
        if (this.viewApp) this.viewApp.style.display = 'block';
        this.activateNavLink('link-home');
        this.showView('view-feed');
    }

    showView(viewId) {
        Object.values(this.views).forEach(view => {
            if (view) view.style.display = 'none';
        });

        if (this.views[viewId]) {
            this.views[viewId].style.display = 'block';
        }
        this.updateRightColumn(viewId);
    }
    
    activateNavLink(linkId) {
        this.navLinks.forEach(l => l.classList.remove('active'));
        const link = document.getElementById(linkId);
        if (link) {
            link.classList.add('active');
        }
    }

    updateRightColumn(viewId) {
        const showProfile = viewId === 'view-profile';
        const showFeed = viewId === 'view-feed';
        
        if (this.rightProfile) this.rightProfile.style.display = showProfile ? 'block' : 'none';
        if (this.rightFeed) this.rightFeed.style.display = showFeed ? 'block' : 'none';
    }

    showComposer() {
        if (this.composerOverlay) {
            this.composerOverlay.style.display = 'flex';
            this.composerOverlay.setAttribute('aria-hidden', 'false');
        }
    }

    hideComposer() {
        if (this.composerOverlay) {
            this.composerOverlay.style.display = 'none';
            this.composerOverlay.setAttribute('aria-hidden', 'true');
        }
    }
}