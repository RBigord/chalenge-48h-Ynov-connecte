import { UIManager } from './UIManager.js';
import { ApiService } from './ApiService.js';
import { TranslationService } from './TranslationService.js';
import { PostRenderer } from './PostRenderer.js';
import { ProfileRenderer } from './ProfileRenderer.js';
import { mockUsers, mockContacts, mockFriends } from './data.js';

/**
 * CampusConnect - App Frontend
 * Chef d'orchestre de l'application.
 */
class CampusConnectApp {
    constructor() {
        // Services et gestionnaires principaux
        this.uiManager = new UIManager();
        this.apiService = new ApiService();
        this.translationService = new TranslationService('fr');

        // Références DOM pour les écouteurs d'événements
        this.postsContainer = document.getElementById('posts-container');
        this.init();
    }

    // Initialisation de l'application
    init() {
        console.log("CampusConnect Initialized");
        this.initNavigation();
        this.initAuth();
        this.initFeed();
        this.initProfilePage();
        this.initLangSwitcher();
        this.renderMessages();
        this.renderFriends();
        this.initComposer();
        this.initInteractiveButtons();
        this.initChatbot();
    }

    initNavigation() {
        // Avec une approche multi-pages, la navigation principale est gérée par les href.
        // Le JS sert maintenant à mettre en évidence le lien actif.
        const currentPage = window.location.pathname.split('/').pop();
        this.uiManager.navLinks.forEach(link => {
            const linkPage = link.getAttribute('href');
            // Gère le cas où on est à la racine (index.html)
            if (linkPage === currentPage || (currentPage === '' && linkPage === 'index.html')) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        // La logique pour le clic sur l'icône de profil doit maintenant rediriger.
        const profileIcons = document.querySelectorAll('.profile-icon');
        profileIcons.forEach(icon => {
            icon.addEventListener('click', () => {
                window.location.href = 'profile.html';
            });
        });
    }

    initAuth() {
        const btnAuthLogin = document.getElementById('btn-auth-login');
        const btnAuthSignup = document.getElementById('btn-auth-signup');
        const logoutLink = document.querySelector('.logout');

        // Sur les pages d'authentification, la connexion redirige vers la page principale.
        if (btnAuthLogin) btnAuthLogin.addEventListener('click', () => {
            window.location.href = 'index.html';
        });
        if (btnAuthSignup) btnAuthSignup.addEventListener('click', () => {
            window.location.href = 'index.html';
        });

        if (logoutLink) logoutLink.addEventListener('click', (e) => {
            e.preventDefault();
            // Redirige vers la page de connexion, car nous sommes maintenant dans une architecture multi-pages.
            window.location.href = 'auth.html';
        });
        this.initAuthTabs();
    }

    async initFeed() {
        if (!this.postsContainer) return;
        
        this.postsContainer.addEventListener('click', (e) => {
            const authorInfo = e.target.closest('.post-author-info');
            if (authorInfo) {
                const authorName = authorInfo.dataset.authorName;
                if (authorName) {
                    window.location.href = `profile.html?user=${encodeURIComponent(authorName)}`;
                }
            }
        });

        const posts = await this.apiService.getPosts();
        this.postsContainer.innerHTML = '';
        posts.forEach(post => {
            this.postsContainer.insertAdjacentHTML('beforeend', PostRenderer.render(post));
        });
    }
    
    initProfilePage() {
        const profileContainer = document.getElementById('profile-posts-container');
        if (profileContainer) {
            profileContainer.addEventListener('click', (e) => {
                const editIcon = e.target.closest('.profile-edit-icon');
                if (editIcon) {
                    this.handleProfileEdit(editIcon);
                }
                const deleteBtn = e.target.closest('.skill-delete-btn');
                if (deleteBtn) {
                    deleteBtn.closest('.skill-tech-row').remove();
                }
                const addBtn = e.target.closest('#btn-add-skill');
                if (addBtn) {
                    const list = document.querySelector('.skill-tech-list');
                    if (list) this.addNewSkillRow(list);
                }
            });

            // Gérer le chargement dynamique du profil
            const urlParams = new URLSearchParams(window.location.search);
            const userName = urlParams.get('user') || 'Leila Martinez';
            const isCurrentUser = userName === 'Leila Martinez';
            this.renderProfile(userName, isCurrentUser);
        }
        this.initProfileTabs();
    }

    async renderProfile(userName, isCurrentUser) {
        const profileData = await this.apiService.getUser(userName);
        if (!profileData) return;

        // Mettre à jour la carte d'en-tête du profil
        document.getElementById('profile-name').textContent = profileData.name;
        document.getElementById('profile-bio').textContent = profileData.bio;
        document.getElementById('profile-picture').src = profileData.avatar;
        document.getElementById('profile-email').textContent = profileData.email;
        document.getElementById('profile-location').textContent = profileData.location;
        document.getElementById('stat-posts').textContent = profileData.stats.competences;
        document.getElementById('stat-followers').textContent = profileData.stats.followers;
        document.getElementById('stat-following').textContent = profileData.stats.following;
        document.getElementById('stat-groups').textContent = profileData.stats.groups;

        // Afficher/masquer les boutons d'action
        document.getElementById('btn-edit-profile').style.display = isCurrentUser ? 'inline-flex' : 'none';
        document.getElementById('btn-follow-profile').style.display = isCurrentUser ? 'none' : 'inline-flex';
        document.getElementById('btn-message-profile').style.display = isCurrentUser ? 'none' : 'inline-flex';

        // Afficher le contenu principal du profil
        const profileContainer = document.getElementById('profile-posts-container');
        if (profileContainer) {
            profileContainer.innerHTML = ProfileRenderer.render(profileData, isCurrentUser);
        }
        
        this.initProfileTabs();
    }

    // Le reste des méthodes (renderMessages, renderFriends, initComposer, etc.) reste ici pour le moment.
    // Elles pourront être extraites dans leurs propres classes de "manager" dans une prochaine étape.
    levelToPercent(level) {
        if (!level) return 10;
        const l = level.toLowerCase();
        if (l.includes('expert')) return 95;
        if (l.includes('avancé')) return 75;
        if (l.includes('intermédiaire')) return 55;
        if (l.includes('débutant')) return 30;
        // Gérer le cas où l'utilisateur entre un nombre
        const percent = parseInt(l.replace('%', ''));
        return !isNaN(percent) && percent >= 0 && percent <= 100 ? percent : 10;
    }

    handleProfileEdit(icon) {
        const card = icon.closest('.profile-section-card');
        if (!card) return;
    
        const isEditing = card.getAttribute('data-editing') === 'true';
    
        if (!isEditing) { // --- ENTRER EN MODE ÉDITION ---
            card.classList.add('is-editing-card');
            icon.classList.replace('fa-pen-to-square', 'fa-check');
            icon.title = 'Save';
            card.setAttribute('data-editing', 'true');
    
            const editableNodes = card.querySelectorAll('.skill-tech-name, .trans-chip, .project-title, .project-desc, .link-label, .link-url');
            editableNodes.forEach(node => {
                node.setAttribute('contenteditable', 'true');
                node.classList.add('is-editing');
            });
    
            // Remplacer le texte du niveau par un sélecteur
            if (card.querySelector('.skill-tech-list')) {
                card.querySelectorAll('.skill-tech-row').forEach(row => {
                    const levelEl = row.querySelector('.skill-tech-level');
                    if (!levelEl) return;
                    const currentLevel = levelEl.textContent.trim();
                    const select = document.createElement('select');
                    select.className = 'skill-level-select is-editing';
                    const levels = ['Débutant', 'Intermédiaire', 'Avancé', 'Expert'];
                    levels.forEach(level => {
                        const option = document.createElement('option');
                        option.value = level;
                        option.textContent = level;
                        if (level === currentLevel) option.selected = true;
                        select.appendChild(option);
                    });
                    levelEl.replaceWith(select);
                });
            }
        } else { // --- QUITTER LE MODE ÉDITION (SAUVEGARDER) ---
            card.classList.remove('is-editing-card');
            icon.classList.replace('fa-check', 'fa-pen-to-square');
            icon.title = 'Edit';
            card.setAttribute('data-editing', 'false');
    
            const editableNodes = card.querySelectorAll('[contenteditable="true"]');
            editableNodes.forEach(node => {
                node.setAttribute('contenteditable', 'false');
                node.classList.remove('is-editing');
            });
    
            // Remplacer le sélecteur par du texte et mettre à jour la barre
            if (card.querySelector('.skill-tech-list')) {
                card.querySelectorAll('.skill-tech-row').forEach(row => {
                    const select = row.querySelector('.skill-level-select');
                    if (select) {
                        const newLevel = select.value;
                        const newLevelEl = document.createElement('div');
                        newLevelEl.className = 'skill-tech-level';
                        newLevelEl.textContent = newLevel;
                        select.replaceWith(newLevelEl);
    
                        const fillEl = row.querySelector('.skill-fill');
                        if (fillEl) fillEl.style.width = `${this.levelToPercent(newLevel)}%`;
                    }
                });
            }
        }
    }

    addNewSkillRow(list) {
        const newRowHTML = `
            <div class="skill-tech-row">
                <div class="skill-tech-top">
                    <div class="skill-tech-name is-editing" contenteditable="true">Nouvelle compétence</div>
                    <div class="skill-tech-level is-editing" contenteditable="true">Débutant</div>
                </div>
                <div class="skill-track">
                    <div class="skill-fill" style="width:30%"></div>
                </div>
                <button class="skill-delete-btn" title="Supprimer la compétence">&times;</button>
            </div>
        `;
        list.insertAdjacentHTML('beforeend', newRowHTML);
        // Se concentrer sur le nouvel élément pour encourager la modification
        const newSkillName = list.lastElementChild.querySelector('.skill-tech-name');
        if (newSkillName) newSkillName.focus();
    }

    // Rendu des données Mockées de la Messagerie
    renderMessages() {
        const contactsContainer = document.getElementById('contacts-list');
        const chatHistory = document.getElementById('chat-history');
        const chatHeader = document.getElementById('chat-header');

        const urlParams = new URLSearchParams(window.location.search);
        const contactName = urlParams.get('contact');

        if (contactsContainer) {
            mockContacts.forEach(contact => {
                const li = document.createElement('li');
                li.className = 'contact-item';
                li.innerHTML = `
                    <img src="${contact.avatar}" alt="${contact.name}" class="avatar-small">
                    <div class="contact-info">
                        <h4>${contact.name}</h4>
                        <p>${contact.lastMessage}</p>
                    </div>
                `;
                contactsContainer.appendChild(li);
            });
            
            // Si on a un nouveau contact de l'URL qui n'est pas dans la liste
            if (contactName && !mockContacts.some(c => c.name === contactName)) {
                const li = document.createElement('li');
                li.className = 'contact-item';
                li.innerHTML = `
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(contactName)}&background=2563eb&color=fff" alt="${contactName}" class="avatar-small">
                    <div class="contact-info">
                        <h4>${contactName}</h4>
                        <p>Nouvelle discussion</p>
                    </div>
                `;
                contactsContainer.prepend(li);
            }
        }

        if (chatHistory && chatHeader) {
            let activeContact = mockContacts[0];
            if (contactName) {
                const found = mockContacts.find(c => c.name === contactName);
                if (found) activeContact = found;
                else activeContact = { name: contactName, avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(contactName)}&background=2563eb&color=fff` };
            }

            chatHeader.innerHTML = `<h3><img src="${activeContact.avatar}" class="avatar-small" style="vertical-align: middle; margin-right: 10px;"> ${activeContact.name}</h3>`;
            chatHistory.innerHTML = `
                <div class="chat-bubble receiver">Salut ! Tu es prêt pour le challenge 48h ?</div>
                <div class="chat-bubble sender">Oui ! J'ai hâte de commencer à coder. 🚀</div>
            `;
        }
    }

    // Rendu des données Mockées de la section Amis
    renderFriends() {
        const friendsContainer = document.getElementById('friends-list-container');
        const friendsCount = document.getElementById('friends-count');
        const btnInvite = document.getElementById('btn-invite-friend');
        const inputInvite = document.getElementById('invite-friend-input');

        if (!friendsContainer || !btnInvite || !inputInvite) return;

        const renderList = () => {
            friendsContainer.innerHTML = '';
            mockFriends.forEach(friend => {
                const friendHTML = `
                    <li class="friend-item">
                        <img src="${friend.avatar}" alt="${friend.name}" class="avatar-small">
                        <div class="friend-item-info">
                            <h4>${friend.name}</h4>
                            <p class="muted small">${friend.promo}</p>
                        </div>
                        <div class="friend-item-actions">
                            <button class="btn-outline-sm" style="padding: 8px 12px; font-size: 0.8rem;">Message</button>
                            <button class="btn-danger-full" style="padding: 8px 12px; font-size: 0.8rem; width: auto; margin-top: 0;">Retirer</button>
                        </div>
                    </li>
                `;
                friendsContainer.insertAdjacentHTML('beforeend', friendHTML);
            });
            if(friendsCount) friendsCount.textContent = mockFriends.length;
        };

        btnInvite.addEventListener('click', () => {
            const name = inputInvite.value.trim();
            if (name && !mockFriends.some(f => f.name.toLowerCase() === name.toLowerCase())) {
                mockFriends.push({ name: name, promo: "Invité", avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=random` });
                inputInvite.value = '';
                renderList();
                alert(`Invitation envoyée à ${name} !`);
            } else if (!name) {
                alert("Veuillez entrer un nom ou un email.");
            } else {
                alert(`${name} est déjà dans votre liste d'amis.`);
            }
        });

