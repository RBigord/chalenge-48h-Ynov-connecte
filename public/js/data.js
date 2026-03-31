export const translations = {
    'fr': {
        'nav_home': 'Accueil',
        'nav_messages': 'Messagerie',
        'nav_profile': 'Profil',
        'nav_friends': 'Amis',
        'nav_ymatch': 'Ymatch',
        'nav_logout': 'Déconnexion',
        'profile_competences': 'Compétences',
        'profile_followers': 'Abonnés',
        'profile_following': 'Abonnements',
        'profile_groups': 'Groupes',
        'profile_tab_competences': 'Compétences',
        'profile_tab_about': 'À propos',
        'profile_tab_activities': 'Activités',
        'profile_tab_photos': 'Photos & Média',
        'friends_manage': 'Gérer les amis',
        'auth_welcome_title': 'Bienvenue sur votre hub de campus',
        'auth_welcome_subtitle': 'Connectez-vous ou créez un compte pour rejoindre le réseau de votre université : événements, groupes et ressources vérifiées du campus - le tout protégé par une sécurité de niveau entreprise.',
        'auth_card_title': 'Content de vous revoir',
        'auth_card_subtitle': 'Connectez-vous ou créez un compte pour continuer sur CampusConnect',
        'auth_tab_login': 'Connexion',
        'auth_tab_signup': 'Inscription',
        'auth_signin_button': 'Se connecter',
        'auth_create_button': 'Créer un compte',
        'nav_settings': 'Réglages',
        'settings_title': 'Réglages du compte'
    },
    'en': {
        'nav_home': 'Home',
        'nav_messages': 'Messages',
        'nav_profile': 'Profile',
        'nav_friends': 'Friends',
        'nav_ymatch': 'Ymatch',
        'nav_logout': 'Logout',
        'profile_competences': 'Skills',
        'profile_followers': 'Followers',
        'profile_following': 'Following',
        'profile_groups': 'Groups',
        'profile_tab_competences': 'Skills',
        'profile_tab_about': 'About',
        'profile_tab_activities': 'Activities',
        'profile_tab_photos': 'Photos & Media',
        'friends_manage': 'Manage Friends',
        'auth_welcome_title': 'Welcome to your campus hub',
        'auth_welcome_subtitle': 'Sign in or create an account to join your university network: events, groups, and verified campus resources — all protected with enterprise-grade security.',
        'auth_card_title': 'Welcome back',
        'auth_card_subtitle': 'Sign in or create an account to continue to CampusConnect',
        'auth_tab_login': 'Login',
        'auth_tab_signup': 'Sign-up',
        'auth_signin_button': 'Sign in',
        'auth_create_button': 'Create account',
        'nav_settings': 'Settings',
        'settings_title': 'Account Settings'
    }
};

