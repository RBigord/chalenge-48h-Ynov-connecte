import { mockPosts, mockUsers, mockContacts, mockFriends } from './data.js';

/**
 * Simule la récupération de données depuis un backend.
 * Dans une application réelle, cette classe utiliserait fetch() pour faire des requêtes réseau.
 */
export class ApiService {
    async getPosts() {
        // Simule une latence réseau
        return new Promise(resolve => setTimeout(() => resolve(mockPosts), 200));
    }

    async getUser(userName) {
        const user = mockUsers[userName] || mockUsers[Object.keys(mockUsers).find(k => k.toLowerCase().includes(userName.toLowerCase().split(' ')[0]))];
        return Promise.resolve(user);
    }

    getUsers() {
        return Promise.resolve(mockUsers);
    }
    
    getContacts() {
        return Promise.resolve(mockContacts);
    }

    getFriends() {
        return Promise.resolve(mockFriends);
    }
}