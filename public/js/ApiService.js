import { mockPosts, mockUsers, mockContacts, mockFriends } from './data.js';

/**
 * Simule la récupération de données depuis un backend.
 * Dans une application réelle, cette classe utiliserait fetch() pour faire des requêtes réseau.
 */
export class ApiService {
    timeAgoFrom(dateValue) {
        if (!dateValue) return 'A l\'instant';
        const date = new Date(dateValue.replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) return 'A l\'instant';

        const diffSec = Math.max(0, Math.floor((Date.now() - date.getTime()) / 1000));
        if (diffSec < 60) return 'A l\'instant';
        if (diffSec < 3600) return `${Math.floor(diffSec / 60)} min`;
        if (diffSec < 86400) return `${Math.floor(diffSec / 3600)} h`;
        return `${Math.floor(diffSec / 86400)} j`;
    }

    mapPost(apiPost) {
        return {
            id: apiPost.id_post,
            authorName: apiPost.auteur_nom || 'Etudiant',
            authorPromo: 'CampusConnect',
            authorExtra: '',
            authorAvatar: apiPost.auteur_avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(apiPost.auteur_nom || 'Etudiant')}&background=2563eb&color=fff`,
            timeAgo: this.timeAgoFrom(apiPost.date_post),
            content: apiPost.contenu || '',
            image: apiPost.image || null,
            likes: Number(apiPost.total_likes || 0),
            comments: Number(apiPost.total_comments || 0),
            shares: 0,
        };
    }

    async getPosts() {
        try {
            const res = await fetch('../Backend/public/index.php?route=posts', {
                credentials: 'include'
            });
            if (!res.ok) throw new Error();
            const json = await res.json();
            if (json.data && json.data.length) {
                return json.data.map(post => this.mapPost(post));
            }
            return mockPosts;
        } catch (e) {
            console.warn("API indisponible, chargement des posts mockés", e);
            return mockPosts;
        }
    }

    async getUser(userName) {
        try {
            const res = await fetch('../Backend/public/index.php?route=profile', {
                credentials: 'include'
            });
            if (res.ok) {
                const json = await res.json();
                if (json.status === 'success' && json.data) {
                    const p = json.data;
                    return {
                        name: p.nom || userName || 'Etudiant',
                        bio: p.bio || 'Etudiant CampusConnect',
                        email: p.email || '',
                        location: `${p.campus || 'Campus'}${p.formation ? ' · ' + p.formation : ''}`,
                        avatar: p.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(p.nom || 'Etudiant')}&background=2563eb&color=fff`,
                        stats: { competences: '0', followers: '0', following: '0', groups: '0' },
                        techSkills: [],
                        transSkills: [],
                        deanList: { title: 'Profil CampusConnect', subtitle: p.annee_etude || '' },
                        projects: [],
                        links: [],
                    };
                }
            }
        } catch (e) {}
        
        const user = mockUsers[userName] || mockUsers[Object.keys(mockUsers).find(k => k.toLowerCase().includes(userName.toLowerCase().split(' ')[0]))];
        return user;
    }

    getUsers() {
        return Promise.resolve(mockUsers);
    }
    
    async getContacts() {
        try {
            const res = await fetch('../Backend/public/index.php?route=contacts', {
                credentials: 'include'
            });
            if (res.ok) {
                const json = await res.json();
                if (json.status === 'success' && Array.isArray(json.data)) {
                    return json.data.map(c => ({
                        id: Number(c.id_user),
                        name: c.nom,
                        avatar: c.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(c.nom || 'Etudiant')}&background=random`,
                        lastMessage: c.last_message || 'Aucun message',
                    }));
                }
            }
        } catch(e) {}
        return [];
    }

    async getUserById(idUser) {
        try {
            const res = await fetch(`../Backend/public/index.php?route=user&id_user=${encodeURIComponent(idUser)}`, {
                credentials: 'include'
            });
            if (!res.ok) throw new Error();

            const json = await res.json();
            if (json.status === 'success' && json.data) {
                const u = json.data;
                return {
                    id: Number(u.id_user),
                    name: u.nom,
                    avatar: u.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(u.nom || 'Etudiant')}&background=random`,
                    lastMessage: 'Nouvelle discussion',
                };
            }
        } catch (e) {}

        return null;
    }

    async getMessages(contactId) {
        try {
            const res = await fetch(`../Backend/public/index.php?route=messages&id_contact=${encodeURIComponent(contactId)}&_t=${Date.now()}`, {
                credentials: 'include'
            });
            if (!res.ok) throw new Error();

            const json = await res.json();
            if (json.status === 'success' && Array.isArray(json.data)) {
                return json.data;
            }
        } catch (e) {
            console.warn('Impossible de charger les messages', e);
        }

        return [];
    }

    async sendMessage(idReceiver, contenu, imageFile = null) {
        let res;
        if (imageFile) {
            const formData = new FormData();
            formData.append('id_receiver', String(idReceiver));
            formData.append('contenu', contenu || '');
            formData.append('image', imageFile);

            res = await fetch('../Backend/public/index.php?route=send_message', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });
        } else {
            res = await fetch('../Backend/public/index.php?route=send_message', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_receiver: idReceiver, contenu }),
                credentials: 'include'
            });
        }

        const json = await res.json();
        if (!res.ok || json.status !== 'success') {
            throw new Error(json.message || 'Echec envoi message');
        }

        return json;
    }

    async getFriends() {
        try {
            const res = await fetch('../Backend/public/index.php?route=friends', {
                credentials: 'include'
            });
            if (res.ok) {
                const json = await res.json();
                if (json.status === 'success' && Array.isArray(json.data)) {
                    return json.data.map(f => ({
                        id: Number(f.id_user),
                        name: f.nom,
                        promo: `${f.annee_etude || ''} ${f.formation || ''}`.trim() || 'CampusConnect',
                        avatar: f.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(f.nom || 'Etudiant')}&background=1e293b&color=fff`,
                    }));
                }
            }
        } catch (e) {}

        return mockFriends;
    }

    async createPost(contenu, imageFile = null) {
        try {
            console.log('[API] createPost - Contenu:', contenu, 'Image:', imageFile);
            
            const formData = new FormData();
            formData.append('contenu', contenu);
            if (imageFile) {
                formData.append('image', imageFile);
            }

            const url = '../Backend/public/index.php?route=create_post';
            console.log('[API] Envoi POST vers:', url);
            
            const res = await fetch(url, {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });

            console.log('[API] Status HTTP:', res.status, res.statusText);
            
            const json = await res.json();
            console.log('[API] Réponse JSON:', json);
            
            return json;
        } catch (e) {
            console.error('[API] Erreur lors de la création du post:', e);
            return { status: 'error', message: 'Erreur réseau: ' + e.message };
        }
    }
}