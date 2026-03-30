/**
 * Un "renderer" sans état pour créer le HTML d'une publication.
 */
export class PostRenderer {
    static render(post) {
        return `
            <article class="card post" data-id="${post.id}">
                <div class="post-header">
                    <div class="post-author-info" data-author-name="${post.authorName}" style="cursor: pointer; display: flex; align-items: flex-start; gap: 12px; flex: 1;">
                        <img src="${post.authorAvatar}" alt="${post.authorName}" class="avatar-small">
                        <div class="post-meta">
                            <div class="post-author-row">
                                <h3 class="post-author-name">${post.authorName}</h3>
                                <span class="badge-promo">${post.authorPromo}</span>
                            </div>
                            <div class="post-author-sub muted small">${post.authorExtra ?? ''}</div>
                        </div>
                    </div>
                    <div class="post-time muted small">${post.timeAgo}</div>
                </div>
                <div class="post-body">
                    <p>${post.content}</p>
                    ${post.image ? `<img src="${post.image}" alt="Post image" class="post-image">` : ''}
                </div>
                <div class="post-footer">
                    <div class="post-stats muted small">
                        <span class="post-stat"><i class="fa-regular fa-heart"></i> ${post.likes}</span>
                        <span class="post-stat"><i class="fa-regular fa-comment"></i> ${post.comments}</span>
                        <span class="post-stat"><i class="fa-solid fa-share"></i> ${post.shares ?? 0}</span>
                    </div>
                    <button class="post-save" type="button"><i class="fa-regular fa-bookmark"></i> Save</button>
                </div>
            </article>
        `;
    }
}