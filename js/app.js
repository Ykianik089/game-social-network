document.addEventListener('DOMContentLoaded', function() {
    createModalHTML();

    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.user-card').forEach(card => {
                const name = card.querySelector('h3').textContent.toLowerCase();
                card.style.display = name.includes(term) ? 'flex' : 'none';
            });
        });
    }

    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            tab.classList.add('active');
            const target = document.getElementById(tab.dataset.target);
            if (target) target.classList.add('active');
        });
    });
});

function createModalHTML() {
    const modalHTML = `
        <div class="modal-overlay" id="customModal">
            <div class="modal-window">
                <div class="modal-title">GameNetwork</div>
                <div class="modal-text" id="modalMessage">Текст уведомления</div>
                <div class="modal-buttons">
                    <button class="modal-btn modal-cancel" id="modalCancel">Отмена</button>
                    <button class="modal-btn modal-confirm" id="modalConfirm">ОК</button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function showConfirm(message, onConfirm) {
    const modal = document.getElementById('customModal');
    const msg = document.getElementById('modalMessage');
    const confirmBtn = document.getElementById('modalConfirm');
    const cancelBtn = document.getElementById('modalCancel');

    msg.textContent = message;
    modal.classList.add('open');

    const close = () => {
        modal.classList.remove('open');
        confirmBtn.onclick = null;
        cancelBtn.onclick = null;
    };

    confirmBtn.onclick = () => {
        close();
        onConfirm();
    };

    cancelBtn.onclick = close;
}

function toggleLike(postId) {
    fetch('api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ action: 'toggle_like', postId: postId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const post = document.querySelector(`.post[data-post-id="${postId}"]`);
            const btn = post.querySelector('.like-btn');
            const icon = btn.querySelector('.like-icon');
            
            if (data.liked) {
                icon.textContent = '❤️';
                icon.style.color = '#ef4444';
            } else {
                icon.textContent = '🤍';
                icon.style.color = '';
            }
        }
    });
}

function addComment(postId) {
    const post = document.querySelector(`.post[data-post-id="${postId}"]`);
    const input = post.querySelector('.comment-input');
    const content = input.value;

    if (!content.trim()) return;

    fetch('api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ action: 'add_comment', postId: postId, content: content })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const section = post.querySelector('.comments-section');
            const form = section.querySelector('.comment-form');
            
            const div = document.createElement('div');
            div.className = 'comment';
            div.innerHTML = `
                <img src="protected_image.php?file=${data.comment.avatar_url.split('/').pop()}" class="avatar-small">
                <div class="comment-content">
                    <strong>${data.comment.username}</strong>
                    <p>${data.comment.content}</p>
                </div>
                <button onclick="deleteComment(${data.comment.id}, this)" style="color: #ef4444; font-size: 12px; border:none; background:none; cursor:pointer;">✕</button>
            `;
            
            section.insertBefore(div, form);
            input.value = '';
        }
    });
}

function deleteComment(commentId, btnElement) {
    showConfirm('Вы действительно хотите удалить этот комментарий?', () => {
        fetch('api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ action: 'delete_comment', commentId: commentId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btnElement.closest('.comment').remove();
            }
        });
    });
}

function addFriend(userId) {
    fetch('api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ action: 'add_friend', userId: userId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) window.location.reload();
    });
}

function cancelRequest(userId) {
    showConfirm('Отменить заявку в друзья?', () => {
        fetch('api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ action: 'cancel_request', userId: userId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.reload();
        });
    });
}

function acceptFriend(userId) {
    fetch('api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ action: 'accept_friend', userId: userId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) window.location.reload();
    });
}

function removeFriend(userId) {
    showConfirm('Удалить пользователя из друзей?', () => {
        fetch('api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ action: 'remove_friend', userId: userId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.reload();
        });
    });
}