export const mockUsers = {
    "Leila Martinez": {
        name: "Leila Martinez",
        bio: "B.S. Computer Science, Class of 2025 · Software Engineering Major",
        email: "leila.martinez@campus.univ.edu",
        location: "Oakwood Campus · San Mateo, CA",
        avatar: "https://ui-avatars.com/api/?name=Leila+Martinez&background=2563eb&color=fff",
        stats: { competences: "12", followers: "3,842", following: "412", groups: "9" },
        techSkills: [
            { label: "React & TypeScript", level: "Expert", percent: 92 },
            { label: "Node.js & Express", level: "Avancé", percent: 78 },
            { label: "Python & Machine Learning", level: "Avancé", percent: 74 },
            { label: "SQL & Database Design", level: "Intermédiaire", percent: 56 },
            { label: "UI/UX Design (Figma)", level: "Intermédiaire", percent: 60 }
        ],
        transSkills: ["Leadership d'équipe", "Communication", "Gestion de projet", "Résolution de problèmes", "Pensée critique"],
        deanList: { title: "Dean's List - Computer Science", subtitle: "5 semesters consecutifs" },
        projects: [
            { title: "StudySync - Application de gestion d'études", desc: "Application web React permettant aux étudiants de suivre leurs groupes d'étude et deadlines.", tags: ["React", "TypeScript", "Firebase"] },
            { title: "ML Price Predictor", desc: "Modèle de machine learning pour prédire les prix immobiliers. Précision de 92%.", tags: ["Python", "Scikit-learn", "Pandas"] }
        ],
        links: [
            { label: "GitHub", url: "github.com/leilamartinez", icon: "fa-brands fa-github" },
            { label: "LinkedIn", url: "linkedin.com/in/leilamartinez", icon: "fa-brands fa-linkedin" },
            { label: "Portfolio Personnel", url: "leilamartinez.dev", icon: "fa-solid fa-globe" }
        ]
    },
    "Sofia Martin": {
        name: "Sofia Martin",
        bio: "Student · CampusConnect · Computer Science",
        email: "sofia.martin@campus.univ.edu",
        location: "Oakwood Campus",
        avatar: "https://ui-avatars.com/api/?name=Sofia+Martin&background=0070f3&color=fff",
        stats: { competences: "8", followers: "1,204", following: "210", groups: "4" },
        techSkills: [{ label: "React", level: "Avancé", percent: 85 }, { label: "JavaScript", level: "Expert", percent: 95 }],
        transSkills: ["Travail d'équipe", "Adaptabilité"],
        deanList: { title: "Honor Roll", subtitle: "Spring 2024" },
        projects: [{ title: "Campus Cafe App", desc: "A small app for ordering coffee.", tags: ["React", "JavaScript"] }],
        links: [{ label: "GitHub", url: "github.com/sofiam", icon: "fa-brands fa-github" }]
    },
    "Emma Dubois": {
        name: "Emma Dubois",
        bio: "Design · Verified · B1 Design",
        email: "emma.dubois@campus.univ.edu",
        location: "Arts Campus",
        avatar: "https://ui-avatars.com/api/?name=Emma+Dubois&background=0ea5e9&color=fff",
        stats: { competences: "15", followers: "5,678", following: "301", groups: "12" },
        techSkills: [{ label: "Figma", level: "Expert", percent: 98 }, { label: "UI/UX", level: "Expert", percent: 95 }],
        transSkills: ["Créativité", "Souci du détail"],
        deanList: { title: "Design Excellence Award", subtitle: "2023" },
        projects: [{ title: "48h Challenge Wireframes", desc: "Complete UI/UX for the social media challenge.", tags: ["Figma", "UI", "UX"] }],
        links: [{ label: "Behance", url: "behance.net/emmadubois", icon: "fa-brands fa-behance" }]
    }
};

export const mockPosts = [
    {
        id: 1,
        authorName: "Sofia Martin",
        authorPromo: "Student · CampusConnect",
        authorExtra: "Computer Science",
        authorAvatar: "https://ui-avatars.com/api/?name=Sofia+Martin&background=0070f3&color=fff",
        timeAgo: "2h ago",
        content: "Anyone down to train React skills tonight at the campus café? 💻☕️",
        image: "https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=1200&q=80",
        likes: 26,
        comments: 4,
        shares: 5
    },
    {
        id: 2,
        authorName: "Emma Dubois",
        authorPromo: "Design · Verified",
        authorExtra: "B1 Design",
        authorAvatar: "https://ui-avatars.com/api/?name=Emma+Dubois&background=0ea5e9&color=fff",
        timeAgo: "5h ago",
        content: "The 48h challenge wireframes are done. Now we ship the code 🔥",
        image: null,
        likes: 34,
        comments: 8,
        shares: 0
    },
    {
        id: 3,
        authorName: "Noah Patel",
        authorPromo: "Creative Club",
        authorExtra: "Campus Events",
        authorAvatar: "https://ui-avatars.com/api/?name=Noah+Patel&background=111827&color=fff",
        timeAgo: "1d ago",
        content: "Updated schedule for the campus gala. Volunteers welcome!",
        image: "https://images.unsplash.com/photo-1531058020387-3be3445568f5?auto=format&fit=crop&w=1200&q=80",
        likes: 78,
        comments: 11,
        shares: 14
    }
];

export const mockContacts = [
    { id: 1, name: "Lucas Martin", avatar: "https://ui-avatars.com/api/?name=Lucas+Martin&background=random", lastMessage: "Tu viens à la cafet ?" },
    { id: 2, name: "Emma Dubois", avatar: "https://ui-avatars.com/api/?name=Emma+Dubois&background=random", lastMessage: "On se capte pour le challenge !" }
];

export const mockFriends = [
    { name: "Marcus Green", promo: "B3 Dev", avatar: "https://ui-avatars.com/api/?name=Marcus+Green&background=1e293b&color=fff" },
    { name: "Aisha Khan", promo: "B2 Design", avatar: "https://ui-avatars.com/api/?name=Aisha+Khan&background=1e293b&color=fff" }
];