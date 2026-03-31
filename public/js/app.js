import { UIManager } from './UIManager.js';
import { ApiService } from './ApiService.js';
import { TranslationService } from './TranslationService.js';
import { PostRenderer } from './PostRenderer.js';
import { ProfileRenderer } from './ProfileRenderer.js';

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
        this.selectedContactId = null;
        this.selectedContactMeta = null;
        this.messagesPollerId = null;

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

        // Active l'UI auth uniquement sur la page qui la contient.
        if (document.getElementById('view-auth')) {
            this.uiManager.showAuth();
        }
    }

    initNavigation() {
        this.uiManager.navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const targetId = e.currentTarget.getAttribute('data-target');
                if (!targetId) {
                    return;
                }

                e.preventDefault();
                const linkId = e.currentTarget.id;
                
                this.uiManager.activateNavLink(linkId);
                this.uiManager.showView(targetId);

                // Si on clique sur le lien du profil, on affiche le profil de l'utilisateur connecté
                if (targetId === 'view-profile') {
                    this.renderProfile('Mon profil', true);
                }
            });
        });

        const profileIcon = document.querySelector('.profile-icon');
        if (profileIcon) {
            profileIcon.addEventListener('click', () => {
                document.getElementById('link-profile')?.click();
            });
        }
    }

    initAuth() {
        const logoutLink = document.querySelector('.logout');

        if (logoutLink) logoutLink.addEventListener('click', (e) => {
            e.preventDefault();
            this.uiManager.showAuth();
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
                    this.renderProfile(authorName, false);
                    this.uiManager.showView('view-profile');
                    this.uiManager.activateNavLink('link-profile');
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

            this.renderProfile('Mon profil', true);
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
    resolveMediaUrl(path) {
        if (!path) return '';
        if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
            return path;
        }
        return `../${path}`;
    }

    async loadConversation(scrollToBottom = false) {
        const chatHistory = document.getElementById('chat-history');
        const chatHeader = document.getElementById('chat-header');
        if (!chatHistory || !this.selectedContactId) return;

        const messages = await this.apiService.getMessages(this.selectedContactId);
        const myId = Number(window.CAMPUSCONNECT_ME?.id_user || 0);

        if (this.selectedContactMeta && chatHeader) {
            chatHeader.innerHTML = `<h3><img src="${this.selectedContactMeta.avatar}" class="avatar-small" style="vertical-align: middle; margin-right: 10px;"> ${this.selectedContactMeta.name}</h3>`;
        }

        chatHistory.innerHTML = messages.map((m) => {
            const cssClass = Number(m.id_sender) === myId ? 'sender' : 'receiver';
            const mediaHtml = m.fichiers
                ? `<div style="margin-top:8px;"><img src="${this.resolveMediaUrl(m.fichiers)}" alt="Image message" style="max-width:220px;border-radius:10px;border:1px solid var(--border-color);"></div>`
                : '';
            return `<div class="chat-bubble ${cssClass}">${m.contenu || ''}${mediaHtml}</div>`;
        }).join('') || '<div class="muted small">Aucun message.</div>';

        if (scrollToBottom) {
            chatHistory.scrollTop = chatHistory.scrollHeight;
        }
    }

    startMessagesPolling() {
        if (this.messagesPollerId) {
            clearInterval(this.messagesPollerId);
        }

        this.messagesPollerId = setInterval(() => {
            this.loadConversation(false);
        }, 3000);
    }

    async renderMessages() {
        const contactsContainer = document.getElementById('contacts-list');
        const urlContactId = Number(new URLSearchParams(window.location.search).get('id_contact') || 0);

        if (contactsContainer) {
            const contacts = await this.apiService.getContacts();

            if (urlContactId > 0 && !contacts.some(c => Number(c.id) === urlContactId)) {
                const missingContact = await this.apiService.getUserById(urlContactId);
                if (missingContact) {
                    contacts.unshift(missingContact);
                }
            }

            contactsContainer.innerHTML = '';
            contacts.forEach(contact => {
                const li = document.createElement('li');
                li.className = 'contact-item';
                li.dataset.contactId = String(contact.id);
                li.innerHTML = `
                    <img src="${contact.avatar}" alt="${contact.name}" class="avatar-small">
                    <div class="contact-info">
                        <h4>${contact.name}</h4>
                        <p>${contact.lastMessage}</p>
                    </div>
                `;
                li.addEventListener('click', async () => {
                    this.selectedContactId = contact.id;
                    this.selectedContactMeta = contact;
                    await this.loadConversation(true);
                    this.startMessagesPolling();
                });
                contactsContainer.appendChild(li);
            });

            if (contacts.length) {
                const byUrl = urlContactId ? contacts.find(c => Number(c.id) === urlContactId) : null;
                const first = byUrl
                    ? contactsContainer.querySelector(`[data-contact-id="${byUrl.id}"]`)
                    : contactsContainer.firstElementChild;
                if (first) {
                    first.click();
                }
            } else {
                const chatHistory = document.getElementById('chat-history');
                const chatHeader = document.getElementById('chat-header');
                if (chatHeader) {
                    chatHeader.textContent = 'Aucune conversation';
                }
                if (chatHistory) {
                    chatHistory.innerHTML = '<div class="muted small">Aucun contact pour le moment.</div>';
                }
            }
        }
    }

    // Rendu des données Mockées de la section Amis
    async renderFriends() {
        const friendsContainer = document.getElementById('friends-list-container');
        const friendsCount = document.getElementById('friends-count');
        const btnInvite = document.getElementById('btn-invite-friend');
        const inputInvite = document.getElementById('invite-friend-input');

        if (!friendsContainer || !btnInvite || !inputInvite) return;

        const friends = await this.apiService.getFriends();

        const renderList = () => {
            friendsContainer.innerHTML = '';
            friends.forEach(friend => {
                const friendHTML = `
                    <li class="friend-item" data-friend-id="${friend.id || ''}">
                        <img src="${friend.avatar}" alt="${friend.name}" class="avatar-small">
                        <div class="friend-item-info">
                            <h4>${friend.name}</h4>
                            <p class="muted small">${friend.promo}</p>
                        </div>
                        <div class="friend-item-actions">
                            <button class="btn-outline-sm btn-friend-message" type="button" style="padding: 8px 12px; font-size: 0.8rem;">Message</button>
                            <button class="btn-danger-full btn-friend-remove" type="button" style="padding: 8px 12px; font-size: 0.8rem; width: auto; margin-top: 0;">Retirer</button>
                        </div>
                    </li>
                `;
                friendsContainer.insertAdjacentHTML('beforeend', friendHTML);
            });
            if(friendsCount) friendsCount.textContent = friends.length;
        };

        friendsContainer.addEventListener('click', (e) => {
            const messageBtn = e.target.closest('.btn-friend-message');
            const removeBtn = e.target.closest('.btn-friend-remove');
            const friendItem = e.target.closest('.friend-item');
            if (!friendItem) return;

            const friendId = Number(friendItem.dataset.friendId || 0);
            const friendName = friendItem.querySelector('h4')?.textContent || 'contact';

            if (messageBtn) {
                if (friendId > 0) {
                    window.location.href = `messages.php?id_contact=${friendId}`;
                } else {
                    alert(`Impossible d'ouvrir la conversation de ${friendName} (id manquant).`);
                }
                return;
            }

            if (removeBtn) {
                const idx = friends.findIndex((f) => Number(f.id || 0) === friendId && f.name === friendName);
                if (idx >= 0) {
                    friends.splice(idx, 1);
                    renderList();
                } else {
                    friendItem.remove();
                    if (friendsCount) {
                        const newCount = Math.max(0, Number(friendsCount.textContent || '0') - 1);
                        friendsCount.textContent = String(newCount);
                    }
                }
            }
        });

        btnInvite.addEventListener('click', () => {
            const name = inputInvite.value.trim();
            if (name && !friends.some(f => f.name.toLowerCase() === name.toLowerCase())) {
                friends.push({ id: 0, name: name, promo: "Invité", avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=random` });
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

        const open = () => {
            // Copier le contenu de la textarea rapide vers le modal
            const quickTextarea = document.getElementById('quick-post-textarea');
            const composerTextarea = document.getElementById('composer-textarea');
            if (quickTextarea && composerTextarea && quickTextarea.value.trim()) {
                composerTextarea.value = quickTextarea.value;
            }
            this.uiManager.showComposer();
        };
        
        const close = () => {
            document.getElementById('composer-textarea').value = '';
            document.getElementById('composer-image-input').value = '';
            document.getElementById('composer-attachments').innerHTML = '';
            document.getElementById('composer-success').style.display = 'none';
            this.uiManager.hideComposer();
        };

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

        // Gestion du bouton image
        const btnComposerImage = document.getElementById('btn-composer-image');
        const imageInput = document.getElementById('composer-image-input');
        if (btnComposerImage && imageInput) {
            btnComposerImage.addEventListener('click', () => imageInput.click());
            imageInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const preview = document.createElement('img');
                    preview.src = URL.createObjectURL(file);
                    preview.style.maxWidth = '200px';
                    preview.style.marginTop = '10px';
                    preview.style.borderRadius = '8px';
                    const attachDiv = document.getElementById('composer-attachments');
                    attachDiv.innerHTML = '';
                    attachDiv.appendChild(preview);
                }
            });
        }

        const publishBtn = document.querySelector('.composer-publish');
        const draftBtn = document.querySelector('.cancel-btn');
        const cancelBtn = document.getElementById('btn-composer-cancel');
        const successEl = document.getElementById('composer-success');
        const textarea = document.getElementById('composer-textarea');

        const publish = async () => {
            const contenu = textarea.value.trim();
            console.log('[COMPOSER] Publish clicked. Contenu:', contenu);
            
            if (!contenu) {
                alert('Veuillez écrire quelque chose !');
                return;
            }

            const imageFile = imageInput.files[0];
            console.log('[COMPOSER] Image file:', imageFile);
            
            try {
                console.log('[COMPOSER] Appel API createPost...');
                const result = await this.apiService.createPost(contenu, imageFile);
                console.log('[COMPOSER] Réponse API:', result);
                
                if (result && result.status === 'success') {
                    successEl.textContent = '✓ Post publié avec succès !';
                    successEl.style.display = 'block';
                    successEl.style.color = 'green';
                    
                    // Réinitialiser le formulaire rapide aussi
                    const quickTextarea = document.getElementById('quick-post-textarea');
                    if (quickTextarea) {
                        quickTextarea.value = '';
                    }
                    
                    // Recharger le feed après 1 seconde
                    setTimeout(() => {
                        close();
                        this.initFeed();
                    }, 1000);
                } else {
                    const errMsg = result?.message || 'Erreur inconnue';
                    console.error('[COMPOSER] Erreur API:', errMsg);
                    successEl.textContent = '✗ Erreur : ' + errMsg;
                    successEl.style.display = 'block';
                    successEl.style.color = 'red';
                }
            } catch (error) {
                console.error('[COMPOSER] Erreur lors de la publication :', error);
                successEl.textContent = '✗ Erreur réseau: ' + error.message;
                successEl.style.display = 'block';
                successEl.style.color = 'red';
            }
        };

        if (publishBtn) publishBtn.addEventListener('click', (e) => { 
            e.preventDefault(); 
            publish(); 
        });
        
        if (draftBtn) draftBtn.addEventListener('click', (e) => { 
            e.preventDefault(); 
            close(); 
        });

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
            btnMessageProfile.addEventListener('click', () => this.goToMessagesView('Leila Martinez'));
        }

        const btnSendMessageSide = document.getElementById('btn-send-message-side');
        if (btnSendMessageSide) {
            btnSendMessageSide.addEventListener('click', () => this.goToMessagesView('Leila Martinez'));
        }

        const btnAddFriend = document.getElementById('btn-add-friend');
        if (btnAddFriend) {
            btnAddFriend.addEventListener('click', () => {
                const friendsLink = document.getElementById('link-friends');
                if (friendsLink) {
                    friendsLink.click();
                }
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
                const contactsList = document.getElementById('contacts-list');
                const name = prompt('Nom du contact pour la nouvelle discussion:');
                if (!name || !contactsList) return;
                const li = document.createElement('li');
                li.className = 'contact-item';
                li.innerHTML = `
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=2563eb&color=fff" alt="${name}" class="avatar-small">
                    <div class="contact-info">
                        <h4>${name}</h4>
                        <p>Nouvelle discussion</p>
                    </div>
                `;
                contactsList.prepend(li);
                this.goToMessagesView(name);
            });
        }
    }

    goToMessagesView(contactName) {
        this.uiManager.showApp();
        const msgLink = document.getElementById('link-messages');
        if (msgLink) msgLink.click();
        const chatHeader = document.getElementById('chat-header');
        if (chatHeader) {
            chatHeader.innerHTML = `<h3>${contactName}</h3>`;
        }
    }

    sendMessageFromInput() {
        const chatInput = document.getElementById('chat-input');
        const chatHistory = document.getElementById('chat-history');
        const imageInput = document.getElementById('chat-image-input');
        const text = chatInput?.value?.trim();
        const imageFile = imageInput?.files?.[0] || null;

        if ((!text && !imageFile) || !chatHistory || !this.selectedContactId) return;

        this.apiService.sendMessage(this.selectedContactId, text || '', imageFile).then(() => {
            if (chatInput) chatInput.value = '';
            if (imageInput) imageInput.value = '';
            this.loadConversation(true);
        }).catch(() => {
            alert('Impossible d\'envoyer le message pour le moment.');
        });
    }

    initLangSwitcher() {
        const btns = document.querySelectorAll('#btn-lang-switcher, #btn-lang-switcher-auth');
        if (!btns.length) return;

        btns.forEach(btn => {
            if (btn) {
                btn.addEventListener('click', () => {
                    const newLang = this.translationService.currentLang === 'fr' ? 'en' : 'fr';
                    this.translationService.setLanguage(newLang);
                    const btnText = newLang.toUpperCase();
                    btns.forEach(b => {
                        if(b) b.innerHTML = `<i class="fa-solid fa-language"></i> ${btnText}`;
                    });
                });
            }
        });
        // Appliquer la langue initiale au chargement
        this.translationService.translatePage();
    }

    initChatbot() {
        // La logique du Chatbot reste ici pour le moment
    }
}

// Lancement de l'app une fois le DOM chargé
document.addEventListener('DOMContentLoaded', () => new CampusConnectApp());