        renderList();
    }

    initComposer() {
        const composerOverlay = this.uiManager.composerOverlay;
        if (!composerOverlay) return;

        const open = () => this.uiManager.showComposer();
        const close = () => this.uiManager.hideComposer();

        const btnNewPost = document.getElementById('btn-newpost');
        if (btnNewPost) {
            btnNewPost.addEventListener('click', open);
        }

        const openButtons = document.querySelectorAll('.btn-open-composer');
        if (openButtons.length) {
            openButtons.forEach(btn => btn.addEventListener('click', open));
        }

        const btnComposerCancel = document.getElementById('btn-composer-cancel');
        if (btnComposerCancel) {
            btnComposerCancel.addEventListener('click', close);
        }

        const publishBtn = document.querySelector('.composer-publish');
        const draftBtn = document.querySelector('.draft-btn');
        const cancelBottomBtn = document.querySelector('.cancel-btn');
        const successEl = document.querySelector('.composer-success');

        const publish = () => {
            if (successEl) {
                successEl.textContent = "Success! Your post has been scheduled and will publish at 7:02 AM on 04/08/2026.";
            }
            close();
        };

        const saveDraft = () => {
            if (successEl) {
                successEl.textContent = "Draft saved! AutoSave keeps updating your draft.";
            }
            close();
        };

        if (publishBtn) publishBtn.addEventListener('click', (e) => { e.preventDefault(); publish(); });
        if (draftBtn) draftBtn.addEventListener('click', (e) => { e.preventDefault(); saveDraft(); });
        if (cancelBottomBtn) cancelBottomBtn.addEventListener('click', (e) => { e.preventDefault(); close(); });

        // Close when clicking the dimmed area
        composerOverlay.addEventListener('click', (e) => {
            if (e.target === composerOverlay) close();
        });

        document.addEventListener('keydown', (e) => {
            const isOpen = composerOverlay.style.display !== 'none';
            if (!isOpen) return;

            if (e.key === 'Escape') {
                close();
                return;
            }

            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                publish();
                return;
            }

            // Ctrl+S (save draft)
            if (e.ctrlKey && (e.key === 's' || e.key === 'S')) {
                e.preventDefault();
                saveDraft();
            }
        });
    }

    initProfileTabs() {
        const tabsContainer = document.querySelector('.profile-tabs');
        if (!tabsContainer) return;
    
        // Utiliser un seul écouteur sur le conteneur des onglets pour la performance
        tabsContainer.addEventListener('click', (e) => {
            const tab = e.target.closest('.profile-tab');
            if (!tab) return;
    
            // Désactiver tous les onglets
            tabsContainer.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
            // Activer l'onglet cliqué
            tab.classList.add('active');
    
            const panelKey = tab.getAttribute('data-profile-tab');
            
            // Cacher tous les panneaux
            document.querySelectorAll('.profile-panel[data-profile-panel]').forEach(p => {
                p.style.display = 'none';
            });
            // Afficher le panneau cible
            const targetPanel = document.querySelector(`.profile-panel[data-profile-panel="${panelKey}"]`);
            if (targetPanel) {
                targetPanel.style.display = 'block';
            }
        });
    }

    initAuthTabs() {
        const tabs = document.querySelectorAll('.auth-tab');
        if (!tabs.length) return;

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const formName = tab.getAttribute('data-auth-form');
                document.querySelectorAll('.auth-form').forEach(form => {
                    const name = form.getAttribute('data-auth-form');
                    form.style.display = name === formName ? 'block' : 'none';
                });
            });
        });
    }

    initInteractiveButtons() {
        const btnEditProfile = document.getElementById('btn-edit-profile');
        if (btnEditProfile) {
            btnEditProfile.addEventListener('click', () => {
                const newBio = prompt('Modifier la bio du profil:', document.getElementById('profile-bio')?.textContent || '');
                if (!newBio) return;
                const bioEl = document.getElementById('profile-bio');
                if (bioEl) bioEl.textContent = newBio;
            });
        }

        const btnMessageProfile = document.getElementById('btn-message-profile');
        if (btnMessageProfile) {
            btnMessageProfile.addEventListener('click', () => {
                const profileName = document.getElementById('profile-name')?.textContent || 'Contact';
                this.goToMessagesView(profileName);
            });
        }

        const btnSendMessageSide = document.getElementById('btn-send-message-side');
        if (btnSendMessageSide) {
            btnSendMessageSide.addEventListener('click', () => {
                const profileName = document.getElementById('profile-name')?.textContent || 'Contact';
                this.goToMessagesView(profileName);
            });
        }

        const btnAddFriend = document.getElementById('btn-add-friend');
        if (btnAddFriend) {
            btnAddFriend.addEventListener('click', () => {
                window.location.href = 'friends.html';
            });
        }

        const btnReportUser = document.getElementById('btn-report-user');
        if (btnReportUser) {
            btnReportUser.addEventListener('click', () => {
                alert('Merci, le signalement a ete envoye au support moderation.');
            });
        }

        const btnTagPost = document.getElementById('btn-tag-post');
        if (btnTagPost) {
            btnTagPost.addEventListener('click', () => {
                if (this.uiManager.composerOverlay) {
                    this.uiManager.showComposer();
                    const tagInput = document.querySelector('.composer-section input.section-input');
                    if (tagInput) tagInput.focus();
                }
            });
        }

        const btnSendMessage = document.getElementById('btn-send-message');
        if (btnSendMessage) {
            btnSendMessage.addEventListener('click', () => this.sendMessageFromInput());
        }
        const chatInput = document.getElementById('chat-input');
        if (chatInput) {
            chatInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.sendMessageFromInput();
                }
            });
        }

        const btnNewDiscussion = document.getElementById('btn-new-discussion');
        if (btnNewDiscussion) {
            btnNewDiscussion.addEventListener('click', () => {
                const name = prompt('Nom du contact pour la nouvelle discussion:');
                if (!name) return;
                this.goToMessagesView(name);
            });
        }
    }

    goToMessagesView(contactName) {
        window.location.href = `messages.html?contact=${encodeURIComponent(contactName)}`;
    }

    sendMessageFromInput() {
        const chatInput = document.getElementById('chat-input');
        const chatHistory = document.getElementById('chat-history');
        const text = chatInput?.value?.trim();
        if (!text || !chatHistory) return;
        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble sender';
        bubble.textContent = text;
        chatHistory.appendChild(bubble);
        chatInput.value = '';
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    initLangSwitcher() {
        const btns = document.querySelectorAll('#btn-lang-switcher, #btn-lang-switcher-auth');
        if (!btns.length) return;
    
        const updateButtonText = (lang) => {
            const btnText = lang.toUpperCase();
            btns.forEach(b => {
                if (b) b.textContent = btnText; // On met à jour seulement le texte, sans icône
            });
        };
    
        // Mettre à jour le texte du bouton au chargement de la page
        updateButtonText(this.translationService.currentLang);
    
        // Ajouter l'écouteur d'événement pour le clic
        btns.forEach(btn => {
            btn.addEventListener('click', () => {
                const newLang = this.translationService.currentLang === 'fr' ? 'en' : 'fr';
                this.translationService.setLanguage(newLang); // Le service sauvegarde la langue et traduit
                updateButtonText(newLang); // On met à jour le texte du bouton
            });
        });
    
        // Traduire la page au chargement initial avec la langue récupérée
        this.translationService.translatePage();
    }

    initChatbot() {
        // La logique du Chatbot reste ici pour le moment
    }
}

// Lancement de l'app une fois le DOM chargé
document.addEventListener('DOMContentLoaded', () => new CampusConnectApp());