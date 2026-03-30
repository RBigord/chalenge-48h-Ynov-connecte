/**
 * Un "renderer" sans état pour créer le HTML de la vue de profil.
 */
export class ProfileRenderer {
    static render(profileData, isCurrentUser = false) {
        const editIconHTML = isCurrentUser ? '<i class="fa-solid fa-pen-to-square profile-edit-icon" aria-hidden="true"></i>' : '';

        const techRowsHTML = profileData.techSkills.map(s => {
            return `
                <div class="skill-tech-row">
                    <div class="skill-tech-top">
                        <div class="skill-tech-name">${s.label}</div>
                        <div class="skill-tech-level">${s.level}</div>
                    </div>
                    <div class="skill-track">
                        <div class="skill-fill" style="width:${s.percent}%"></div>
                    </div>
                    <button class="skill-delete-btn" title="Supprimer la compétence">&times;</button>
                </div>
            `;
        }).join('');

        const transChipsHTML = profileData.transSkills.map(t => `<span class="trans-chip">${t}</span>`).join('');

        const projectsHTML = profileData.projects.map(p => {
            const tags = p.tags.map(tag => `<span class="project-tag">${tag}</span>`).join('');
            return `
                <div class="project-card">
                    <div class="project-title-row">
                        <div class="project-title">${p.title}</div>
                    </div>
                    <div class="project-desc muted">${p.desc}</div>
                    <div class="project-tags">${tags}</div>
                </div>
            `;
        }).join('');

        const linksHTML = profileData.links.map(l => {
            return `
                <div class="link-row">
                    <i class="${l.icon} link-icon"></i>
                    <div class="link-text">
                        <div class="link-label">${l.label}</div>
                        <div class="link-url muted small">${l.url}</div>
                    </div>
                    <i class="fa-solid fa-arrow-right link-arrow"></i>
                </div>
            `;
        }).join('');

        return `
            <div class="profile-panel" data-profile-panel="competences">
                <div class="card profile-section-card">
                    <div class="profile-section-head">
                        <div class="profile-section-title">Compétences Techniques</div>
                        ${editIconHTML}
                    </div>
                    <div class="skill-tech-list">
                        ${techRowsHTML}
                    </div>
                    <button id="btn-add-skill" class="btn-outline-sm" style="margin-top: 15px; width: 100%;">Ajouter une compétence</button>
                </div>

                <div class="card profile-section-card">
                    <div class="profile-section-head">
                        <div class="profile-section-title">Compétences Transversales</div>
                        ${editIconHTML}
                    </div>
                    <div class="trans-chip-group">
                        ${transChipsHTML}
                    </div>
                </div>
            </div>

            <div class="profile-panel" data-profile-panel="about" style="display:none;">
                <div class="card profile-section-card">
                    <div class="profile-section-head">
                        <div class="profile-section-title">${profileData.deanList.title}</div>
                    </div>
                    <div class="muted small">${profileData.deanList.subtitle}</div>
                </div>

                <div class="card profile-section-card">
                    <div class="profile-section-head">
                        <div class="profile-section-title">Projets &amp; Portfolio</div>
                        ${editIconHTML}
                    </div>
                    <div class="projects-grid">
                        ${projectsHTML}
                    </div>
                </div>

                <div class="card profile-section-card">
                    <div class="profile-section-head">
                        <div class="profile-section-title">Liens Professionnels</div>
                        ${editIconHTML}
                    </div>
                    <div class="links-list">
                        ${linksHTML}
                    </div>
                </div>
            </div>

            <div class="profile-panel" data-profile-panel="activities" style="display:none;">
                <div class="card profile-section-card muted">Activities coming soon.</div>
            </div>

            <div class="profile-panel" data-profile-panel="photos" style="display:none;">
                <div class="card profile-section-card muted">Photos &amp; media coming soon.</div>
            </div>
        `;
    }
}